<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMaster;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'variant_name');
        $sheet->setCellValue('B1', 'color_name');
        $sheet->setCellValue('C1', 'fuel_type');
        $sheet->setCellValue('D1', 'transmission');
        $sheet->setCellValue('E1', 'ex_showroom_price');
        // Example row
        $sheet->setCellValue('A2', 'test');
        $sheet->setCellValue('B2', 'red');
        $sheet->setCellValue('C2', 'Petrol');
        $sheet->setCellValue('D2', 'Manual');
        $sheet->setCellValue('E2', '750000.00');

        $writer = new Xls($spreadsheet);
        $path = storage_path('app/vehicle_master_template.xls');
        $writer->save($path);

        return response()->download($path, 'vehicle_master_template.xls')->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:2048',
        ]);

        $file = $request->file('csv_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, ['xls', 'xlsx'])) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            if (count($rows) < 2) {
                return redirect()->back()->withErrors(['csv_file' => 'The uploaded file is empty.']);
            }
            $header = array_map(function($h) {
                return trim(strtolower($h));
            }, array_shift($rows));
            $dataRows = $rows;
        } else {
            $handle = fopen($file->getRealPath(), 'r');
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
            $dataRows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $dataRows[] = $row;
            }
            fclose($handle);
        }

        $required = ['variant_name', 'color_name', 'fuel_type', 'transmission', 'ex_showroom_price'];
        foreach ($required as $req) {
            if (!in_array($req, $header)) {
                return redirect()->back()->withErrors(['csv_file' => "Missing required header column: {$req}"]);
            }
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowCount = 0;
        $seenInFile = [];

        foreach ($dataRows as $row) {
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

        $msg = "Import complete. Successfully imported: {$imported} record(s). Skipped: {$skipped} record(s).";
        
        if (!empty($errors)) {
            return redirect()->route('admin.vehicle-masters.index')
                ->withSuccess($msg)
                ->with('import_errors', $errors);
        }

        return redirect()->route('admin.vehicle-masters.index')->withSuccess($msg);
    }
}
