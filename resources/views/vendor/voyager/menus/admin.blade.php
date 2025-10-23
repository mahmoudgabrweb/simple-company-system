@php
    /**
     * Expected variables:
     * $items (collection of menu items)
     * $isModelTranslatable (bool)
     *
     * Renders a nestable OL/LI tree:
     *  - .dd-item[data-id]
     *  - .dd-handle (drag handle)
     *  - .item_actions (buttons with data-* payloads for modal)
     */
@endphp

<style>
    :root{ --primary:#1D2A3A; --accent:#B89564; --muted:#6C757D; --line:#E0E3E7; }

    .dd-list { list-style:none; padding-left:0; }
    .dd-item { margin-bottom:.5rem; }

    .menu-item-card{
        display:flex; align-items:center; gap:12px;
        border:1px solid var(--line);
        background:#fff; border-radius:12px; padding:.6rem .75rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.03);
    }
    .dd-handle{
        cursor:grab; user-select:none;
        width:36px; height:36px; border-radius:8px;
        border:1px dashed var(--line);
        display:flex; align-items:center; justify-content:center;
        background:#f8f9fa;
    }
    .dd-handle:active{ cursor:grabbing; }
    .grip{
        width:16px; height:16px; position:relative;
        background: radial-gradient(#b6b8bb 1.5px, transparent 2px) 0 0/6px 6px;
        opacity:.9;
    }
    .icon-dot{
        width:32px; height:32px; border-radius:999px;
        background: #f3efe9; display:flex; align-items:center; justify-content:center;
        border:1px solid #efe7db; color:#a07e4e;
        font-size:16px;
    }
    .mi-title{ font-weight:600; color:#1b1f24; }
    .mi-sub{ font-size:.9rem; color:var(--muted); }
    .badge-pill{
        border:1px solid var(--line); border-radius:999px;
        padding:.15rem .5rem; font-size:.75rem; color:#343a40; background:#f8f9fa;
    }
    .badge-accent{
        background: rgba(184,149,100,.12); border-color: rgba(184,149,100,.35); color:#7a5d34;
    }
    .item_actions .btn{
        padding:.25rem .5rem; font-size:.8rem;
    }
    .dd-placeholder{
        background:rgba(184,149,100,.15);
        border:2px dashed var(--accent); border-radius:10px;
        height:46px; margin:.5rem 0;
    }
    .child-list { margin-left:2.25rem; } /* indent children */
</style>

@php
    $renderItems = function($items) use (&$renderItems, $isModelTranslatable) {
        if ($items->isEmpty()) return;
        echo '<ol class="dd-list">';
        foreach ($items as $item) {
            $title = $item->getTranslatedAttribute('title');
            $isRoute = !empty($item->route);
            $labelLeft = $isRoute ? 'Route' : 'URL';
            $labelRight = $item->target === '_blank' ? 'Blank' : 'Self';
            $displayRight = $item->target === '_blank' ? '_blank' : '_self';

            // icon preview
            $icon = trim($item->icon_class ?? '') !== '' ? '<i class="'.$item->icon_class.'"></i>' : '<i class="bx bx-link-alt"></i>';

            echo '<li class="dd-item" data-id="'.$item->id.'">';
                echo '<div class="menu-item-card">';

                    // Drag handle
                    echo '<div class="dd-handle" title="Drag to reorder"><span class="grip"></span></div>';

                    // Icon
                    echo '<div class="icon-dot">'.$icon.'</div>';

                    // Content (title + meta)
                    echo '<div class="flex-grow-1">';
                        echo '<div class="mi-title">'.$title.'</div>';
                        echo '<div class="mi-sub">';
                            if ($isRoute) {
                                echo '<span class="badge-pill badge-accent me-2">Route</span>';
                                echo e($item->route);
                                if (!empty($item->parameters)) {
                                    echo ' <span class="text-muted">·</span> <code>'.e(json_encode($item->parameters)).'</code>';
                                }
                            } else {
                                echo '<span class="badge-pill me-2">URL</span>';
                                echo e($item->url);
                            }
                            echo ' <span class="text-muted mx-2">·</span> ';
                            echo '<span class="badge-pill">'.e($displayRight).'</span>';
                        echo '</div>';
                    echo '</div>';

                    // Actions
                    echo '<div class="item_actions">';
                        // Edit button with payload
                        echo '<button type="button" class="btn btn-outline-primary me-1 edit"
                                data-id="'.$item->id.'"
                                data-title="'.e($title).'"
                                data-url="'.e($item->url).'"
                                data-route="'.e($item->route).'"
                                data-parameters=\''.e(json_encode($item->parameters)).'\'
                                data-icon_class="'.e($item->icon_class).'"
                                data-color="'.e($item->color).'"
                                data-target="'.e($item->target).'">
                                <i class="bx bx-edit"></i> '.__('voyager::generic.edit').'
                              </button>';

                        // Delete
                        echo '<button type="button" class="btn btn-outline-danger delete" data-id="'.$item->id.'">
                                <i class="bx bx-trash"></i> '.__('voyager::generic.delete').'
                              </button>';

                        // Hidden i18n field Voyager expects (if translatable)
                        if ($isModelTranslatable) {
                            echo '<input type="hidden" id="title'.$item->id.'_i18n" value="'.e(json_encode($item->getTranslationsOf('title'))).'">';
                        }
                    echo '</div>';

                echo '</div>'; // card

                // Children
                if ($item->children && $item->children->count()) {
                    echo '<div class="child-list">';
                    $renderItems($item->children);
                    echo '</div>';
                }

            echo '</li>';
        }
        echo '</ol>';
    };
@endphp

{!! $renderItems($items) !!}
