<?php


namespace App\Http\Controllers\Admin;

use App\Models\AboutBlock;
use App\Support\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AboutBlockController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'about_blocks';
        $this->model = AboutBlock::class;
    }

    // Voyager: GET voyager.about_blocks.index → redirect to edit singleton
    public function index()
    {
        $this->checkPermission('browse');

        $about = AboutBlock::singleton();
        return redirect()->route('voyager.about_blocks.edit', $about->id);
    }

    // Voyager: GET voyager.about_blocks.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $about = AboutBlock::forCompany()->findOrFail($id);

        return view('admin.site.about_blocks.edit', compact('about'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.about_blocks.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $request->validate([
            'title' => 'required|string|max:190',
            'body' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
        ]);

        $about = AboutBlock::forCompany()->findOrFail($id);

        $about->update([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'video_url' => $request->input('video_url'),
        ]);

        return redirect()->route('voyager.about_blocks.edit', $id)
            ->with(['message' => 'تم حفظ بيانات قسم التعريف بنجاح', 'alert-type' => 'success']);
    }
}
