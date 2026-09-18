<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Hotel;
use App\Models\User;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeAttendancePayrollDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found. Seeding skipped.');
            return;
        }

        $employees = Employee::all();
        if ($employees->isEmpty()) {
            $this->command->error('No employees found. Please seed employees first.');
            return;
        }

        $adminUser = User::where('status', 'active')->first();
        $approvedBy = $adminUser ? $adminUser->id : null;

        // ── 1. Seed Attendances for the last 45 days ──
        $startDate = Carbon::today()->subDays(45);
        $endDate = Carbon::today();

        $this->command->info('Seeding employee attendances...');

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $isSunday = $date->isSunday();
            
            foreach ($employees as $employee) {
                if ($isSunday) {
                    EmployeeAttendance::updateOrCreate(
                        ['employee_id' => $employee->id, 'date' => $date->format('Y-m-d')],
                        [
                            'check_in' => null,
                            'check_out' => null,
                            'status' => 'holiday',
                            'hours_worked' => 0.00,
                            'overtime_hours' => 0.00,
                            'notes' => 'Weekly Off (Sunday)',
                            'approved_by' => $approvedBy,
                        ]
                    );
                    continue;
                }

                // Randomize weekday attendance
                $rand = rand(1, 100);
                if ($rand <= 85) {
                    // Present
                    $checkIn = '09:00:00';
                    $checkOut = '18:00:00';
                    $hoursWorked = 9.00;
                    $overtime = rand(1, 10) > 8 ? (float)rand(1, 2) : 0.00;
                    $status = 'present';
                    $notes = 'On time';
                } elseif ($rand <= 90) {
                    // Late
                    $checkIn = '10:15:00';
                    $checkOut = '18:00:00';
                    $hoursWorked = 7.75;
                    $overtime = 0.00;
                    $status = 'late';
                    $notes = 'Late arrival';
                } elseif ($rand <= 94) {
                    // Leave
                    $checkIn = null;
                    $checkOut = null;
                    $hoursWorked = 0.00;
                    $overtime = 0.00;
                    $status = 'leave';
                    $notes = 'Approved leave';
                } elseif ($rand <= 98) {
                    // Half day
                    $checkIn = '09:00:00';
                    $checkOut = '13:30:00';
                    $hoursWorked = 4.50;
                    $overtime = 0.00;
                    $status = 'half_day';
                    $notes = 'Half day duty';
                } else {
                    // Absent
                    $checkIn = null;
                    $checkOut = null;
                    $hoursWorked = 0.00;
                    $overtime = 0.00;
                    $status = 'absent';
                    $notes = 'Absent without notice';
                }

                EmployeeAttendance::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date->format('Y-m-d')],
                    [
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => $status,
                        'hours_worked' => $hoursWorked,
                        'overtime_hours' => $overtime,
                        'notes' => $notes,
                        'approved_by' => $approvedBy,
                    ]
                );
            }
        }

        // ── 2. Seed Payroll for June 2026 (Paid) ──
        $this->command->info('Seeding Paid Payroll for June 2026...');
        $this->seedPayrollForPeriod($hotel->id, '2026-06', '2026-06-01', '2026-06-30', 'paid', '2026-07-05', $approvedBy);

        // ── 3. Seed Payroll for July 2026 (Draft/Processing) ──
        $this->command->info('Seeding Draft Payroll for July 2026...');
        $this->seedPayrollForPeriod($hotel->id, '2026-07', '2026-07-01', '2026-07-31', 'draft', null, $approvedBy);
    }

    private function seedPayrollForPeriod($hotelId, $period, $startDate, $endDate, $status, $paymentDate, $approvedBy): void
    {
        $employees = Employee::all();
        $daysInPeriod = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        // Check if payroll already exists
        $payrollNumber = 'PAY-' . Carbon::parse($startDate)->format('Ym');
        $payroll = Payroll::where('payroll_number', $payrollNumber)->first();
        if ($payroll) {
            $payroll->items()->delete();
        } else {
            $payroll = Payroll::create([
                'hotel_id' => $hotelId,
                'payroll_number' => $payrollNumber,
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => $paymentDate,
                'status' => $status,
                'created_by' => $approvedBy,
            ]);
        }

        $totalBasic = 0;
        $totalEarnings = 0;
        $totalDeductions = 0;
        $totalNetPay = 0;

        foreach ($employees as $employee) {
            // Count attendances
            $attendance = EmployeeAttendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $daysPresent = $attendance->whereIn('status', ['present', 'late'])->count();
            $daysAbsent = $daysInPeriod - $daysPresent;
            $overtimeHours = $attendance->sum('overtime_hours');

            // Apply standard payroll calculation logic
            $earnedBasic = $daysPresent > 0 ? ($employee->basic_salary / $daysInPeriod) * $daysPresent : 0;
            $earnedHra = $earnedBasic * 0.40;
            $earnedAllowances = $earnedBasic * 0.20;
            $earnings = $earnedBasic + $earnedHra + $earnedAllowances;

            $pfDeduction = $earnedBasic * 0.12;
            $esiDeduction = $earnings <= 21000 ? $earnings * 0.075 : 0;
            $deductions = $pfDeduction + $esiDeduction;
            $netPay = $earnings - $deductions;

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'employee_id' => $employee->id,
                'basic_salary' => $employee->basic_salary,
                'earnings' => $earnings,
                'deductions' => $deductions,
                'net_pay' => $netPay,
                'days_present' => $daysPresent,
                'days_absent' => $daysAbsent,
                'overtime_hours' => $overtimeHours,
                'earned_basic' => $earnedBasic,
                'earned_hra' => $earnedHra,
                'earned_allowances' => $earnedAllowances,
                'pf_deduction' => $pfDeduction,
                'esi_deduction' => $esiDeduction,
                'status' => $status === 'paid' ? 'paid' : 'pending',
            ]);

            $totalBasic += $employee->basic_salary;
            $totalEarnings += $earnings;
            $totalDeductions += $deductions;
            $totalNetPay += $netPay;
        }

        $payroll->update([
            'total_basic' => $totalBasic,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'total_net_pay' => $totalNetPay,
            'total_employees' => $employees->count(),
        ]);
    }
}
