<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->orderBy('name')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'labor_charge' => 'required|numeric|min:0',
        ]);
        Service::create($data);
        return redirect()->route('admin.services.index')->withSuccess('Service created successfully.');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'labor_charge' => 'required|numeric|min:0',
        ]);
        $service->update($data);
        return redirect()->route('admin.services.index')->withSuccess('Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return response()->json(['success' => true, 'is_active' => $service->is_active]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'category_name');
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'description');
        $sheet->setCellValue('D1', 'labor_charge');
        
        // Example row
        $sheet->setCellValue('A2', 'General Service');
        $sheet->setCellValue('B2', 'Oil Change & Filter');
        $sheet->setCellValue('C2', 'Basic service package including oil swap and filter replacement');
        $sheet->setCellValue('D2', '450.00');

        $writer = new Xls($spreadsheet);
        $path = storage_path('app/service_template.xls');
        $writer->save($path);

        return response()->download($path, 'service_template.xls')->deleteFileAfterSend(true);
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

        $required = ['category_name', 'name', 'labor_charge'];
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
            
            $categoryName = isset($data['category_name']) ? trim($data['category_name']) : '';
            $name = isset($data['name']) ? trim($data['name']) : '';
            $description = isset($data['description']) ? trim($data['description']) : '';
            $laborCharge = isset($data['labor_charge']) ? trim($data['labor_charge']) : '0';

            if (empty($categoryName) || empty($name)) {
                $errors[] = "Row {$rowCount}: Category Name and Service Name are required.";
                $skipped++;
                continue;
            }

            if (!is_numeric($laborCharge) || floatval($laborCharge) < 0) {
                $errors[] = "Row {$rowCount}: Labor Charge must be a positive number.";
                $skipped++;
                continue;
            }

            $serviceKey = strtolower($name) . '|' . strtolower($categoryName);

            if (in_array($serviceKey, $seenInFile)) {
                $errors[] = "Row {$rowCount}: Duplicate Service name '{$name}' for category '{$categoryName}' in the CSV file.";
                $skipped++;
                continue;
            }

            // Find or create category
            $category = ServiceCategory::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
            if (!$category) {
                $category = ServiceCategory::create([
                    'name' => $categoryName,
                    'is_active' => true
                ]);
            }

            $exists = Service::where('service_category_id', $category->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->exists();

            if ($exists) {
                $errors[] = "Row {$rowCount}: Service '{$name}' for category '{$categoryName}' already exists in the database.";
                $skipped++;
                continue;
            }

            $seenInFile[] = $serviceKey;

            Service::create([
                'service_category_id' => $category->id,
                'name' => $name,
                'description' => $description ?: null,
                'labor_charge' => floatval($laborCharge),
                'is_active' => true,
            ]);

            $imported++;
        }

        $msg = "Import complete. Successfully imported: {$imported} record(s). Skipped: {$skipped} record(s).";
        
        if (!empty($errors)) {
            return redirect()->route('admin.services.index')
                ->withSuccess($msg)
                ->with('import_errors', $errors);
        }

        return redirect()->route('admin.services.index')->withSuccess($msg);
    }
}
