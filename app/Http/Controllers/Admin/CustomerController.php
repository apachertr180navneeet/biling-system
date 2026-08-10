<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\VehicleSalesInvoice;
use App\Models\PartSalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Customer::orderBy('name');

        if ($search) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $query->where(function($q) use ($escapedSearch) {
                $q->where('name', 'like', $escapedSearch)
                  ->orWhere('phone', 'like', $escapedSearch)
                  ->orWhere('email', 'like', $escapedSearch)
                  ->orWhere('gstin', 'like', $escapedSearch)
                  ->orWhere('company_name', 'like', $escapedSearch);
            });
        }

        $customers = $query->paginate(20);
        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $query = Customer::orderBy('name');

        if ($search) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $query->where(function($q) use ($escapedSearch) {
                $q->where('name', 'like', $escapedSearch)
                  ->orWhere('phone', 'like', $escapedSearch)
                  ->orWhere('email', 'like', $escapedSearch)
                  ->orWhere('gstin', 'like', $escapedSearch)
                  ->orWhere('company_name', 'like', $escapedSearch);
            });
        }

        $customers = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Type');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Company Name');
        $sheet->setCellValue('D1', 'Phone');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'Address');
        $sheet->setCellValue('G1', 'State');
        $sheet->setCellValue('H1', 'GSTIN');
        $sheet->setCellValue('I1', 'PAN No');
        $sheet->setCellValue('J1', 'Aadhaar No');
        $sheet->setCellValue('K1', 'Status');

        $row = 2;
        foreach ($customers as $c) {
            $sheet->setCellValue('A' . $row, $c->type);
            $sheet->setCellValue('B' . $row, $c->name);
            $sheet->setCellValue('C' . $row, $c->company_name);
            $sheet->setCellValue('D' . $row, $c->phone);
            $sheet->setCellValue('E' . $row, $c->email);
            $sheet->setCellValue('F' . $row, $c->address);
            $sheet->setCellValue('G' . $row, $c->state);
            $sheet->setCellValue('H' . $row, $c->gstin);
            $sheet->setCellValue('I' . $row, $c->pan_no);
            $sheet->setCellValue('J' . $row, $c->aadhaar_no);
            $sheet->setCellValue('K' . $row, $c->is_active ? 'Active' : 'Inactive');
            $row++;
        }

        $writer = new Xls($spreadsheet);
        $path = storage_path('app/customers_export.xls');
        $writer->save($path);

        return response()->download($path, 'customers_' . date('Ymd_His') . '.xls')->deleteFileAfterSend(true);
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|in:individual,corporate',
            'name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15',
            'pan_no' => 'nullable|string|max:10',
            'aadhaar_no' => 'nullable|string|max:12',
        ]);

        $data['type'] = $data['type'] ?? 'individual';
        $data['name'] = $data['name'] ?? ($data['company_name'] ?? 'Customer');
        $data['phone'] = !empty($data['phone']) ? $data['phone'] : null;

        try {
            $customer = Customer::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'customer' => $customer
                ]);
            }
            return redirect()->route('admin.customers.index')->withSuccess('Customer created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'type' => 'nullable|in:individual,corporate',
            'name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15',
            'pan_no' => 'nullable|string|max:10',
            'aadhaar_no' => 'nullable|string|max:12',
        ]);

        $data['type'] = $data['type'] ?? 'individual';
        $data['name'] = $data['name'] ?? ($data['company_name'] ?? 'Customer');
        $data['phone'] = !empty($data['phone']) ? $data['phone'] : null;

        try {
            $customer->update($data);
            return redirect()->route('admin.customers.index')->withSuccess('Customer updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        return response()->json(['success' => true, 'is_active' => $customer->fresh()->is_active]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'type');
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'company_name');
        $sheet->setCellValue('D1', 'phone');
        $sheet->setCellValue('E1', 'email');
        $sheet->setCellValue('F1', 'address');
        $sheet->setCellValue('G1', 'state');
        $sheet->setCellValue('H1', 'gstin');
        $sheet->setCellValue('I1', 'pan_no');
        $sheet->setCellValue('J1', 'aadhaar_no');
        
        // Example row
        $sheet->setCellValue('A2', 'individual');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', '');
        $sheet->setCellValue('D2', '9876543210');
        $sheet->setCellValue('E2', 'john@example.com');
        $sheet->setCellValue('F2', '456 Elm Street');
        $sheet->setCellValue('G2', 'Maharashtra');
        $sheet->setCellValue('H2', '');
        $sheet->setCellValue('I2', 'ABCDE1234F');
        $sheet->setCellValue('J2', '123456789012');

        $writer = new Xls($spreadsheet);
        $path = storage_path('app/customer_template.xls');
        $writer->save($path);

        return response()->download($path, 'customer_template.xls')->deleteFileAfterSend(true);
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

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowCount = 0;
        $seenInFile = [];

        foreach ($dataRows as $row) {
            $rowCount++;
            if (count(array_filter($row)) === 0) {
                continue;
            }
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            } elseif (count($row) > count($header)) {
                $row = array_slice($row, 0, count($header));
            }

            $data = array_combine($header, $row);
            
            $type = isset($data['type']) && !empty(trim($data['type'])) ? trim($data['type']) : 'individual';
            $name = isset($data['name']) ? trim($data['name']) : '';
            if (empty($name) && isset($data['first_name'])) {
                $name = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
            }
            $companyName = isset($data['company_name']) ? trim($data['company_name']) : '';
            if (empty($name)) {
                $name = $companyName ?: 'Customer';
            }
            $phone = isset($data['phone']) ? trim($data['phone']) : '';
            $email = isset($data['email']) ? trim($data['email']) : '';
            $address = isset($data['address']) ? trim($data['address']) : '';
            $state = isset($data['state']) ? trim($data['state']) : '';
            $gstin = isset($data['gstin']) ? trim($data['gstin']) : '';
            $panNo = isset($data['pan_no']) ? trim($data['pan_no']) : '';
            $aadhaarNo = isset($data['aadhaar_no']) ? trim($data['aadhaar_no']) : '';

            if (!empty($phone)) {
                $phoneKey = strtolower($phone);
                if (in_array($phoneKey, $seenInFile)) {
                    $errors[] = "Row {$rowCount}: Duplicate Phone '{$phone}' in the CSV file.";
                    $skipped++;
                    continue;
                }
                $exists = Customer::where('phone', $phone)->exists();
                if ($exists) {
                    $errors[] = "Row {$rowCount}: Customer with Phone '{$phone}' already exists in the database.";
                    $skipped++;
                    continue;
                }
                $seenInFile[] = $phoneKey;
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowCount}: Email format is invalid.";
                $skipped++;
                continue;
            }

            Customer::create([
                'type' => in_array($type, ['individual', 'corporate']) ? $type : 'individual',
                'name' => $name,
                'company_name' => $companyName ?: null,
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'address' => $address ?: null,
                'state' => $state ?: null,
                'gstin' => $gstin ?: null,
                'pan_no' => $panNo ?: null,
                'aadhaar_no' => $aadhaarNo ?: null,
                'is_active' => true,
            ]);

            $imported++;
        }

        $msg = "Import complete. Successfully imported: {$imported} record(s). Skipped: {$skipped} record(s).";
        
        if (!empty($errors)) {
            return redirect()->route('admin.customers.index')
                ->withSuccess($msg)
                ->with('import_errors', $errors);
        }

        return redirect()->route('admin.customers.index')->withSuccess($msg);
    }

    public function ledgerSummary(Request $request)
    {
        $customerId = $request->input('customer_id');
        $customerName = trim($request->input('customer_name', ''));
        $customerMobile = trim($request->input('customer_mobile', ''));

        if (!$customerId && empty($customerName) && empty($customerMobile)) {
            return response()->json([
                'success' => false,
                'message' => 'No customer parameter provided.'
            ]);
        }

        $customer = null;
        if ($customerId) {
            $customer = Customer::find($customerId);
        }
        if (!$customer && !empty($customerMobile)) {
            $customer = Customer::where('phone', $customerMobile)->first();
        }
        if (!$customer && !empty($customerName)) {
            $customer = Customer::where(DB::raw('LOWER(name)'), strtolower($customerName))->first();
        }

        // Build Vehicle Sales Invoices Query
        $vehicleQuery = VehicleSalesInvoice::query();
        if ($customer) {
            $vehicleQuery->where(function($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
                if ($customer->phone) {
                    $q->orWhere('customer_mobile', $customer->phone);
                }
            });
        } else {
            $vehicleQuery->where(function($q) use ($customerName, $customerMobile) {
                if (!empty($customerName)) {
                    $q->where(DB::raw('LOWER(customer_name)'), strtolower($customerName));
                }
                if (!empty($customerMobile)) {
                    $q->orWhere('customer_mobile', $customerMobile);
                }
            });
        }

        // Build Part Sales Invoices Query
        $partQuery = PartSalesInvoice::query();
        if ($customer) {
            $partQuery->where(function($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
                if ($customer->phone) {
                    $q->orWhere('customer_mobile', $customer->phone);
                }
            });
        } else {
            $partQuery->where(function($q) use ($customerName, $customerMobile) {
                if (!empty($customerName)) {
                    $q->where(DB::raw('LOWER(customer_name)'), strtolower($customerName));
                }
                if (!empty($customerMobile)) {
                    $q->orWhere('customer_mobile', $customerMobile);
                }
            });
        }

        $totalVehicleBilled = (float)$vehicleQuery->sum('grand_total');
        $totalVehiclePaid   = (float)$vehicleQuery->sum('received_amount');
        $totalVehicleBal    = (float)$vehicleQuery->sum('balance');
        $vehicleCount       = $vehicleQuery->count();

        $totalPartBilled = (float)$partQuery->sum('total_amount');
        $totalPartPaid   = (float)$partQuery->sum('received_amount');
        $totalPartBal    = (float)$partQuery->sum('balance');
        $partCount       = $partQuery->count();

        $totalAmount = $totalVehicleBilled + $totalPartBilled;
        $paidAmount  = $totalVehiclePaid + $totalPartPaid;
        $outstanding = $totalVehicleBal + $totalPartBal;
        $totalInvoices = $vehicleCount + $partCount;

        $ledgerUrl = null;
        if ($customer) {
            $ledgerUrl = route('admin.customers.ledger', $customer);
        } else {
            $ledgerUrl = route('admin.customers.ledger-search', ['search' => $customerName ?: $customerMobile]);
        }

        return response()->json([
            'success' => true,
            'customer_id' => $customer ? $customer->id : null,
            'customer_name' => $customer ? $customer->name : ($customerName ?: 'Customer'),
            'total_amount' => number_format($totalAmount, 2, '.', ''),
            'paid_amount' => number_format($paidAmount, 2, '.', ''),
            'outstanding_balance' => number_format($outstanding, 2, '.', ''),
            'total_invoices' => $totalInvoices,
            'ledger_url' => $ledgerUrl,
        ]);
    }

    public function ledger(Customer $customer, Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date', date('Y-m-d')); // Default to today
        $type = $request->input('type', 'all'); // 'all', 'vehicle', 'part'
        $paymentStatus = $request->input('payment_status', 'all'); // 'all', 'paid', 'pending'
        $search = $request->input('search');

        // Vehicle Invoices Query
        $vehicleInvoices = collect();
        if ($type === 'all' || $type === 'vehicle') {
            $vQuery = VehicleSalesInvoice::with('vehicleInventory')
                ->where(function($q) use ($customer) {
                    $q->where('customer_id', $customer->id)
                      ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
                    if ($customer->phone) {
                        $q->orWhere('customer_mobile', $customer->phone);
                    }
                });

            if ($fromDate) {
                $vQuery->whereDate('invoice_date', '>=', $fromDate);
            }
            if ($toDate) {
                $vQuery->whereDate('invoice_date', '<=', $toDate);
            }
            if ($paymentStatus === 'paid') {
                $vQuery->where('balance', '<=', 0);
            } elseif ($paymentStatus === 'pending') {
                $vQuery->where('balance', '>', 0);
            }
            if ($search) {
                $escaped = '%' . addcslashes($search, '%_') . '%';
                $vQuery->where(function($sq) use ($escaped) {
                    $sq->where('invoice_number', 'like', $escaped)
                       ->orWhereHas('vehicleInventory', function($vq) use ($escaped) {
                           $vq->where('vehicle_description', 'like', $escaped)
                              ->orWhere('chassis_number', 'like', $escaped);
                       });
                });
            }

            $vehicleInvoices = $vQuery->get();
        }

        // Part Invoices Query
        $partInvoices = collect();
        if ($type === 'all' || $type === 'part') {
            $pQuery = PartSalesInvoice::with('items.sparePart')
                ->where(function($q) use ($customer) {
                    $q->where('customer_id', $customer->id)
                      ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
                    if ($customer->phone) {
                        $q->orWhere('customer_mobile', $customer->phone);
                    }
                });

            if ($fromDate) {
                $pQuery->whereDate('invoice_date', '>=', $fromDate);
            }
            if ($toDate) {
                $pQuery->whereDate('invoice_date', '<=', $toDate);
            }
            if ($paymentStatus === 'paid') {
                $pQuery->where('balance', '<=', 0);
            } elseif ($paymentStatus === 'pending') {
                $pQuery->where('balance', '>', 0);
            }
            if ($search) {
                $escaped = '%' . addcslashes($search, '%_') . '%';
                $pQuery->where(function($sq) use ($escaped) {
                    $sq->where('invoice_number', 'like', $escaped)
                       ->orWhereHas('items.sparePart', function($spq) use ($escaped) {
                           $spq->where('name', 'like', $escaped)
                               ->orWhere('part_no', 'like', $escaped);
                       });
                });
            }

            $partInvoices = $pQuery->get();
        }

        // Combine into unified transaction list
        $transactions = collect();

        foreach ($vehicleInvoices as $v) {
            $itemDesc = $v->vehicleInventory ? ($v->vehicleInventory->vehicle_description . ' (Chassis: ' . $v->vehicleInventory->chassis_number . ')') : 'Vehicle Sale';
            $transactions->push([
                'id' => $v->id,
                'doc_type' => 'vehicle',
                'doc_label' => 'Vehicle Invoice',
                'doc_number' => $v->invoice_number,
                'doc_date' => $v->invoice_date ? $v->invoice_date->format('Y-m-d') : '',
                'particulars' => $itemDesc,
                'total_amount' => (float)$v->grand_total,
                'received_amount' => (float)$v->received_amount,
                'balance' => (float)$v->balance,
                'payment_mode' => $v->payment_mode ?? 'Cash',
                'created_at' => $v->created_at,
                'raw_model' => $v,
                'pdf_url' => route('admin.vehicle-sales-invoices.pdf', $v),
                'show_url' => route('admin.vehicle-sales-invoices.show', $v),
                'payment_url' => route('admin.vehicle-sales-invoices.receive-payment', $v),
            ]);
        }

        foreach ($partInvoices as $p) {
            $itemNames = [];
            foreach ($p->items as $item) {
                $name = $item->sparePart ? $item->sparePart->name : ($item->item_name ?? 'Spare Part');
                $itemNames[] = $name . ' x' . $item->quantity;
            }
            $itemDesc = !empty($itemNames) ? implode(', ', $itemNames) : 'Spare Parts Sale';
            $transactions->push([
                'id' => $p->id,
                'doc_type' => 'part',
                'doc_label' => 'Part Invoice',
                'doc_number' => $p->invoice_number,
                'doc_date' => $p->invoice_date ? $p->invoice_date->format('Y-m-d') : '',
                'particulars' => $itemDesc,
                'total_amount' => (float)$p->total_amount,
                'received_amount' => (float)$p->received_amount,
                'balance' => (float)$p->balance,
                'payment_mode' => $p->payment_mode ?? 'Cash',
                'created_at' => $p->created_at,
                'raw_model' => $p,
                'pdf_url' => route('admin.part-sales-invoices.pdf', $p),
                'show_url' => route('admin.part-sales-invoices.show', $p),
                'payment_url' => route('admin.part-sales-invoices.receive-payment', $p),
            ]);
        }

        // Sort by invoice date descending
        $transactions = $transactions->sortByDesc(function($item) {
            return $item['doc_date'] . ' ' . $item['created_at'];
        })->values();

        // Overall Summaries up to today
        $allVehicleQuery = VehicleSalesInvoice::where(function($q) use ($customer) {
            $q->where('customer_id', $customer->id)
              ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
            if ($customer->phone) {
                $q->orWhere('customer_mobile', $customer->phone);
            }
        });
        if ($toDate) {
            $allVehicleQuery->whereDate('invoice_date', '<=', $toDate);
        }

        $allPartQuery = PartSalesInvoice::where(function($q) use ($customer) {
            $q->where('customer_id', $customer->id)
              ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
            if ($customer->phone) {
                $q->orWhere('customer_mobile', $customer->phone);
            }
        });
        if ($toDate) {
            $allPartQuery->whereDate('invoice_date', '<=', $toDate);
        }

        $totalBilled = (float)$allVehicleQuery->sum('grand_total') + (float)$allPartQuery->sum('total_amount');
        $totalPaid   = (float)$allVehicleQuery->sum('received_amount') + (float)$allPartQuery->sum('received_amount');
        $totalOutstanding = (float)$allVehicleQuery->sum('balance') + (float)$allPartQuery->sum('balance');
        $totalInvoices = $allVehicleQuery->count() + $allPartQuery->count();

        return view('admin.customers.ledger', compact(
            'customer',
            'transactions',
            'totalBilled',
            'totalPaid',
            'totalOutstanding',
            'totalInvoices',
            'fromDate',
            'toDate',
            'type',
            'paymentStatus',
            'search'
        ));
    }

    public function ledgerSearch(Request $request)
    {
        $search = trim($request->input('search', ''));
        if (!empty($search)) {
            $customer = Customer::where('phone', $search)
                ->orWhere('name', 'like', '%' . addcslashes($search, '%_') . '%')
                ->orWhere('company_name', 'like', '%' . addcslashes($search, '%_') . '%')
                ->first();

            if ($customer) {
                return redirect()->route('admin.customers.ledger', $customer);
            }
        }

        return redirect()->route('admin.customers.index', ['search' => $search])->with('error', 'Customer not found. Please select or create a customer.');
    }

    public function exportLedger(Customer $customer, Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date', date('Y-m-d'));

        // Vehicle Invoices Query
        $vQuery = VehicleSalesInvoice::with('vehicleInventory')
            ->where(function($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
                if ($customer->phone) {
                    $q->orWhere('customer_mobile', $customer->phone);
                }
            });
        if ($fromDate) $vQuery->whereDate('invoice_date', '>=', $fromDate);
        if ($toDate) $vQuery->whereDate('invoice_date', '<=', $toDate);
        $vInvoices = $vQuery->get();

        // Part Invoices Query
        $pQuery = PartSalesInvoice::with('items.sparePart')
            ->where(function($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere(DB::raw('LOWER(customer_name)'), strtolower($customer->name));
                if ($customer->phone) {
                    $q->orWhere('customer_mobile', $customer->phone);
                }
            });
        if ($fromDate) $pQuery->whereDate('invoice_date', '>=', $fromDate);
        if ($toDate) $pQuery->whereDate('invoice_date', '<=', $toDate);
        $pInvoices = $pQuery->get();

        $rows = collect();
        foreach ($vInvoices as $v) {
            $itemDesc = $v->vehicleInventory ? ($v->vehicleInventory->vehicle_description . ' (Chassis: ' . $v->vehicleInventory->chassis_number . ')') : 'Vehicle Sale';
            $rows->push([
                'date' => $v->invoice_date ? $v->invoice_date->format('Y-m-d') : '',
                'type' => 'Vehicle Invoice',
                'number' => $v->invoice_number,
                'particulars' => $itemDesc,
                'mode' => $v->payment_mode ?? 'Cash',
                'total' => (float)$v->grand_total,
                'received' => (float)$v->received_amount,
                'balance' => (float)$v->balance,
                'status' => $v->balance <= 0 ? 'PAID' : ($v->received_amount > 0 ? 'PARTIAL' : 'UNPAID'),
            ]);
        }
        foreach ($pInvoices as $p) {
            $itemNames = [];
            foreach ($p->items as $item) {
                $name = $item->sparePart ? $item->sparePart->name : ($item->item_name ?? 'Spare Part');
                $itemNames[] = $name . ' x' . $item->quantity;
            }
            $itemDesc = !empty($itemNames) ? implode(', ', $itemNames) : 'Spare Parts Sale';
            $rows->push([
                'date' => $p->invoice_date ? $p->invoice_date->format('Y-m-d') : '',
                'type' => 'Part Invoice',
                'number' => $p->invoice_number,
                'particulars' => $itemDesc,
                'mode' => $p->payment_mode ?? 'Cash',
                'total' => (float)$p->total_amount,
                'received' => (float)$p->received_amount,
                'balance' => (float)$p->balance,
                'status' => $p->balance <= 0 ? 'PAID' : ($p->received_amount > 0 ? 'PARTIAL' : 'UNPAID'),
            ]);
        }

        $rows = $rows->sortByDesc('date')->values();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Ledger');

        // Header info
        $sheet->setCellValue('A1', 'CUSTOMER LEDGER STATEMENT');
        $sheet->setCellValue('A2', 'Customer Name: ' . $customer->name);
        $sheet->setCellValue('A3', 'Phone: ' . ($customer->phone ?? 'N/A') . ' | GSTIN: ' . ($customer->gstin ?? 'N/A'));
        $sheet->setCellValue('A4', 'Statement Period: ' . ($fromDate ?: 'Beginning') . ' to ' . $toDate);

        // Table headers
        $sheet->setCellValue('A6', 'Date');
        $sheet->setCellValue('B6', 'Doc Type');
        $sheet->setCellValue('C6', 'Invoice No');
        $sheet->setCellValue('D6', 'Particulars / Details');
        $sheet->setCellValue('E6', 'Payment Mode');
        $sheet->setCellValue('F6', 'Total Amount (₹)');
        $sheet->setCellValue('G6', 'Received Amount (₹)');
        $sheet->setCellValue('H6', 'Outstanding Balance (₹)');
        $sheet->setCellValue('I6', 'Status');

        $r = 7;
        foreach ($rows as $item) {
            $sheet->setCellValue('A' . $r, $item['date']);
            $sheet->setCellValue('B' . $r, $item['type']);
            $sheet->setCellValue('C' . $r, $item['number']);
            $sheet->setCellValue('D' . $r, $item['particulars']);
            $sheet->setCellValue('E' . $r, $item['mode']);
            $sheet->setCellValue('F' . $r, $item['total']);
            $sheet->setCellValue('G' . $r, $item['received']);
            $sheet->setCellValue('H' . $r, $item['balance']);
            $sheet->setCellValue('I' . $r, $item['status']);
            $r++;
        }

        $writer = new Xls($spreadsheet);
        $filename = 'customer_ledger_' . str_replace(' ', '_', strtolower($customer->name)) . '_' . date('Ymd_His') . '.xls';
        $path = storage_path('app/' . $filename);
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}
