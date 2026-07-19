<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMaster;
use Illuminate\Http\Request;

class VehicleMasterController extends Controller
{
    public function index()
    {
        $vehicles = VehicleMaster::orderBy('variant_name')->paginate(20);
        return view('admin.vehicle_masters.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicle_masters.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'variant_name' => 'nullable|string|max:255',
            'color_name' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:255',
            'ex_showroom_price' => 'required|numeric|min:0',
        ]);
        VehicleMaster::create($data);
        return redirect()->route('admin.vehicle-masters.index')->withSuccess('Vehicle master created successfully.');
    }

    public function edit(VehicleMaster $vehicleMaster)
    {
        return view('admin.vehicle_masters.edit', ['vehicle' => $vehicleMaster]);
    }

    public function update(Request $request, VehicleMaster $vehicleMaster)
    {
        $data = $request->validate([
            'variant_name' => 'nullable|string|max:255',
            'color_name' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:255',
            'ex_showroom_price' => 'required|numeric|min:0',
        ]);
        $vehicleMaster->update($data);
        return redirect()->route('admin.vehicle-masters.index')->withSuccess('Vehicle master updated successfully.');
    }

    public function destroy(VehicleMaster $vehicleMaster)
    {
        $vehicleMaster->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(VehicleMaster $vehicleMaster)
    {
        $vehicleMaster->update(['is_active' => !$vehicleMaster->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehicleMaster->is_active]);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vehicle_master_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['variant_name', 'color_name', 'fuel_type', 'transmission', 'ex_showroom_price']);
            // Example row
            fputcsv($file, ['test', 'red', 'Petrol', 'Manual', '750000.00']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $handle = fopen($path, 'r');
        if (!$handle) {
            return redirect()->back()->withErrors(['csv_file' => 'Failed to open the uploaded file.']);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return redirect()->back()->withErrors(['csv_file' => 'The uploaded file is empty.']);
        }

        $header = array_map(function($h) {
            return trim(strtolower($h));
        }, $header);

        $required = ['variant_name', 'color_name', 'fuel_type', 'transmission', 'ex_showroom_price'];
        foreach ($required as $req) {
            if (!in_array($req, $header)) {
                fclose($handle);
                return redirect()->back()->withErrors(['csv_file' => "Missing required header column: {$req}"]);
            }
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowCount = 0;
        $seenInFile = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            if (count($row) !== count($header)) {
                if (count(array_filter($row)) === 0) {
                    continue;
                }
                $errors[] = "Row {$rowCount}: Column count mismatch.";
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);
            
            $variantName = isset($data['variant_name']) ? trim($data['variant_name']) : '';
            $colorName = isset($data['color_name']) ? trim($data['color_name']) : '';
            $fuelType = isset($data['fuel_type']) ? trim($data['fuel_type']) : '';
            $transmission = isset($data['transmission']) ? trim($data['transmission']) : '';
            $exShowroomPrice = isset($data['ex_showroom_price']) ? trim($data['ex_showroom_price']) : '0';

            if (empty($variantName)) {
                $errors[] = "Row {$rowCount}: Variant Name is required.";
                $skipped++;
                continue;
            }

            if (!is_numeric($exShowroomPrice) || floatval($exShowroomPrice) < 0) {
                $errors[] = "Row {$rowCount}: Ex-Showroom Price must be a positive number.";
                $skipped++;
                continue;
            }

            $combKey = strtolower($variantName) . '|' . strtolower($colorName);

            if (in_array($combKey, $seenInFile)) {
                $errors[] = "Row {$rowCount}: Duplicate combination '{$variantName}' and '{$colorName}' in the CSV file.";
                $skipped++;
                continue;
            }

            $exists = VehicleMaster::whereRaw('LOWER(variant_name) = ?', [strtolower($variantName)])
                ->whereRaw('LOWER(color_name) = ?', [strtolower($colorName)])
                ->exists();

            if ($exists) {
                $errors[] = "Row {$rowCount}: Duplicate combination '{$variantName}' and '{$colorName}' already exists in the database.";
                $skipped++;
                continue;
            }

            $seenInFile[] = $combKey;

            VehicleMaster::create([
                'variant_name' => $variantName,
                'color_name' => $colorName ?: null,
                'fuel_type' => $fuelType ?: null,
                'transmission' => $transmission ?: null,
                'ex_showroom_price' => floatval($exShowroomPrice),
                'is_active' => true,
            ]);

            $imported++;
        }

        fclose($handle);

        $msg = "Import complete. Successfully imported: {$imported} record(s). Skipped: {$skipped} record(s).";
        
        if (!empty($errors)) {
            return redirect()->route('admin.vehicle-masters.index')
                ->withSuccess($msg)
                ->with('import_errors', $errors);
        }

        return redirect()->route('admin.vehicle-masters.index')->withSuccess($msg);
    }
}
