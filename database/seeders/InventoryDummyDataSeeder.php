<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use App\Models\InventoryItem;
use App\Models\InventorySupplier;
use App\Models\InventoryPurchaseOrder;
use App\Models\InventoryPurchaseOrderItem;
use App\Models\InventoryGrn;
use App\Models\InventoryGrnItem;
use App\Models\InventoryStock;
use App\Models\InventoryStockTransfer;
use App\Models\InventoryStockTransferItem;
use App\Models\InventoryStockAdjustment;
use App\Models\Hotel;
use App\Models\User;
use App\Helpers\Helper;
use Carbon\Carbon;

class InventoryDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found.');
            return;
        }

        $users = User::where('status', 'active')->get();

        // ── Categories ──
        $categoriesData = [
            ['name' => 'Food & Beverage', 'description' => 'All food and beverage items for restaurant and room service'],
            ['name' => 'Housekeeping Supplies', 'description' => 'Cleaning agents, toiletries, and room supplies'],
            ['name' => 'Kitchen Equipment', 'description' => 'Kitchen utensils, cookware, and small equipment'],
            ['name' => 'Linen & Textiles', 'description' => 'Bed linen, towels, tablecloths, and napkins'],
            ['name' => 'Maintenance & Repair', 'description' => 'Spare parts, tools, and maintenance supplies'],
            ['name' => 'Office Supplies', 'description' => 'Stationery, printers, and general office items'],
            ['name' => 'Guest Amenities', 'description' => 'Toiletries, slippers, robes, and guest comfort items'],
            ['name' => 'Beverages (Non-Alcoholic)', 'description' => 'Soft drinks, juices, water, coffee, and tea'],
            ['name' => 'Alcoholic Beverages', 'description' => 'Wine, beer, spirits, and cocktails'],
            ['name' => 'Frozen & Dairy', 'description' => 'Frozen foods, dairy products, and ice cream'],
        ];

        $categories = collect();
        foreach ($categoriesData as $cat) {
            $categories->push(InventoryCategory::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Helper::slug('inventory_categories', $cat['name']),
                    'description' => $cat['description'],
                    'status' => 'active',
                ]
            ));
        }

        // ── Units ──
        $unitsData = [
            ['name' => 'Kilogram', 'short_name' => 'Kg'],
            ['name' => 'Gram', 'short_name' => 'Gm'],
            ['name' => 'Litre', 'short_name' => 'L'],
            ['name' => 'Millilitre', 'short_name' => 'Ml'],
            ['name' => 'Piece', 'short_name' => 'Pc'],
            ['name' => 'Box', 'short_name' => 'Box'],
            ['name' => 'Pack', 'short_name' => 'Pkt'],
            ['name' => 'Dozen', 'short_name' => 'Dz'],
            ['name' => 'Case', 'short_name' => 'Cs'],
            ['name' => 'Bottle', 'short_name' => 'Btl'],
            ['name' => 'Carton', 'short_name' => 'Ctn'],
            ['name' => 'Bag', 'short_name' => 'Bag'],
            ['name' => 'Pair', 'short_name' => 'Pr'],
            ['name' => 'Roll', 'short_name' => 'Rl'],
            ['name' => 'Set', 'short_name' => 'Set'],
        ];

        $units = collect();
        foreach ($unitsData as $u) {
            $units->push(InventoryUnit::firstOrCreate(
                ['name' => $u['name']],
                [
                    'slug' => Helper::slug('inventory_units', $u['name']),
                    'short_name' => $u['short_name'],
                    'status' => 'active',
                ]
            ));
        }

        // ── Items ──
        $itemsData = [
            // Food & Beverage
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Basmati Rice', 'sku' => 'FNB-001', 'cost' => 120, 'sell' => 0, 'min' => 50, 'max' => 200],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Chicken Breast', 'sku' => 'FNB-002', 'cost' => 350, 'sell' => 0, 'min' => 20, 'max' => 80],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Paneer', 'sku' => 'FNB-003', 'cost' => 280, 'sell' => 0, 'min' => 10, 'max' => 50],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Onion', 'sku' => 'FNB-004', 'cost' => 40, 'sell' => 0, 'min' => 30, 'max' => 100],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Tomato', 'sku' => 'FNB-005', 'cost' => 50, 'sell' => 0, 'min' => 20, 'max' => 80],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Potato', 'sku' => 'FNB-006', 'cost' => 35, 'sell' => 0, 'min' => 30, 'max' => 100],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Mutton', 'sku' => 'FNB-007', 'cost' => 800, 'sell' => 0, 'min' => 10, 'max' => 40],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Fish (Rohu)', 'sku' => 'FNB-008', 'cost' => 400, 'sell' => 0, 'min' => 10, 'max' => 30],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Prawns', 'sku' => 'FNB-009', 'cost' => 900, 'sell' => 0, 'min' => 5, 'max' => 20],
            ['cat' => 'Food & Beverage', 'unit' => 'L', 'name' => 'Cooking Oil', 'sku' => 'FNB-010', 'cost' => 150, 'sell' => 0, 'min' => 20, 'max' => 60],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Maida (Flour)', 'sku' => 'FNB-011', 'cost' => 45, 'sell' => 0, 'min' => 25, 'max' => 80],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Sugar', 'sku' => 'FNB-012', 'cost' => 55, 'sell' => 0, 'min' => 20, 'max' => 60],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Salt', 'sku' => 'FNB-013', 'cost' => 25, 'sell' => 0, 'min' => 10, 'max' => 30],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Turmeric Powder', 'sku' => 'FNB-014', 'cost' => 200, 'sell' => 0, 'min' => 5, 'max' => 15],
            ['cat' => 'Food & Beverage', 'unit' => 'Kg', 'name' => 'Chilli Powder', 'sku' => 'FNB-015', 'cost' => 250, 'sell' => 0, 'min' => 5, 'max' => 15],

            // Housekeeping Supplies
            ['cat' => 'Housekeeping Supplies', 'unit' => 'L', 'name' => 'Floor Cleaner', 'sku' => 'HK-001', 'cost' => 180, 'sell' => 0, 'min' => 10, 'max' => 40],
            ['cat' => 'Housekeeping Supplies', 'unit' => 'L', 'name' => 'Glass Cleaner', 'sku' => 'HK-002', 'cost' => 160, 'sell' => 0, 'min' => 8, 'max' => 30],
            ['cat' => 'Housekeeping Supplies', 'unit' => 'L', 'name' => 'Toilet Cleaner', 'sku' => 'HK-003', 'cost' => 120, 'sell' => 0, 'min' => 10, 'max' => 40],
            ['cat' => 'Housekeeping Supplies', 'unit' => 'Kg', 'name' => 'Detergent Powder', 'sku' => 'HK-004', 'cost' => 90, 'sell' => 0, 'min' => 20, 'max' => 60],
            ['cat' => 'Housekeeping Supplies', 'unit' => 'L', 'name' => 'Fabric Softener', 'sku' => 'HK-005', 'cost' => 220, 'sell' => 0, 'min' => 5, 'max' => 20],
            ['cat' => 'Housekeeping Supplies', 'unit' => 'Pack', 'name' => 'Garbage Bags (Large)', 'sku' => 'HK-006', 'cost' => 350, 'sell' => 0, 'min' => 5, 'max' => 20],
            ['cat' => 'Housekeeping Supplies', 'unit' => 'Pack', 'name' => 'Garbage Bags (Small)', 'sku' => 'HK-007', 'cost' => 200, 'sell' => 0, 'min' => 5, 'max' => 20],
            ['cat' => 'Housekeeping Supplies', 'unit' => 'Piece', 'name' => 'Mop Head', 'sku' => 'HK-008', 'cost' => 150, 'sell' => 0, 'min' => 10, 'max' => 30],

            // Guest Amenities
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Shampoo (30ml)', 'sku' => 'GA-001', 'cost' => 15, 'sell' => 0, 'min' => 200, 'max' => 800],
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Conditioner (30ml)', 'sku' => 'GA-002', 'cost' => 18, 'sell' => 0, 'min' => 200, 'max' => 800],
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Body Wash (30ml)', 'sku' => 'GA-003', 'cost' => 16, 'sell' => 0, 'min' => 200, 'max' => 800],
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Body Lotion (30ml)', 'sku' => 'GA-004', 'cost' => 20, 'sell' => 0, 'min' => 200, 'max' => 800],
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Dental Kit', 'sku' => 'GA-005', 'cost' => 25, 'sell' => 0, 'min' => 300, 'max' => 1000],
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Shaving Kit', 'sku' => 'GA-006', 'cost' => 30, 'sell' => 0, 'min' => 100, 'max' => 400],
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Slippers (Pair)', 'sku' => 'GA-007', 'cost' => 45, 'sell' => 0, 'min' => 100, 'max' => 400],
            ['cat' => 'Guest Amenities', 'unit' => 'Piece', 'name' => 'Sewing Kit', 'sku' => 'GA-008', 'cost' => 10, 'sell' => 0, 'min' => 200, 'max' => 600],

            // Beverages (Non-Alcoholic)
            ['cat' => 'Beverages (Non-Alcoholic)', 'unit' => 'Bottle', 'name' => 'Mineral Water (1L)', 'sku' => 'BNA-001', 'cost' => 15, 'sell' => 50, 'min' => 100, 'max' => 500],
            ['cat' => 'Beverages (Non-Alcoholic)', 'unit' => 'Bottle', 'name' => 'Coca Cola (300ml)', 'sku' => 'BNA-002', 'cost' => 20, 'sell' => 60, 'min' => 50, 'max' => 200],
            ['cat' => 'Beverages (Non-Alcoholic)', 'unit' => 'Bottle', 'name' => 'Sprite (300ml)', 'sku' => 'BNA-003', 'cost' => 20, 'sell' => 60, 'min' => 50, 'max' => 200],
            ['cat' => 'Beverages (Non-Alcoholic)', 'unit' => 'Bottle', 'name' => 'Fresh Lime Juice', 'sku' => 'BNA-004', 'cost' => 25, 'sell' => 80, 'min' => 30, 'max' => 100],
            ['cat' => 'Beverages (Non-Alcoholic)', 'unit' => 'Kg', 'name' => 'Coffee Beans (Arabica)', 'sku' => 'BNA-005', 'cost' => 1200, 'sell' => 0, 'min' => 5, 'max' => 20],
            ['cat' => 'Beverages (Non-Alcoholic)', 'unit' => 'Kg', 'name' => 'Tea Leaves (Premium)', 'sku' => 'BNA-006', 'cost' => 600, 'sell' => 0, 'min' => 5, 'max' => 15],

            // Alcoholic Beverages
            ['cat' => 'Alcoholic Beverages', 'unit' => 'Bottle', 'name' => 'Kingfisher Beer (650ml)', 'sku' => 'ALC-001', 'cost' => 120, 'sell' => 300, 'min' => 24, 'max' => 100],
            ['cat' => 'Alcoholic Beverages', 'unit' => 'Bottle', 'name' => 'Bacardi White Rum (750ml)', 'sku' => 'ALC-002', 'cost' => 800, 'sell' => 1800, 'min' => 6, 'max' => 24],
            ['cat' => 'Alcoholic Beverages', 'unit' => 'Bottle', 'name' => 'Blenders Pride Whisky (750ml)', 'sku' => 'ALC-003', 'cost' => 900, 'sell' => 2000, 'min' => 6, 'max' => 24],
            ['cat' => 'Alcoholic Beverages', 'unit' => 'Bottle', 'name' => 'Absolut Vodka (750ml)', 'sku' => 'ALC-004', 'cost' => 1500, 'sell' => 3200, 'min' => 6, 'max' => 18],
            ['cat' => 'Alcoholic Beverages', 'unit' => 'Bottle', 'name' => 'Sula Red Wine (750ml)', 'sku' => 'ALC-005', 'cost' => 500, 'sell' => 1200, 'min' => 12, 'max' => 48],

            // Frozen & Dairy
            ['cat' => 'Frozen & Dairy', 'unit' => 'L', 'name' => 'Amul Milk (Toned)', 'sku' => 'FD-001', 'cost' => 56, 'sell' => 0, 'min' => 20, 'max' => 60],
            ['cat' => 'Frozen & Dairy', 'unit' => 'Kg', 'name' => 'Butter (Amul)', 'sku' => 'FD-002', 'cost' => 500, 'sell' => 0, 'min' => 5, 'max' => 20],
            ['cat' => 'Frozen & Dairy', 'unit' => 'Kg', 'name' => 'Cheese (Mozzarella)', 'sku' => 'FD-003', 'cost' => 400, 'sell' => 0, 'min' => 5, 'max' => 15],
            ['cat' => 'Frozen & Dairy', 'unit' => 'L', 'name' => 'Fresh Cream', 'sku' => 'FD-004', 'cost' => 180, 'sell' => 0, 'min' => 5, 'max' => 15],
            ['cat' => 'Frozen & Dairy', 'unit' => 'Kg', 'name' => 'Ice Cream (Vanilla)', 'sku' => 'FD-005', 'cost' => 250, 'sell' => 0, 'min' => 5, 'max' => 20],

            // Kitchen Equipment
            ['cat' => 'Kitchen Equipment', 'unit' => 'Piece', 'name' => 'Chef Knife (8")', 'sku' => 'KE-001', 'cost' => 800, 'sell' => 0, 'min' => 2, 'max' => 8],
            ['cat' => 'Kitchen Equipment', 'unit' => 'Piece', 'name' => 'Non-Stick Pan (12")', 'sku' => 'KE-002', 'cost' => 1200, 'sell' => 0, 'min' => 2, 'max' => 6],
            ['cat' => 'Kitchen Equipment', 'unit' => 'Piece', 'name' => 'Stock Pot (20L)', 'sku' => 'KE-003', 'cost' => 2500, 'sell' => 0, 'min' => 1, 'max' => 4],

            // Linen & Textiles
            ['cat' => 'Linen & Textiles', 'unit' => 'Piece', 'name' => 'Bath Towel', 'sku' => 'LT-001', 'cost' => 350, 'sell' => 0, 'min' => 50, 'max' => 200],
            ['cat' => 'Linen & Textiles', 'unit' => 'Piece', 'name' => 'Hand Towel', 'sku' => 'LT-002', 'cost' => 200, 'sell' => 0, 'min' => 50, 'max' => 200],
            ['cat' => 'Linen & Textiles', 'unit' => 'Piece', 'name' => 'Bed Sheet (King)', 'sku' => 'LT-003', 'cost' => 800, 'sell' => 0, 'min' => 30, 'max' => 120],
            ['cat' => 'Linen & Textiles', 'unit' => 'Piece', 'name' => 'Pillow Cover', 'sku' => 'LT-004', 'cost' => 250, 'sell' => 0, 'min' => 60, 'max' => 240],

            // Maintenance & Repair
            ['cat' => 'Maintenance & Repair', 'unit' => 'Piece', 'name' => 'LED Bulb (9W)', 'sku' => 'MR-001', 'cost' => 80, 'sell' => 0, 'min' => 20, 'max' => 60],
            ['cat' => 'Maintenance & Repair', 'unit' => 'Piece', 'name' => 'AC Filter', 'sku' => 'MR-002', 'cost' => 350, 'sell' => 0, 'min' => 5, 'max' => 20],
            ['cat' => 'Maintenance & Repair', 'unit' => 'Piece', 'name' => 'Plumbing Washer', 'sku' => 'MR-003', 'cost' => 15, 'sell' => 0, 'min' => 50, 'max' => 200],
            ['cat' => 'Maintenance & Repair', 'unit' => 'Roll', 'name' => 'Teflon Tape', 'sku' => 'MR-004', 'cost' => 25, 'sell' => 0, 'min' => 20, 'max' => 60],
        ];

        $items = collect();
        foreach ($itemsData as $itemData) {
            $cat = $categories->firstWhere('name', $itemData['cat']);
            $unit = $units->firstWhere('short_name', $itemData['unit']) ?? $units->firstWhere('name', $itemData['unit']);

            $items->push(InventoryItem::firstOrCreate(
                ['sku' => $itemData['sku']],
                [
                    'hotel_id' => $hotel->id,
                    'category_id' => $cat->id,
                    'unit_id' => $unit->id,
                    'name' => $itemData['name'],
                    'slug' => Helper::slug('inventory_items', $itemData['name']),
                    'description' => 'Standard inventory item',
                    'cost_price' => $itemData['cost'],
                    'sell_price' => $itemData['sell'],
                    'min_stock' => $itemData['min'],
                    'max_stock' => $itemData['max'],
                    'is_reorder' => $itemData['min'] > 50,
                    'status' => 'active',
                ]
            ));
        }

        // ── Suppliers ──
        $suppliersData = [
            ['name' => 'Fresh Farms Supply Co.', 'contact' => 'Rajesh Kumar', 'email' => 'rajesh@freshfarms.com', 'phone' => '9876543210', 'address' => '123 Market Road, Mumbai', 'gst' => '27AABCT1234F1Z5', 'terms' => 'Net 30'],
            ['name' => 'Hotel Linen House', 'contact' => 'Priya Sharma', 'email' => 'priya@linenhouse.com', 'phone' => '9876543211', 'address' => '456 Textile Nagar, Delhi', 'gst' => '07AABCL5678G1Z3', 'terms' => 'Net 15'],
            ['name' => 'Mega Grocery Wholesalers', 'contact' => 'Amit Patel', 'email' => 'amit@megagrocery.com', 'phone' => '9876543212', 'address' => '789 Wholesale Market, Pune', 'gst' => '27AABCM9012H1Z1', 'terms' => 'Net 30'],
            ['name' => 'Beverage Bros Distributors', 'contact' => 'Vikram Singh', 'email' => 'vikram@beveragebros.com', 'phone' => '9876543213', 'address' => '321 Distribution Hub, Bangalore', 'gst' => '29AABCB3456J1Z9', 'terms' => 'COD'],
            ['name' => 'CleanPro Supplies Pvt Ltd', 'contact' => 'Neha Gupta', 'email' => 'neha@cleanpro.com', 'phone' => '9876543214', 'address' => '654 Industrial Area, Chennai', 'gst' => '33AABCC7890K1Z7', 'terms' => 'Net 30'],
            ['name' => 'Star Kitchen Solutions', 'contact' => 'Arjun Mehta', 'email' => 'arjun@starkitchen.com', 'phone' => '9876543215', 'address' => '987 Kitchen Street, Hyderabad', 'gst' => '36AABCS1234L1Z5', 'terms' => 'Net 45'],
            ['name' => 'Guest Comfort Products', 'contact' => 'Kavita Joshi', 'email' => 'kavita@guestcomfort.com', 'phone' => '9876543216', 'address' => '147 Amenity Lane, Jaipur', 'gst' => '08AABCG5678M1Z3', 'terms' => 'Net 15'],
            ['name' => 'Frozen Foods Express', 'contact' => 'Sanjay Rao', 'email' => 'sanjay@frozenfoods.com', 'phone' => '9876543217', 'address' => '258 Cold Storage Road, Kolkata', 'gst' => '19AABCF9012N1Z1', 'terms' => 'COD'],
        ];

        $suppliers = collect();
        foreach ($suppliersData as $s) {
            $suppliers->push(InventorySupplier::firstOrCreate(
                ['name' => $s['name']],
                [
                    'slug' => Helper::slug('inventory_suppliers', $s['name']),
                    'contact_person' => $s['contact'],
                    'email' => $s['email'],
                    'phone' => $s['phone'],
                    'address' => $s['address'],
                    'gst_number' => $s['gst'],
                    'payment_terms' => $s['terms'],
                    'status' => 'active',
                ]
            ));
        }

        // ── Purchase Orders (skip if already seeded) ──
        if (InventoryPurchaseOrder::count() > 0) {
            $this->command->info('Purchase orders already exist, skipping.');
        } else {
            $poStatuses = ['draft', 'pending', 'approved', 'received'];
            $poCount = 12;

        for ($i = 0; $i < $poCount; $i++) {
            $poDate = Carbon::now()->subDays(rand(0, 30));
            $status = $poStatuses[array_rand($poStatuses)];
            $supplier = $suppliers->random();
            $selectedItems = $items->random(rand(3, 7));

            $subtotal = 0;
            $poItems = [];
            foreach ($selectedItems as $item) {
                $qty = rand(5, 50);
                $cost = $item->cost_price;
                $total = $qty * $cost;
                $subtotal += $total;
                $poItems[] = [
                    'item_id' => $item->id,
                    'quantity_ordered' => $qty,
                    'quantity_received' => $status === 'received' ? $qty : ($status === 'approved' ? rand(0, $qty) : 0),
                    'unit_cost' => $cost,
                    'total_cost' => $total,
                ];
            }
            $taxAmount = round($subtotal * 0.18, 2);
            $totalAmount = $subtotal + $taxAmount;

            $po = InventoryPurchaseOrder::create([
                'hotel_id' => $hotel->id,
                'supplier_id' => $supplier->id,
                'po_number' => 'PO-' . $poDate->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'slug' => 'po-' . $poDate->format('Ymd') . '-' . ($i + 1),
                'po_date' => $poDate,
                'expected_delivery_date' => $poDate->copy()->addDays(rand(3, 10)),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'notes' => collect([
                    'Urgent - kitchen running low',
                    'Standard weekly order',
                    'Monthly bulk order',
                    'Festival season stock-up',
                    null,
                ])->random(),
                'po_status' => $status,
                'approved_by' => in_array($status, ['approved', 'received']) ? $users->random()->id : null,
                'approved_at' => in_array($status, ['approved', 'received']) ? $poDate->copy()->addHours(rand(1, 24)) : null,
                'created_by' => $users->random()->id,
                'status' => 'active',
            ]);

            foreach ($poItems as $poItem) {
                InventoryPurchaseOrderItem::create(array_merge($poItem, [
                    'purchase_order_id' => $po->id,
                ]));
            }
        }
        }

        // ── GRN (skip if already seeded) ──
        if (InventoryGrn::count() > 0) {
            $this->command->info('GRN records already exist, skipping.');
        } else {
            $grnStatuses = ['pending', 'inspected', 'accepted', 'partial'];
            $approvedPOs = InventoryPurchaseOrder::where('po_status', 'approved')->get();

        foreach ($approvedPOs->take(6) as $po) {
            $grnDate = $po->po_date->copy()->addDays(rand(1, 5));
            $status = $grnStatuses[array_rand($grnStatuses)];
            $supplier = $po->supplier;

            $subtotal = 0;
            $grnItems = [];
            foreach ($po->items as $poItem) {
                $qtyReceived = $poItem->quantity_ordered - rand(0, min(3, $poItem->quantity_ordered));
                $qtyRejected = rand(0, min(2, $qtyReceived));
                $qtyAccepted = $qtyReceived - $qtyRejected;
                $total = $qtyAccepted * $poItem->unit_cost;
                $subtotal += $total;

                $grnItems[] = [
                    'item_id' => $poItem->item_id,
                    'quantity_ordered' => $poItem->quantity_ordered,
                    'quantity_received' => $qtyReceived,
                    'quantity_accepted' => $qtyAccepted,
                    'quantity_rejected' => $qtyRejected,
                    'unit_cost' => $poItem->unit_cost,
                    'total_cost' => $total,
                    'rejection_reason' => $qtyRejected > 0 ? collect(['Damaged packaging', 'Wrong item', 'Expired date', 'Quality not met'])->random() : null,
                ];
            }

            $taxAmount = round($subtotal * 0.18, 2);

            $grn = InventoryGrn::create([
                'hotel_id' => $hotel->id,
                'purchase_order_id' => $po->id,
                'supplier_id' => $supplier->id,
                'grn_number' => 'GRN-' . $grnDate->format('Ymd') . str_pad($po->id, 3, '0', STR_PAD_LEFT),
                'slug' => 'grn-' . $grnDate->format('Ymd') . '-' . strtolower($po->po_number),
                'grn_date' => $grnDate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $subtotal + $taxAmount,
                'notes' => 'Received against ' . $po->po_number,
                'remarks' => collect([
                    'All items in good condition',
                    'Some items damaged - noted in rejection',
                    'Partial delivery - remaining expected next week',
                    null,
                ])->random(),
                'grn_status' => $status,
                'received_by' => $users->random()->id,
                'created_by' => $users->random()->id,
                'status' => 'active',
            ]);

            foreach ($grnItems as $grnItem) {
                InventoryGrnItem::create(array_merge($grnItem, [
                    'grn_id' => $grn->id,
                ]));
            }
        }
        }

        // ── Stock (Current Inventory, skip if already seeded) ──
        if (InventoryStock::count() > 0) {
            $this->command->info('Stock records already exist, skipping.');
        } else {
            $locations = ['Main Store', 'Kitchen Store', 'Housekeeping Store', 'Restaurant Store', 'Bar Store'];

            foreach ($items as $item) {
                $qty = rand($item->min_stock, $item->max_stock);
                InventoryStock::create([
                    'hotel_id' => $hotel->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'reserved_quantity' => rand(0, min(5, $qty)),
                    'average_cost' => $item->cost_price * rand(90, 110) / 100,
                ]);
            }
        }

        // ── Stock Transfers (skip if already seeded) ──
        if (InventoryStockTransfer::count() > 0) {
            $this->command->info('Stock transfers already exist, skipping.');
        } else {
            $transferStatuses = ['draft', 'pending', 'approved', 'in_transit', 'received'];
            $transferCount = 8;

        for ($i = 0; $i < $transferCount; $i++) {
            $transferDate = Carbon::now()->subDays(rand(0, 15));
            $status = $transferStatuses[array_rand($transferStatuses)];
            $fromLoc = $locations[array_rand($locations)];
            $toLoc = $locations[array_rand($locations)];
            while ($toLoc === $fromLoc) {
                $toLoc = $locations[array_rand($locations)];
            }
            $selectedItems = $items->random(rand(2, 5));

            $transfer = InventoryStockTransfer::create([
                'hotel_id' => $hotel->id,
                'transfer_number' => 'ST-' . $transferDate->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'slug' => 'st-' . $transferDate->format('Ymd') . '-' . ($i + 1),
                'transfer_date' => $transferDate,
                'from_location' => $fromLoc,
                'to_location' => $toLoc,
                'notes' => collect([
                    'Urgent transfer for event setup',
                    'Routine stock redistribution',
                    'Kitchen to restaurant transfer',
                    'Housekeeping restocking',
                    null,
                ])->random(),
                'transfer_status' => $status,
                'approved_by' => in_array($status, ['approved', 'in_transit', 'received']) ? $users->random()->id : null,
                'approved_at' => in_array($status, ['approved', 'in_transit', 'received']) ? $transferDate->copy()->addHours(rand(1, 12)) : null,
                'created_by' => $users->random()->id,
                'status' => 'active',
            ]);

            foreach ($selectedItems as $item) {
                InventoryStockTransferItem::create([
                    'transfer_id' => $transfer->id,
                    'item_id' => $item->id,
                    'quantity_sent' => rand(2, 20),
                    'quantity_received' => $status === 'received' ? rand(2, 20) : null,
                    'notes' => null,
                ]);
            }
        }
        }

        // ── Stock Adjustments (skip if already seeded) ──
        if (InventoryStockAdjustment::count() > 0) {
            $this->command->info('Stock adjustments already exist, skipping.');
        } else {
            $adjTypes = ['addition', 'subtraction', 'damage', 'expired', 'theft', 'correction'];
            $adjCount = 10;

        for ($i = 0; $i < $adjCount; $i++) {
            $adjDate = Carbon::now()->subDays(rand(0, 20));
            $type = $adjTypes[array_rand($adjTypes)];
            $item = $items->random();
            $stock = InventoryStock::where('item_id', $item->id)->first();
            $qtyBefore = $stock ? $stock->quantity : rand(10, 100);
            $adjQty = rand(1, min(10, $qtyBefore));
            $qtyAfter = in_array($type, ['addition', 'correction']) ? $qtyBefore + $adjQty : $qtyBefore - $adjQty;

            InventoryStockAdjustment::create([
                'hotel_id' => $hotel->id,
                'item_id' => $item->id,
                'adjustment_number' => 'ADJ-' . $adjDate->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'slug' => 'adj-' . $adjDate->format('Ymd') . '-' . ($i + 1),
                'adjustment_date' => $adjDate,
                'adjustment_type' => $type,
                'quantity_before' => $qtyBefore,
                'adjustment_quantity' => $adjQty,
                'quantity_after' => $qtyAfter,
                'unit_cost' => $item->cost_price,
                'total_value' => $adjQty * $item->cost_price,
                'reason' => collect([
                    'Damaged during delivery',
                    'Expired items removed from shelf',
                    'Found missing during audit',
                    'Correction after physical count',
                    'Additional stock received from supplier',
                    'Spillage in store room',
                    'Staff consumption noted',
                    null,
                ])->random(),
                'approved_by' => $users->random()->id,
                'created_by' => $users->random()->id,
                'status' => 'active',
            ]);
        }
        }

        $this->command->info('Inventory dummy data seeded successfully!');
        $this->command->info('  - Categories: ' . InventoryCategory::count());
        $this->command->info('  - Units: ' . InventoryUnit::count());
        $this->command->info('  - Items: ' . InventoryItem::count());
        $this->command->info('  - Suppliers: ' . InventorySupplier::count());
        $this->command->info('  - Purchase Orders: ' . InventoryPurchaseOrder::count());
        $this->command->info('  - GRN: ' . InventoryGrn::count());
        $this->command->info('  - Stock Records: ' . InventoryStock::count());
        $this->command->info('  - Stock Transfers: ' . InventoryStockTransfer::count());
        $this->command->info('  - Stock Adjustments: ' . InventoryStockAdjustment::count());
    }
}
