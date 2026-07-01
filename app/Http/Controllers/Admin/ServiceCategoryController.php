<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::orderBy('name')->paginate(20);
        return view('admin.service_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.service_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
            'description' => 'nullable|string',
        ]);
        ServiceCategory::create($data);
        return redirect()->route('admin.service-categories.index')->withSuccess('Category created successfully.');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        return view('admin.service_categories.edit', ['category' => $serviceCategory]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $serviceCategory->id,
            'description' => 'nullable|string',
        ]);
        $serviceCategory->update($data);
        return redirect()->route('admin.service-categories.index')->withSuccess('Category updated successfully.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(ServiceCategory $serviceCategory)
    {
        $serviceCategory->update(['is_active' => !$serviceCategory->is_active]);
        return response()->json(['success' => true, 'is_active' => $serviceCategory->is_active]);
    }
}
