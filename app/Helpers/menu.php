<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

// -------- URL helpers ----------
// Get normalized path for any URL
if (!function_exists('_path_of')) {
    function _path_of(?string $url): string
    {
        if (!$url) return '/';
        $path = parse_url($url, PHP_URL_PATH) ?? '/';
        return rtrim($path, '/') ?: '/';
    }
}

// Get normalized dashboard path once
if (!function_exists('_dashboard_path')) {
    function _dashboard_path(): string
    {
        try {
            return _path_of(route('voyager.dashboard'));
        } catch (\Throwable $e) {
            // Fallback if route not bound for any reason
            return '/admin';
        }
    }
}

/**
 * Boundary-safe active check for LEAVES.
 * - Dashboard ('/admin') and root ('/') are active only on EXACT match.
 * - Others: exact match OR child path (itemPath + '/...').
 */
if (!function_exists('_leaf_active')) {
    function _leaf_active(string $itemUrl, string $currentPath): bool
    {
        $itemPath = _path_of($itemUrl);
        $dashPath = _dashboard_path();

        // Root & dashboard must be exact
        if ($itemPath === '/' || $itemPath === $dashPath) {
            return $currentPath === $itemPath;
        }

        // Normal pages: exact OR boundary child
        if ($currentPath === $itemPath) return true;
        return str_starts_with($currentPath, $itemPath . '/');
    }
}

/**
 * Infer a "browse_{table}" permission key from a menu item when permission_id is NULL.
 * - Prefer Voyager routes: voyager.{table}.index  → browse_{table}
 * - Fallback to URL path:  /admin/{table}[/...]   → browse_{table}
 */
if (!function_exists('_infer_browse_permission_key')) {
    function _infer_browse_permission_key(MenuItem $item): ?string
    {
        // Prefer named route voyager.{table}.index
        $route = $item->route;
        if ($route && Str::startsWith($route, 'voyager.')) {
            // e.g. voyager.jobs.index -> jobs
            $rest = Str::after($route, 'voyager.');
            $table = Str::before($rest, '.');
            return $table ? "browse_{$table}" : null;
        }

        // Fallback to URL path
        $path = _path_of($item->link(true)); // e.g. /admin/jobs or /admin/jobs/...
        if (preg_match('#^/admin/([a-z0-9_\-]+)(?:/.*)?$#i', $path, $m)) {
            $table = $m[1];
            return "browse_{$table}";
        }

        return null;
    }
}

/**
 * Active-state check that respects permission filtering.
 * Returns 'active' if the item (or any permitted descendant) matches the current path.
 */
if (!function_exists('checkIfOneMenuItemActive')) {
    function checkIfOneMenuItemActive(
        MenuItem $item,
        array $allowedBrowseIds,
        array $allowedBrowseKeys,
        ?string $currentPath = null
    ): string {
        $currentPath = $currentPath ?? _path_of(url()->current());
        $href = $item->link(true);

        // If item has children, recurse – only through permitted items
        $children = $item->children()->orderBy('order')->get();
        foreach ($children as $child) {
            // permission gate for child
            $allowed = true;
            if (!is_null($child->permission_id)) {
                $allowed = in_array($child->permission_id, $allowedBrowseIds, true);
            } else {
                if ($permKey = _infer_browse_permission_key($child)) {
                    $allowed = in_array($permKey, $allowedBrowseKeys, true);
                }
            }
            if (!$allowed) {
                continue;
            }

            if (checkIfOneMenuItemActive($child, $allowedBrowseIds, $allowedBrowseKeys, $currentPath) === 'active') {
                return 'active';
            }
        }

        // Leaf (or branch link) active check – boundary-safe
        return _leaf_active($href, $currentPath) ? 'active' : '';
    }
}

// --------- Recursive renderer -----------
if (!function_exists('renderAdminMenu')) {
    function renderAdminMenu(): string
    {
        $user = auth()->user();

        // Roles (many-to-many via roles_all as per your project)
        $roleIds = $user->roles_all()->pluck('id')->toArray();

        // Permissions attached to those roles
        $permissionIds = DB::table('permission_role')
            ->whereIn('role_id', $roleIds)
            ->pluck('permission_id')
            ->toArray();

        // Build both ID and KEY whitelists for browse_* permissions
        $allowedBrowseIds = Permission::where('key', 'LIKE', 'browse%')
            ->whereIn('id', $permissionIds)
            ->pluck('id')->toArray();

        $allowedBrowseKeys = Permission::where('key', 'LIKE', 'browse%')
            ->whereIn('id', $permissionIds)
            ->pluck('key')->toArray();

        $menu = Menu::where('name', 'admin')->firstOrFail();
        $roots = $menu->items()->whereNull('parent_id')->orderBy('order')->get();

        $currentPath = _path_of(url()->current());

        return _renderMenuLevel($roots, 0, $allowedBrowseIds, $allowedBrowseKeys, $currentPath);
    }
}

if (!function_exists('_renderMenuLevel')) {
    function _renderMenuLevel(
        $items,
        int $level,
        array $allowedBrowseIds,
        array $allowedBrowseKeys,
        string $currentPath
    ): string {
        $ulClass = $level === 0 ? 'menu-inner py-1' : 'menu-sub';
        $html = "<ul class=\"{$ulClass}\">";

        foreach ($items as $item) {
            /** @var MenuItem $item */

            // ---- Permission gate ----
            $allowed = true;

            if (!is_null($item->permission_id)) {
                // Use explicit permission_id on the item
                $allowed = in_array($item->permission_id, $allowedBrowseIds, true);
            } else {
                // Fallback: infer browse_* from route/url
                if ($permKey = _infer_browse_permission_key($item)) {
                    $allowed = in_array($permKey, $allowedBrowseKeys, true);
                }
            }

            if (!$allowed) {
                continue; // hide this item
            }
            // ---- end permission gate ----

            $href = $item->link(true);
            $children = $item->children()->orderBy('order')->get();
            $hasChildren = $children->isNotEmpty();

            // Active/open logic:
            // - Branch: 'open' if any permitted descendant is active
            // - Leaf: 'active' only if boundary-safe match
            if ($hasChildren) {
                $branchActive = (checkIfOneMenuItemActive($item, $allowedBrowseIds, $allowedBrowseKeys, $currentPath) === 'active');
                $liClass = $branchActive ? 'menu-item open' : 'menu-item';

                $html .= "
                    <li class=\"{$liClass}\">
                        <a href=\"javascript:void(0);\" class=\"menu-link menu-toggle\">
                            <i class=\"menu-icon tf-icons ti ti-smart-home\"></i>
                            <div data-i18n=\"{$item->title}\">{$item->title}</div>
                        </a>
                        " . _renderMenuLevel($children, $level + 1, $allowedBrowseIds, $allowedBrowseKeys, $currentPath) . "
                    </li>
                ";
            } else {
                $leafActive = _leaf_active($href, $currentPath);
                $liClass = $leafActive ? 'menu-item active' : 'menu-item';

                $html .= "
                    <li class=\"{$liClass}\">
                        <a href=\"{$href}\" class=\"menu-link\">
                            <i class=\"menu-icon tf-icons ti ti-mail\"></i>
                            <div data-i18n=\"{$item->title}\">{$item->title}</div>
                        </a>
                    </li>
                ";
            }
        }

        $html .= '</ul>';
        return $html;
    }
}
