<?php


namespace App\Http\Controllers\Admin;

use App\Models\FooterLink;
use App\Models\SiteSetting;
use App\Support\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteSettingController extends MainController
{
    public function __construct()
    {
        // Not a typical CRUD list; treat as a singleton module
        $this->moduleName = 'site_settings';
        $this->model = SiteSetting::class;
    }

    // Voyager: GET voyager.site_settings.index => redirect to edit singleton
    public function index()
    {
        $this->checkPermission('browse');

        $settings = SiteSetting::singleton();
        return redirect()->route('voyager.site_settings.edit', $settings->id);
    }

    // Voyager: GET voyager.site_settings.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $settings = SiteSetting::forCompany()->findOrFail($id);

        // Load footer links grouped (useful, services, projects, custom)
        $footerLinks = FooterLink::forCompany()
            ->ordered()
            ->get()
            ->groupBy('group_key');

        return view('admin.site.site_settings.edit', compact('settings', 'footerLinks'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.site_settings.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $request->validate([
            // Contacts
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|string|max:190',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:190',

            'address_line' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',

            // Socials
            'whatsapp_url' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',

            // Portfolio CTA
            'portfolio_cta_text' => 'nullable|string|max:120',
            'portfolio_cta_url' => 'nullable|string|max:255',

            // Footer about
            'footer_about_title' => 'nullable|string|max:190',
            'footer_about_text' => 'nullable|string',

            // Branding
            'logo_path' => 'nullable|string|max:255',
            'favicon_path' => 'nullable|string|max:255',

            // Footer links (optional, inline manage)
            'footer_links' => 'sometimes|array',
            'footer_links.*.id' => 'nullable|integer|exists:footer_links,id',
            'footer_links.*.label' => 'required_with:footer_links|string|max:190',
            'footer_links.*.url' => 'required_with:footer_links|string|max:255',
            'footer_links.*.group_key' => 'nullable|string|max:50',
            'footer_links.*.display_order' => 'nullable|integer|min:0',
            'footer_links.*.is_active' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $id) {
            $settings = SiteSetting::forCompany()->findOrFail($id);

            // Normalize empty strings in arrays
            $emails = array_values(array_filter((array)$request->input('emails', []), fn($v) => filled($v)));
            $phones = array_values(array_filter((array)$request->input('phones', []), fn($v) => filled($v)));

            $settings->update([
                'emails' => $emails,
                'phones' => $phones,
                'address_line' => $request->input('address_line'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'whatsapp_url' => $request->input('whatsapp_url'),
                'facebook_url' => $request->input('facebook_url'),
                'instagram_url' => $request->input('instagram_url'),
                'linkedin_url' => $request->input('linkedin_url'),
                'youtube_url' => $request->input('youtube_url'),
                'portfolio_cta_text' => $request->input('portfolio_cta_text'),
                'portfolio_cta_url' => $request->input('portfolio_cta_url'),
                'footer_about_title' => $request->input('footer_about_title'),
                'footer_about_text' => $request->input('footer_about_text'),
                'logo_path' => $request->input('logo_path'),
                'favicon_path' => $request->input('favicon_path'),
            ]);

            // Inline manage Footer Links (optional)
            if ($request->has('footer_links')) {
                $keepIds = [];
                foreach ((array)$request->input('footer_links', []) as $row) {
                    if (empty($row['label']) || empty($row['url'])) {
                        continue;
                    }
                    $payload = [
                        'label' => $row['label'],
                        'url' => $row['url'],
                        'group_key' => $row['group_key'] ?? 'useful',
                        'display_order' => (int)($row['display_order'] ?? 0),
                        'is_active' => !empty($row['is_active']),
                        'company_id' => CompanyContext::id(),
                    ];

                    if (!empty($row['id'])) {
                        $link = FooterLink::forCompany()->find($row['id']);
                        if ($link) {
                            $link->update($payload);
                            $keepIds[] = $link->id;
                        }
                    } else {
                        $link = FooterLink::create($payload);
                        $keepIds[] = $link->id;
                    }
                }

                // Remove any links not present in payload (for this company)
                FooterLink::forCompany()
                    ->when(!empty($keepIds), fn($q) => $q->whereNotIn('id', $keepIds))
                    ->delete();
            }
        });

        return redirect()->route('voyager.site_settings.edit', $id)
            ->with(['message' => 'تم حفظ الإعدادات بنجاح', 'alert-type' => 'success']);
    }

    // Optional quick-delete for a single footer link (AJAX)
    public function deleteFooterLink(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $link = FooterLink::forCompany()->findOrFail($id);
        $link->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
