<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Designation;
use App\Helpers\Helper;

class DepartmentDesignationDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Front Office',
                'description' => 'Guest reception, check-in/check-out, reservations, and concierge services.',
                'designations' => ['Front Office Manager', 'Front Desk Agent', 'Reservation Agent', 'Concierge', 'Night Auditor'],
            ],
            [
                'name' => 'Housekeeping',
                'description' => 'Room cleaning, laundry, public area maintenance, and inventory management.',
                'designations' => ['Executive Housekeeper', 'Housekeeping Supervisor', 'Room Attendant', 'Laundry Supervisor', 'Public Area Cleaner'],
            ],
            [
                'name' => 'Food & Beverage',
                'description' => 'Restaurant operations, bar, room service, banquets, and kitchen management.',
                'designations' => ['F&B Manager', 'Executive Chef', 'Sous Chef', 'Restaurant Manager', 'Waiter/Waitress', 'Bartender', 'Room Service Attendant'],
            ],
            [
                'name' => 'Sales & Marketing',
                'description' => 'Guest acquisition, corporate sales, digital marketing, and revenue management.',
                'designations' => ['Sales Manager', 'Marketing Executive', 'Revenue Analyst', 'Digital Marketing Specialist', 'Sales Executive'],
            ],
            [
                'name' => 'Finance & Accounting',
                'description' => 'Financial reporting, accounts payable/receivable, payroll, and tax compliance.',
                'designations' => ['Finance Manager', 'Accounts Executive', 'Auditor', 'Billing Clerk', 'Payroll Specialist'],
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Recruitment, training, employee relations, and compliance.',
                'designations' => ['HR Manager', 'HR Executive', 'Training Coordinator', 'Recruitment Specialist'],
            ],
            [
                'name' => 'Engineering & Maintenance',
                'description' => 'Building maintenance, HVAC, electrical, plumbing, and preventive maintenance.',
                'designations' => ['Chief Engineer', 'Maintenance Supervisor', 'Electrician', 'Plumber', 'Technician'],
            ],
            [
                'name' => 'Spa & Wellness',
                'description' => 'Spa treatments, wellness programs, and fitness center operations.',
                'designations' => ['Spa Manager', 'Spa Therapist', 'Spa Attendant', 'Fitness Trainer'],
            ],
            [
                'name' => 'Security',
                'description' => 'Guest safety, asset protection, CCTV monitoring, and emergency response.',
                'designations' => ['Security Manager', 'Security Supervisor', 'Security Guard'],
            ],
            [
                'name' => 'IT & Technology',
                'description' => 'PMS/POS systems, network infrastructure, and technical support.',
                'designations' => ['IT Manager', 'System Administrator', 'IT Support Engineer'],
            ],
        ];

        foreach ($departments as $dept) {
            $department = Department::create([
                'company_id' => 1,
                'name' => $dept['name'],
                'slug' => Helper::slug('departments', $dept['name']),
                'description' => $dept['description'],
                'status' => 'active',
            ]);

            foreach ($dept['designations'] as $designationName) {
                Designation::create([
                    'department_id' => $department->id,
                    'name' => $designationName,
                    'slug' => Helper::slug('designations', $designationName),
                    'status' => 'active',
                ]);
            }
        }
    }
}
