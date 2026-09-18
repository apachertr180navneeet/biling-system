<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Translation;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class LanguageController extends Controller
{
    public function index()
    {
        return view('admin.languages.index');
    }

    public function data(Request $request)
    {
        $query = Language::withCount('translations');

        return DataTables::of($query)
            ->addColumn('direction_badge', fn($l) => '<span class="badge bg-' . ($l->direction === 'rtl' ? 'warning' : 'info') . '">' . strtoupper($l->direction) . '</span>')
            ->addColumn('status_badge', fn($l) => $l->status === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('default_badge', fn($l) => $l->is_default ? '<span class="badge bg-primary">Default</span>' : '')
            ->addColumn('actions', function ($l) {
                $editUrl = route('admin.languages.edit', $l);
                $translateUrl = route('admin.languages.translations', $l);
                return "<div class='btn-group btn-group-sm'>
                    <a href='{$translateUrl}' class='btn btn-info' title='Translations'><i class='bx bx-language'></i></a>
                    <a href='{$editUrl}' class='btn btn-primary' title='Edit'><i class='bx bx-edit'></i></a>
                </div>";
            })
            ->rawColumns(['direction_badge', 'status_badge', 'default_badge', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $language = null;
        return view('admin.languages.form', compact('language'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:languages,code',
                'native_name' => 'nullable|string|max:255',
                'direction' => 'required|in:ltr,rtl',
                'is_active' => 'nullable',
            ]);

            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active');

            if ($request->boolean('is_default')) {
                Language::where('is_default', true)->update(['is_default' => false]);
                $data['is_default'] = true;
            }

            Language::create($data);
            return redirect()->route('admin.languages.index')->with('success', 'Language created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Language $language)
    {
        return view('admin.languages.form', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
                'native_name' => 'nullable|string|max:255',
                'direction' => 'required|in:ltr,rtl',
                'is_active' => 'nullable',
            ]);

            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active');

            if ($request->boolean('is_default')) {
                Language::where('is_default', true)->where('id', '!=', $language->id)->update(['is_default' => false]);
                $data['is_default'] = true;
            }

            $language->update($data);
            return redirect()->route('admin.languages.index')->with('success', 'Language updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function translations(Language $language)
    {
        $translations = Translation::where('language_id', $language->id)->orderBy('group')->orderBy('key')->get();
        return view('admin.languages.translations', compact('language', 'translations'));
    }

    public function storeTranslation(Request $request, Language $language)
    {
        try {
            $request->validate([
                'group' => 'required|string|max:255',
                'key' => 'required|string|max:255',
                'value' => 'required|string',
            ]);

            Translation::updateOrCreate(
                ['language_id' => $language->id, 'group' => $request->group, 'key' => $request->key],
                ['value' => $request->value]
            );

            return redirect()->back()->with('success', 'Translation saved!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroyTranslation(Translation $translation)
    {
        $translation->delete();
        return redirect()->back()->with('success', 'Translation deleted!');
    }
}
