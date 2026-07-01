<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePartCategory;
use Illuminate\Http\Request;

class SparePartCategoryController extends Controller
{
    public function index()
    {
        $categories = SparePartCategory::orderBy('name')->paginate(20);
        return view('admin.spare_part_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.spare_part_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:spare_part_categories']);
        SparePartCategory::create($data);
        return redirect()->route('admin.spare-part-categories.index')->withSuccess('Category created successfully.');
    }

    public function edit(SparePartCategory $sparePartCategory)
    {
        return view('admin.spare_part_categories.edit', ['category' => $sparePartCategory]);
    }

    public function update(Request $request, SparePartCategory $sparePartCategory)
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:spare_part_categories,name,' . $sparePartCategory->id]);
        $sparePartCategory->update($data);
        return redirect()->route('admin.spare-part-categories.index')->withSuccess('Category updated successfully.');
    }

    public function destroy(SparePartCategory $sparePartCategory)
    {
        $sparePartCategory->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }

    public function toggleStatus(SparePartCategory $sparePartCategory)
    {
        $sparePartCategory->update(['is_active' => !$sparePartCategory->is_active]);
        return response()->json(['success' => true, 'is_active' => $sparePartCategory->fresh()->is_active]);
    }
}
