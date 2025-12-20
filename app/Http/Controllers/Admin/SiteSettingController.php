<?php

namespace App\Http\Controllers\Admin;

use App\Models\SiteSetting;
use App\Services\UploaderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SiteSettingController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'site_settings';
        $this->model = SiteSetting::class;
    }

    public function index()
    {
//        $this->checkPermission($this->moduleName . '_edit');

        $settings = SiteSetting::orderBy('key')->get();

        return view('admin.site_settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
//        $this->checkPermission($this->moduleName . '_edit');

        $payload = $request->input('settings', []);
        $toDeleteIds = array_map('intval', $request->input('deleted_ids', []));

        // Normalize: remove completely empty new rows
        $payload = collect($payload)->filter(function ($row) {
            $key = trim((string)($row['key'] ?? ''));
            $type = (string)($row['type'] ?? '');
            $hasValue = isset($row['value']) && trim((string)$row['value']) !== '';
            return $key !== '' || $type !== '' || $hasValue;
        })->toArray();

        // Build validation rules per row
        $rules = [];
        $messages = [];

        foreach ($payload as $rowKey => $row) {
            $id = $row['id'] ?? null;
            $keyPath = "settings.$rowKey.key";
            $typePath = "settings.$rowKey.type";
            $valuePath = "settings.$rowKey.value";
            $filePath = "settings.$rowKey.file";

            $rules[$keyPath] = ['required', 'string', 'max:191'];
            $rules[$typePath] = ['required', Rule::in(['text', 'longtext', 'file'])];

            // Key uniqueness (ignore same ID)
            $rules[$keyPath][] = Rule::unique('site_settings', 'key')->ignore($id);

            // Value required for text/longtext
            $rules[$valuePath] = [
                Rule::requiredIf(fn() => in_array(($row['type'] ?? null), ['text', 'longtext'], true)),
                'nullable',
                'string',
            ];

            // File validation when type=file
            // Required if: type=file AND (new OR existing has no value OR switched_to_file flag)
            $rules[$filePath] = [
                Rule::requiredIf(function () use ($row) {
                    if (($row['type'] ?? null) !== 'file') return false;
                    $isNew = empty($row['id']);
                    $hasExistingFile = !empty($row['value']); // value holds old path
                    $switchedToFile = !empty($row['switched_to_file']);
                    return $isNew || $switchedToFile || !$hasExistingFile;
                }),
                'nullable',
                'file',
                'max:5120', // 5MB
                // If you want strict types: 'mimes:jpg,jpeg,png,webp,pdf'
            ];

            $messages["$filePath.required"] = 'File is required for file type settings.';
        }

        // Prevent duplicate keys inside the same request
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->after(function ($v) use ($payload) {
            $keys = [];
            foreach ($payload as $rowKey => $row) {
                $k = trim((string)($row['key'] ?? ''));
                if ($k === '') continue;
                $lower = mb_strtolower($k);
                if (isset($keys[$lower])) {
                    $v->errors()->add("settings.$rowKey.key", 'This key is duplicated in your submitted settings.');
                } else {
                    $keys[$lower] = true;
                }
            }
        });

        $validator->validate();

        DB::transaction(function () use ($request, $payload, $toDeleteIds) {

            // 1) Delete marked rows (+ delete files)
            if (!empty($toDeleteIds)) {
                $deleteRows = SiteSetting::whereIn('id', $toDeleteIds)->get();
                foreach ($deleteRows as $row) {
                    if ($row->type === 'file' && $row->value) {
                        $this->deleteStoredFile($row->value);
                    }
                    $row->delete();
                }
            }

            // 2) Upsert rows
            foreach ($payload as $rowKey => $row) {
                // If user marked it deleted, skip
                if (!empty($row['id']) && in_array((int)$row['id'], $toDeleteIds, true)) {
                    continue;
                }

                $id = $row['id'] ?? null;
                $type = $row['type'];
                $key = trim($row['key']);

                $setting = $id ? SiteSetting::find($id) : new SiteSetting();

                if (!$setting) {
                    // if the row was deleted concurrently, treat as new
                    $setting = new SiteSetting();
                }

                $oldType = $setting->exists ? $setting->type : null;
                $oldValue = $setting->exists ? $setting->value : null;

                $setting->key = $key;
                $setting->type = $type;

                // Handle type switching cleanup (as agreed)
                $switchedAwayFromFile = ($oldType === 'file' && in_array($type, ['text', 'longtext'], true));
                $switchedToFile = (in_array($oldType, ['text', 'longtext'], true) && $type === 'file');

                if ($switchedAwayFromFile && $oldValue) {
                    $this->deleteStoredFile($oldValue);
                    $oldValue = null;
                }

                if ($switchedToFile) {
                    // clear incompatible text value
                    $oldValue = null;
                }

                if (in_array($type, ['text', 'longtext'], true)) {
                    $setting->value = (string)($row['value'] ?? '');
                    $setting->save();
                    continue;
                }

                // type=file
                // if new file uploaded => upload + delete old file (if any)
                $uploadedFile = $request->file("settings.$rowKey.file");

                if ($uploadedFile) {
                    if (!empty($oldValue)) {
                        $this->deleteStoredFile($oldValue);
                    }

                    // Use your UploaderService (same style you already use)
//                    $folderName = $this->uploadFolder;
                    list($_, $path) = (new UploaderService())->uploadFile($uploadedFile, 'site_projects');
//                    list($_, $path) = (new UploaderService())->uploadFile($uploadedFile, $folderName);

                    $setting->value = $path;
                    $setting->save();
                } else {
                    // no new upload => keep existing path (if exists), otherwise stays null (but validator already prevented that)
                    $setting->value = $oldValue;
                    $setting->save();
                }
            }
        });

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    private function deleteStoredFile(string $path): void
    {
        // UploaderService stores paths like "site_settings/xxx.ext"
        // Ensure we delete from the disk used by your upload flow (usually "public")
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
