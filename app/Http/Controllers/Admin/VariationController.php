<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Project, Quotation, Variation, VariationSection, VariationItem, Unit};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VariationController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'variations';
        $this->model = Variation::class;
    }

    // Guard: project must have an ACTIVE quotation that is NOT completed
    protected function assertProjectAllowsVariation(Project $project): Quotation
    {
        // "Active quotation" as we used earlier: the single active-per-project, not completed
        $active = Quotation::where('project_id', $project->id)
            ->whereIn('status', ['draft', 'sent', 'accepted']) // treat these as active
            ->orderByDesc('id')
            ->first();

        if (!$active) {
            abort(403, 'Variations require an active quotation.');
        }
        if ($active->status === 'completed') {
            abort(403, 'Cannot add variations: quotation is completed.');
        }
        return $active;
    }

    // List all variations for a project
    public function index(Project $project)
    {
        $this->checkPermission('browse');

        $variations = Variation::forProject($project->id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.variations.index', compact('project', 'variations'));
    }

    public function create(Project $project)
    {
        $this->checkPermission('add');
        $activeQuotation = $this->assertProjectAllowsVariation($project);

        $units = Unit::select('id', 'name_en as name')->orderBy('name_en')->get();
        return view('admin.variations.create', compact('project', 'units', 'activeQuotation'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->checkPermission('add');
        $activeQuotation = $this->assertProjectAllowsVariation($project);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'sent', 'accepted', 'rejected', 'expired', 'completed'])],
            'currency' => ['required', 'size:3'],
            'valid_until' => ['nullable', 'date'],

            // sections + items (arrays)
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.order' => ['nullable', 'integer', 'min:1'],
            'sections.*.items' => ['required', 'array', 'min:1'],
            'sections.*.items.*.name' => ['required', 'string', 'max:255'],
            'sections.*.items.*.description' => ['nullable', 'string'],
            'sections.*.items.*.unit_id' => ['nullable', 'exists:units,id'],
            'sections.*.items.*.qty' => ['required', 'numeric', 'min:0'],
            'sections.*.items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'sections.*.items.*.discount_type' => ['required', Rule::in(['none', 'percent', 'fixed'])],
            'sections.*.items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
            'sections.*.items.*.order' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($project, $activeQuotation, $data, &$variation) {
            $variation = Variation::create([
                'project_id' => $project->id,
                'quotation_id' => $activeQuotation->id,
                'title' => $data['title'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'currency' => $data['currency'],
                'valid_until' => $data['valid_until'] ?? null,
            ]);

            foreach ($data['sections'] as $i => $sec) {
                $section = VariationSection::create([
                    'variation_id' => $variation->id,
                    'title' => $sec['title'],
                    'order' => $sec['order'] ?? ($i + 1),
                ]);

                foreach ($sec['items'] as $j => $it) {
                    $item = new VariationItem([
                        'name' => $it['name'],
                        'description' => $it['description'] ?? null,
                        'unit_id' => $it['unit_id'] ?? null,
                        'qty' => $it['qty'],
                        'unit_price' => $it['unit_price'],
                        'discount_type' => $it['discount_type'],
                        'discount_value' => $it['discount_value'] ?? 0,
                        'order' => $it['order'] ?? ($j + 1),
                    ]);
                    $item->computeSubtotal();
                    $section->items()->save($item);
                }
            }

            $variation->refreshTotals();
        });

        return redirect()->route('voyager.projects.variations.index', $project->id)
            ->with('success', 'Variation created.');
    }

    public function show(Project $project, Variation $variation)
    {
        $this->checkPermission('read');
        abort_unless($variation->project_id === $project->id, 404);

        $variation->load(['sections.items.unit', 'quotation', 'project']);
        return view('admin.variations.show', compact('project', 'variation'));
    }

    public function edit(Project $project, Variation $variation)
    {
        $this->checkPermission('edit');
        abort_unless($variation->project_id === $project->id, 404);

        // You can still edit while base quotation is active & not completed
        $this->assertProjectAllowsVariation($project);

        $variation->load(['sections.items']);
        $units = Unit::select('id', 'name_en as name')->orderBy('name_en')->get();

        return view('admin.variations.edit', compact('project', 'variation', 'units'));
    }

    public function update(Request $request, Project $project, Variation $variation): RedirectResponse
    {
        $this->checkPermission('edit');
        abort_unless($variation->project_id === $project->id, 404);
        $this->assertProjectAllowsVariation($project);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'sent', 'accepted', 'rejected', 'expired', 'completed'])],
            'currency' => ['required', 'size:3'],
            'valid_until' => ['nullable', 'date'],

            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.order' => ['nullable', 'integer', 'min:1'],
            'sections.*.items' => ['required', 'array', 'min:1'],
            'sections.*.items.*.id' => ['nullable', 'integer'],
            'sections.*.items.*.name' => ['required', 'string', 'max:255'],
            'sections.*.items.*.description' => ['nullable', 'string'],
            'sections.*.items.*.unit_id' => ['nullable', 'exists:units,id'],
            'sections.*.items.*.qty' => ['required', 'numeric', 'min:0'],
            'sections.*.items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'sections.*.items.*.discount_type' => ['required', Rule::in(['none', 'percent', 'fixed'])],
            'sections.*.items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
            'sections.*.items.*.order' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($variation, $data) {
            $variation->update([
                'title' => $data['title'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'currency' => $data['currency'],
                'valid_until' => $data['valid_until'] ?? null,
            ]);

            // sync sections/items (simple replace strategy for clarity)
            $variation->sections()->delete();
            foreach ($data['sections'] as $i => $sec) {
                $section = VariationSection::create([
                    'variation_id' => $variation->id,
                    'title' => $sec['title'],
                    'order' => $sec['order'] ?? ($i + 1),
                ]);

                foreach ($sec['items'] as $j => $it) {
                    $item = new VariationItem([
                        'name' => $it['name'],
                        'description' => $it['description'] ?? null,
                        'unit_id' => $it['unit_id'] ?? null,
                        'qty' => $it['qty'],
                        'unit_price' => $it['unit_price'],
                        'discount_type' => $it['discount_type'],
                        'discount_value' => $it['discount_value'] ?? 0,
                        'order' => $it['order'] ?? ($j + 1),
                    ]);
                    $item->computeSubtotal();
                    $section->items()->save($item);
                }
            }

            $variation->refreshTotals();
        });

        return redirect()->route('admin.projects.variations.edit', [$variation->project_id, $variation->id])
            ->with('success', 'Variation updated.');
    }

    public function destroy(Project $project, Variation $variation): RedirectResponse
    {
        $this->checkPermission('delete');
        abort_unless($variation->project_id === $project->id, 404);

        $variation->delete();
        return redirect()->route('admin.projects.variations.index', $project->id)
            ->with('success', 'Variation deleted.');
    }
}
