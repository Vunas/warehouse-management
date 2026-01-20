<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Employee::factory()->create([
            'position' => 'Warehouse Manager',
            'hired_at' => now()->subYears(2),
        ]);

        Employee::factory()->count(20)->create();

        $staffRole = DB::table('roles')->where('name', 'Staff')->first();

        if ($staffRole) {
            $employees = Employee::all();

            foreach ($employees as $emp) {
                $hasRole = DB::table('employee_role')
                    ->where('employee_id', $emp->id)
                    ->exists();

                if (!$hasRole) {
                    DB::table('employee_role')->insert([
                        'employee_id' => $emp->id,
                        'role_id'     => $staffRole->id
                    ]);
                }
            }
        }

        echo "Seeded: Employees with Roles.\n";
    }
}
