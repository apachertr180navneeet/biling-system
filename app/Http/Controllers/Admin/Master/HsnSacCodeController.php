<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HsnSacCode;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class HsnSacCodeController extends Controller
{
    public function index()
    {
        return view('admin.masters.hsn-sac-codes.index');
    }

    public function data(Request $request)
    {
        $query = HsnSacCode::latest();

        return DataTables::of($query)
            ->filterColumn('code', fn($q, $v) => $q->where('code', 'like', "%{$v}%"))
            ->filterColumn('name', fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->addColumn('type_label', fn($item) => strtoupper($item->type))
            ->addColumn('gst_rate_label', fn($item) => $item->gst_rate . '%')
            ->addColumn('status_badge', function($item) {
                $badge = $item->status === 'active'
                    ? '<span class="badge bg-success" style="cursor:pointer" onclick="toggleStatus('.$item->id.')" title="Click to deactivate">Active</span>'
                    : '<span class="badge bg-secondary" style="cursor:pointer" onclick="toggleStatus('.$item->id.')" title="Click to activate">Inactive</span>';
                return $badge;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->addColumn('actions', function ($item) {
                // Use Boxicons and call the JS functions present in the view (editRow, deleteRow)
                $code = addslashes($item->code ?? '');
                $name = addslashes($item->name ?? '');
                $type = $item->type ?? '';
                $gst = $item->gst_rate ?? 0;
                $cess = $item->cess_rate ?? 0;
                $desc = addslashes($item->description ?? '');
                $status = $item->status ?? '';

                $editOnclick = "editRow({$item->id}, '{$code}', '{$name}', '{$type}', {$gst}, {$cess}, '{$desc}', '{$status}')";
                $deleteOnclick = "deleteRow({$item->id})";

                return '<button class="btn btn-sm btn-icon btn-outline-primary" onclick="' . $editOnclick . '" title="Edit"><i class="bx bx-edit"></i></button> '
                     . '<button class="btn btn-sm btn-icon btn-outline-danger" onclick="' . $deleteOnclick . '" title="Delete"><i class="bx bx-trash"></i></button>';
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:20|unique:hsn_sac_codes,code,' . $request->id,
                'name' => 'required|string|max:255',
                'type' => 'required|in:hsn,sac',
                'gst_rate' => 'required|numeric|min:0|max:100',
                'cess_rate' => 'nullable|numeric|min:0|max:100',
                'description' => 'nullable|string',
            ]);

            HsnSacCode::updateOrCreate(
                ['id' => $request->id],
                $request->only('code', 'name', 'type', 'gst_rate', 'cess_rate', 'description', 'status')
            );

            return response()->json(['success' => true, 'message' => 'HSN/SAC code saved successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy(HsnSacCode $hsnSacCode)
    {
        try {
            $hsnSacCode->delete();
            return response()->json(['success' => true, 'message' => 'HSN/SAC code deleted successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function status(HsnSacCode $hsnSacCode)
    {
        try {
            $hsnSacCode->update(['status' => $hsnSacCode->status === 'active' ? 'inactive' : 'active']);
            return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
