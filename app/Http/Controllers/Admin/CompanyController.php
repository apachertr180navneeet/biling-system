<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Currency;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;
use Exception;

class CompanyController extends Controller
{
    public function index()
    {
        return view('admin.company.index');
    }

    public function data(Request $request)
    {
        $query = Company::with('currency');

        return DataTables::of($query)
            ->addColumn('currency_name', fn($c) => $c->currency?->name ?? '-')
            ->addColumn('status_badge', fn($c) => $c->status === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('actions', function ($c) {
                $editUrl = route('admin.company.edit', $c);
                return "<a href='{$editUrl}' class='btn btn-sm btn-primary'><i class='bx bx-edit'></i></a>";
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $company = null;
        $currencies = Currency::where('status', 'active')->get();
        return view('admin.company.form', compact('company', 'currencies'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'website' => 'nullable|url',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'currency_id' => 'nullable|exists:currencies,id',
                'timezone' => 'nullable|string',
                'gstin' => 'nullable|string|max:15',
                'pan' => 'nullable|string|max:10',
            ]);

            $data = $request->all();
            $data['slug'] = \App\Helpers\Helper::slug('companies', $request->name);
            $data['is_gst_registered'] = $request->boolean('is_gst_registered');
            $data['is_einvoice_enabled'] = $request->boolean('is_einvoice_enabled');
            $data['status'] = $request->boolean('status') ? 'active' : 'inactive';

            if ($request->file('logo')) {
                $file = $request->file('logo');
                $filename = time() . $file->getClientOriginalName();
                $folder = 'uploads/company/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $data['logo'] = $folder . $filename;
            }

            Company::create($data);
            return redirect()->route('admin.company.index')->with('success', 'Company created!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Company $company)
    {
        $currencies = Currency::where('status', 'active')->get();
        return view('admin.company.form', compact('company', 'currencies'));
    }

    public function update(Request $request, Company $company)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'website' => 'nullable|url',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'currency_id' => 'nullable|exists:currencies,id',
                'timezone' => 'nullable|string',
                'gstin' => 'nullable|string|max:15',
                'pan' => 'nullable|string|max:10',
            ]);

            $data = $request->except('logo');
            $data['is_gst_registered'] = $request->boolean('is_gst_registered');
            $data['is_einvoice_enabled'] = $request->boolean('is_einvoice_enabled');
            $data['status'] = $request->boolean('status') ? 'active' : 'inactive';

            if ($request->file('logo')) {
                $file = $request->file('logo');
                $filename = time() . $file->getClientOriginalName();
                $folder = 'uploads/company/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $data['logo'] = $folder . $filename;
            }

            $company->update($data);
            return redirect()->route('admin.company.index')->with('success', 'Company updated!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.company.index')->with('success', 'Company deleted!');
    }
}
