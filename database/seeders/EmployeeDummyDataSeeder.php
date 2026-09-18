<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Branch;
use App\Models\Hotel;
use App\Helpers\Helper;
use Carbon\Carbon;

class EmployeeDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found. Seeding skipped.');
            return;
        }

        $branches = Branch::where('status', 'active')->get();
        if ($branches->isEmpty()) {
            $this->command->error('No active branches found. Seeding skipped.');
            return;
        }

        $headOffice = $branches->firstWhere('is_head_office', true) ?: $branches->first();
        $goaBranch = $branches->firstWhere('name', 'like', '%Goa%') ?: $branches->first();

        // Get Departments & Designations dynamically
        $frontOffice = Department::where('name', 'Front Office')->first();
        $housekeeping = Department::where('name', 'Housekeeping')->first();
        $fnb = Department::where('name', 'Food & Beverage')->first();
        $finance = Department::where('name', 'Finance & Accounting')->first();
        $hr = Department::where('name', 'Human Resources')->first();
        $engineering = Department::where('name', 'Engineering & Maintenance')->first();
        $spa = Department::where('name', 'Spa & Wellness')->first();
        $security = Department::where('name', 'Security')->first();
        $it = Department::where('name', 'IT & Technology')->first();

        $employeesData = [
            [
                'first_name' => 'Amit',
                'last_name' => 'Sharma',
                'email' => 'amit.sharma@mehmaan.com',
                'phone' => '+91-9876543210',
                'gender' => 'male',
                'date_of_birth' => '1985-04-12',
                'date_of_joining' => '2020-01-15',
                'basic_salary' => 55000.00,
                'employment_type' => 'full_time',
                'department_id' => $frontOffice?->id,
                'designation_id' => Designation::where('name', 'Front Office Manager')->first()?->id,
                'branch_id' => $headOffice->id,
            ],
            [
                'first_name' => 'Priya',
                'last_name' => 'Patel',
                'email' => 'priya.patel@mehmaan.com',
                'phone' => '+91-9876543211',
                'gender' => 'female',
                'date_of_birth' => '1992-08-23',
                'date_of_joining' => '2022-03-10',
                'basic_salary' => 28000.00,
                'employment_type' => 'full_time',
                'department_id' => $frontOffice?->id,
                'designation_id' => Designation::where('name', 'Front Desk Agent')->first()?->id,
                'branch_id' => $goaBranch->id,
            ],
            [
                'first_name' => 'Vikram',
                'last_name' => 'Singh',
                'email' => 'vikram.singh@mehmaan.com',
                'phone' => '+91-9876543212',
                'gender' => 'male',
                'date_of_birth' => '1988-11-05',
                'date_of_joining' => '2021-06-01',
                'basic_salary' => 65000.00,
                'employment_type' => 'full_time',
                'department_id' => $fnb?->id,
                'designation_id' => Designation::where('name', 'Executive Chef')->first()?->id,
                'branch_id' => $goaBranch->id,
            ],
            [
                'first_name' => 'Neha',
                'last_name' => 'Gupta',
                'email' => 'neha.gupta@mehmaan.com',
                'phone' => '+91-9876543213',
                'gender' => 'female',
                'date_of_birth' => '1990-05-18',
                'date_of_joining' => '2019-10-01',
                'basic_salary' => 58000.00,
                'employment_type' => 'full_time',
                'department_id' => $hr?->id,
                'designation_id' => Designation::where('name', 'HR Manager')->first()?->id,
                'branch_id' => $headOffice->id,
            ],
            [
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'email' => 'rajesh.kumar@mehmaan.com',
                'phone' => '+91-9876543214',
                'gender' => 'male',
                'date_of_birth' => '1983-02-14',
                'date_of_joining' => '2018-04-20',
                'basic_salary' => 48000.00,
                'employment_type' => 'full_time',
                'department_id' => $finance?->id,
                'designation_id' => Designation::where('name', 'Accounts Executive')->first()?->id,
                'branch_id' => $headOffice->id,
            ],
            [
                'first_name' => 'Sunita',
                'last_name' => 'Rao',
                'email' => 'sunita.rao@mehmaan.com',
                'phone' => '+91-9876543215',
                'gender' => 'female',
                'date_of_birth' => '1995-12-01',
                'date_of_joining' => '2023-01-10',
                'basic_salary' => 19000.00,
                'employment_type' => 'full_time',
                'department_id' => $housekeeping?->id,
                'designation_id' => Designation::where('name', 'Room Attendant')->first()?->id,
                'branch_id' => $goaBranch->id,
            ],
            [
                'first_name' => 'Anil',
                'last_name' => 'Mehta',
                'email' => 'anil.mehta@mehmaan.com',
                'phone' => '+91-9876543216',
                'gender' => 'male',
                'date_of_birth' => '1987-07-29',
                'date_of_joining' => '2021-08-15',
                'basic_salary' => 32000.00,
                'employment_type' => 'full_time',
                'department_id' => $engineering?->id,
                'designation_id' => Designation::where('name', 'Maintenance Supervisor')->first()?->id,
                'branch_id' => $goaBranch->id,
            ],
            [
                'first_name' => 'Pooja',
                'last_name' => 'Joshi',
                'email' => 'pooja.joshi@mehmaan.com',
                'phone' => '+91-9876543217',
                'gender' => 'female',
                'date_of_birth' => '1993-03-05',
                'date_of_joining' => '2022-11-01',
                'basic_salary' => 24000.00,
                'employment_type' => 'full_time',
                'department_id' => $spa?->id,
                'designation_id' => Designation::where('name', 'Spa Therapist')->first()?->id,
                'branch_id' => $goaBranch->id,
            ],
            [
                'first_name' => 'Rahul',
                'last_name' => 'Verma',
                'email' => 'rahul.verma@mehmaan.com',
                'phone' => '+91-9876543218',
                'gender' => 'male',
                'date_of_birth' => '1991-09-17',
                'date_of_joining' => '2023-05-01',
                'basic_salary' => 16500.00,
                'employment_type' => 'full_time',
                'department_id' => $security?->id,
                'designation_id' => Designation::where('name', 'Security Guard')->first()?->id,
                'branch_id' => $goaBranch->id,
            ],
            [
                'first_name' => 'Rohan',
                'last_name' => 'Deshmukh',
                'email' => 'rohan.deshmukh@mehmaan.com',
                'phone' => '+91-9876543219',
                'gender' => 'male',
                'date_of_birth' => '1994-02-10',
                'date_of_joining' => '2022-07-01',
                'basic_salary' => 36000.00,
                'employment_type' => 'full_time',
                'department_id' => $it?->id,
                'designation_id' => Designation::where('name', 'IT Support Engineer')->first()?->id,
                'branch_id' => $headOffice->id,
            ],
        ];

        $banks = ['State Bank of India', 'HDFC Bank', 'ICICI Bank', 'Axis Bank', 'Punjab National Bank'];

        foreach ($employeesData as $index => $data) {
            $num = $index + 1;
            $fullName = $data['first_name'] . ' ' . $data['last_name'];
            
            Employee::create(array_merge($data, [
                'hotel_id' => $hotel->id,
                'employee_id' => sprintf('EMP-%03d', $num),
                'slug' => Helper::slug('employees', $fullName),
                'address' => 'Street ' . $num . ', Sector ' . rand(1, 20),
                'city' => $data['branch_id'] === $headOffice->id ? 'New Delhi' : 'Panaji',
                'state' => $data['branch_id'] === $headOffice->id ? 'Delhi' : 'Goa',
                'pin_code' => $data['branch_id'] === $headOffice->id ? '110001' : '403001',
                'pan_number' => 'ABCDE' . rand(1000, 9999) . 'F',
                'aadhaar_number' => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                'bank_name' => $banks[array_rand($banks)],
                'bank_account_number' => '91000' . rand(1000000, 9999999),
                'ifsc_code' => 'SBIN000' . rand(1000, 9999),
                'status' => 'active',
            ]));
        }
    }
}
