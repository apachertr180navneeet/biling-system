# Motor Vehicle Billing & Inventory System — Product Plan
**Scope:** New vehicles (single showroom) + Spare Parts | GST & Non-GST billing | Full inventory tracking
**Stack:** Laravel 11 + MySQL 8

---

## 1. Module List

### A. Master Data
1. **Vehicle Master** — Brand, Model, Variant, Color, HSN code, Ex-showroom price list
2. **Spare Parts Master** — Part No, Name, Category, HSN/SAC code, GST applicability, GST rate
3. **HSN/SAC & GST Master** — Tax slabs (CGST/SGST/IGST), state codes
4. **Supplier / OEM Master** — Vehicle manufacturer & parts vendor details
5. **Customer Master** — Individual / Corporate, KYC docs, GSTIN (if B2B), address, Aadhaar/PAN

### B. Inventory
6. **Vehicle Stock (Chassis-wise)** — Each vehicle unit tracked by chassis no. + engine no. + VIN, status: In Stock / Reserved / Sold / In-Transit
7. **Spare Parts Stock** — Quantity, godown/rack location, reorder level, low-stock alerts
8. **Purchase / GRN** — Purchase orders to OEM/vendors, Goods Receipt Note, stock-in

### C. Sales & Billing
9. **Vehicle Sales / Booking** — Booking → Allocation (chassis no.) → Invoice → Delivery
10. **Vehicle Invoice (GST / Non-GST toggle)** — On-road price breakup: Ex-showroom + RTO + Insurance + Accessories + Extended Warranty + handling
11. **Spare Parts Invoice (GST / Non-GST toggle)** — Counter sale or service-linked sale
12. **Quotation / Proforma Invoice** — Before final billing
13. **Credit Note / Debit Note** — Returns, price corrections
14. **E-Invoice / E-Way Bill hooks** *(optional, Phase 2)* — IRN generation if turnover mandates it

### D. Accounts & Payments
15. **Payments & Receipts** — Cash, Card, UPI, Bank Transfer, Finance/Loan disbursement, EMI-linked
16. **Customer Ledger** — Outstanding, advances, part-payments
17. **Finance/Loan Partner Tracking** — Bank/NBFC name, loan amount, disbursement status (if vehicle sold on loan)

### E. Admin & Reports
18. **User Roles & Permissions** — Admin, Sales Executive, Accountant, Store/Inventory Manager
19. **Reports**
    - Sales Register (GST / Non-GST split)
    - GSTR-1 summary export (B2B/B2C, HSN-wise)
    - Stock Register (vehicle & parts)
    - Outstanding/Receivables report
    - Profit report (purchase vs sale price)
20. **Audit Log** — Who created/edited/cancelled an invoice

> **Not in current scope but worth flagging for later:** Service/workshop module (job cards, labour billing) — many vehicle dealers eventually need this since spare parts billing often ties into service. Let me know if you want it added as Phase 2.

---

## 2. Database Schema

### Master Tables

**`users`**
| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| name, email, password | | |
| role_id | FK → roles | |
| status | ENUM(active,inactive) | |

**`roles`** — id, name (admin, sales, accountant, inventory_manager)

**`customers`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| type | ENUM(individual, corporate) | |
| name, phone, email | | |
| address, state, state_code | | for GST place of supply |
| gstin | NULLABLE | only for B2B |
| pan_no, aadhaar_no | NULLABLE | |
| created_at, updated_at | | |

**`vehicle_brands`** — id, name
**`vehicle_models`** — id, brand_id FK, name, body_type (hatchback/SUV/etc.)
**`vehicle_variants`** — id, model_id FK, name, fuel_type, transmission, ex_showroom_price, hsn_code
**`vehicle_colors`** — id, variant_id FK, color_name, color_code

**`hsn_sac_master`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| code | HSN/SAC code | |
| description | | |
| gst_rate | DECIMAL | e.g. 28.00 |
| cess_rate | DECIMAL | vehicles often attract compensation cess |

**`spare_part_categories`** — id, name (e.g. Engine, Electrical, Body)

**`spare_parts`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| part_no | UNIQUE | |
| name | | |
| category_id | FK | |
| hsn_code | FK → hsn_sac_master | |
| is_gst_applicable | BOOLEAN | drives GST vs non-GST invoice line |
| gst_rate | DECIMAL | copied at time of sale for history |
| purchase_price, selling_price | DECIMAL | |
| unit | ENUM(pcs, set, ltr) | |

**`suppliers`** — id, name, type(OEM/parts_vendor), gstin, address, contact

---

### Inventory Tables

**`vehicle_stock`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| variant_id | FK | |
| color_id | FK | |
| chassis_no | UNIQUE | |
| engine_no | UNIQUE | |
| vin | UNIQUE | |
| purchase_id | FK → purchase_orders | |
| purchase_price | DECIMAL | |
| status | ENUM(in_stock, reserved, sold, in_transit) | |
| received_date | DATE | |

**`spare_part_stock`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| part_id | FK | |
| location | rack/godown code | |
| quantity | INT | |
| reorder_level | INT | |
| last_updated | TIMESTAMP | |

**`purchase_orders`** — id, supplier_id FK, po_type(vehicle/parts), po_date, status
**`purchase_order_items`** — id, po_id FK, item_type(vehicle/part), item_id, qty, rate
**`grn`** (Goods Receipt Note) — id, po_id FK, received_date, received_by FK→users

---

### Sales & Billing Tables

**`vehicle_bookings`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| customer_id | FK | |
| variant_id | FK | |
| color_id | FK | |
| booking_amount | DECIMAL | |
| status | ENUM(booked, allocated, invoiced, cancelled) | |
| allocated_stock_id | FK → vehicle_stock, NULLABLE | |

**`vehicle_invoices`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| invoice_no | UNIQUE, sequential | separate series recommended for GST vs non-GST |
| invoice_type | ENUM(gst, non_gst) | |
| booking_id | FK, NULLABLE | |
| customer_id | FK | |
| vehicle_stock_id | FK | |
| ex_showroom_price | DECIMAL | |
| rto_charges | DECIMAL | |
| insurance_charges | DECIMAL | |
| accessories_charges | DECIMAL | |
| extended_warranty_charges | DECIMAL | |
| taxable_amount | DECIMAL | |
| cgst_amount, sgst_amount, igst_amount | DECIMAL | 0 if non-GST |
| cess_amount | DECIMAL | |
| total_amount | DECIMAL | |
| payment_status | ENUM(pending, partial, paid) | |
| invoice_date | DATE | |
| created_by | FK → users | |
| cancelled_at | TIMESTAMP NULLABLE | |

**`spare_part_invoices`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| invoice_no | UNIQUE | separate series per invoice_type |
| invoice_type | ENUM(gst, non_gst) | |
| customer_id | FK, NULLABLE | walk-in allowed |
| subtotal | DECIMAL | |
| cgst_amount, sgst_amount, igst_amount | DECIMAL | |
| total_amount | DECIMAL | |
| payment_status | ENUM | |
| invoice_date | DATE | |
| created_by | FK | |

**`spare_part_invoice_items`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| invoice_id | FK | |
| part_id | FK | |
| qty | INT | |
| rate | DECIMAL | |
| gst_rate | DECIMAL | snapshot at sale time |
| amount | DECIMAL | |

**`credit_notes`** / **`debit_notes`** — id, invoice_type(vehicle/part), invoice_id, reason, amount, date

---

### Payments & Ledger

**`payments`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| payable_type | ENUM(vehicle_invoice, spare_part_invoice) | polymorphic |
| payable_id | BIGINT | |
| mode | ENUM(cash, card, upi, bank_transfer, finance) | |
| amount | DECIMAL | |
| reference_no | NULLABLE | |
| paid_at | TIMESTAMP | |

**`finance_details`** — id, vehicle_invoice_id FK, bank_name, loan_amount, disbursement_status, sanction_date

**`customer_ledger`** — id, customer_id FK, txn_type(debit/credit), amount, reference, date

---

### Admin

**`audit_logs`** — id, user_id FK, action, model_type, model_id, old_value, new_value, created_at

---

## 3. Key Design Decisions to Flag

1. **Invoice numbering:** GST and Non-GST invoices should use separate number series (e.g. `GST/24-25/0001` vs `NGST/24-25/0001`) — this is standard practice and simplifies GSTR-1 filing.
2. **Non-GST bills:** Confirm the business scenario for these — e.g. accessories sold as pure trading without GST registration threshold, or old-stock cash memos. This affects whether `is_gst_applicable` is a global toggle per invoice or per line item (a single invoice could technically have both GST and non-GST lines if not handled carefully — recommend keeping it invoice-level for simplicity).
3. **Cess on vehicles:** Passenger vehicles often attract GST Compensation Cess on top of 28% GST — the `hsn_sac_master.cess_rate` field is there for this.
4. **Chassis-level stock:** Since these are new vehicles, each unit is unique (not just quantity-based like parts) — hence the separate `vehicle_stock` table keyed by chassis/engine/VIN rather than a simple quantity counter.

---

## 4. Suggested Build Order (Laravel + MySQL)

**Phase 1 — Foundation**
- Auth, roles/permissions, master data (brands, models, variants, colors, HSN, customers, suppliers)

**Phase 2 — Inventory**
- Purchase orders, GRN, vehicle_stock, spare_part_stock

**Phase 3 — Billing Core**
- Vehicle booking → invoice flow, spare parts invoice flow, GST calculation engine, invoice numbering series

**Phase 4 — Payments & Reports**
- Payments, ledger, GSTR-1 export, stock reports, dashboard

**Phase 5 (optional)**
- E-invoice/e-way bill API integration, service module

---

Let me know which module you want to start building first — I can generate Laravel migrations/models for any of these tables, or a full ERD.
