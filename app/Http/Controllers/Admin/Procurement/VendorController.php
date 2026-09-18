<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\VendorDocument;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class VendorController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        $categories = VendorCategory::where('status', 'active')->get();
        return view('admin.procurement.vendors.index', compact('hotels', 'categories'));
    }

    public function data(Request $request)
    {
        $query = Vendor::query()->with(['hotel', 'category']);

        return DataTables::of($query)
            ->filterColumn('company_name', function ($query, $value) {
                $query->where('company_name', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('category_name', function ($query, $value) {
                $query->whereHas('category', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($v) => $v->hotel->name ?? '')
            ->addColumn('category_name', fn($v) => $v->category->name ?? '')
            ->addColumn('status_url', fn($v) => route('admin.procurement.vendors.status', $v))
            ->addColumn('edit_url', fn($v) => route('admin.procurement.vendors.edit', $v))
            ->addColumn('delete_url', fn($v) => route('admin.procurement.vendors.destroy', $v))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $vendor = null;
        $hotels = Hotel::where('status', 'active')->get();
        $categories = VendorCategory::where('status', 'active')->get();
        return view('admin.procurement.vendors.form', compact('vendor', 'hotels', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'vendor_category_id' => 'nullable|exists:vendor_categories,id',
                'company_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'pincode' => 'nullable|string|max:10',
                'gstin' => 'nullable|string|max:30',
                'pan' => 'nullable|string|max:20',
                'cin' => 'nullable|string|max:30',
                'tan' => 'nullable|string|max:30',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:50',
                'bank_ifsc' => 'nullable|string|max:20',
                'bank_branch' => 'nullable|string|max:255',
                'credit_limit' => 'nullable|numeric|min:0',
                'payment_terms' => 'nullable|string|max:255',
                'vendor_type' => 'required|in:material,service,both',
                'rating' => 'nullable|in:excellent,good,average,poor',
                'agreement_start_date' => 'nullable|date',
                'agreement_end_date' => 'nullable|date|after_or_equal:agreement_start_date',
                'notes' => 'nullable|string',
                'status' => 'required|in:active,inactive,blacklisted',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('vendors', $request->company_name);
            Vendor::create($data);
            return redirect()->route('admin.procurement.vendors.index')->with('success', 'Vendor created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Vendor $vendor)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $categories = VendorCategory::where('status', 'active')->get();
        return view('admin.procurement.vendors.form', compact('vendor', 'hotels', 'categories'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'vendor_category_id' => 'nullable|exists:vendor_categories,id',
                'company_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'pincode' => 'nullable|string|max:10',
                'gstin' => 'nullable|string|max:30',
                'pan' => 'nullable|string|max:20',
                'cin' => 'nullable|string|max:30',
                'tan' => 'nullable|string|max:30',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:50',
                'bank_ifsc' => 'nullable|string|max:20',
                'bank_branch' => 'nullable|string|max:255',
                'credit_limit' => 'nullable|numeric|min:0',
                'payment_terms' => 'nullable|string|max:255',
                'vendor_type' => 'required|in:material,service,both',
                'rating' => 'nullable|in:excellent,good,average,poor',
                'agreement_start_date' => 'nullable|date',
                'agreement_end_date' => 'nullable|date|after_or_equal:agreement_start_date',
                'notes' => 'nullable|string',
                'status' => 'required|in:active,inactive,blacklisted',
            ]);
            $vendor->update($request->all());
            return redirect()->route('admin.procurement.vendors.index')->with('success', 'Vendor updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Vendor $vendor)
    {
        try {
            $vendor->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Vendor deleted successfully!']);
            }
            return redirect()->route('admin.procurement.vendors.index')->with('success', 'Vendor deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Vendor $vendor)
    {
        try {
            $vendor->status = $vendor->status === 'active' ? 'inactive' : 'active';
            $vendor->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $vendor->status, 'message' => 'Vendor status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Vendor status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['hotel', 'category', 'documents']);
        return view('admin.procurement.vendors.show', compact('vendor'));
    }
}
