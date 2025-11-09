<?php


namespace App\Http\Controllers\Admin;

use App\Models\HomeHero;
use App\Support\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeHeroController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'home_heroes';
        $this->model = HomeHero::class;
    }

    // Voyager: GET voyager.home_heroes.index → redirect to edit singleton
    public function index()
    {
        $this->checkPermission('browse');

        $hero = HomeHero::singleton();
        return redirect()->route('voyager.home_heroes.edit', $hero->id);
    }

    // Voyager: GET voyager.home_heroes.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $hero = HomeHero::forCompany()->findOrFail($id);

        return view('admin.site.home_heroes.edit', compact('hero'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.home_heroes.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $request->validate([
            'kicker' => 'nullable|string|max:120',
            'headline' => 'required|string|max:190',
            'subheadline' => 'nullable|string|max:255',
            'primary_cta_text' => 'nullable|string|max:80',
            'primary_cta_url' => 'nullable|string|max:255',
            'secondary_cta_text' => 'nullable|string|max:80',
            'secondary_cta_url' => 'nullable|string|max:255',
            'background_type' => 'required|in:image,color,video',
            'background_value' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $hero = HomeHero::forCompany()->findOrFail($id);

        $hero->update([
            'kicker' => $request->input('kicker'),
            'headline' => $request->input('headline'),
            'subheadline' => $request->input('subheadline'),
            'primary_cta_text' => $request->input('primary_cta_text'),
            'primary_cta_url' => $request->input('primary_cta_url'),
            'secondary_cta_text' => $request->input('secondary_cta_text'),
            'secondary_cta_url' => $request->input('secondary_cta_url'),
            'background_type' => $request->input('background_type', 'image'),
            'background_value' => $request->input('background_value'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('voyager.home_heroes.edit', $id)
            ->with(['message' => 'تم حفظ بيانات قسم البطل بنجاح', 'alert-type' => 'success']);
    }
}
