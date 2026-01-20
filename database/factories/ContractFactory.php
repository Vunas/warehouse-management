<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContractFactory extends Factory
{
    public function definition(): array
    {
        $customerIds = DB::table('customers')->pluck('id')->toArray();
        $custId = !empty($customerIds) ? $this->faker->randomElement($customerIds) : 1;

        $startDate = $this->faker->dateTimeBetween('-2 years', '+1 month');
        $endDate = Carbon::parse($startDate)->addMonths($this->faker->numberBetween(6, 24));

        return [
            'customer_id' => $custId,
            'contract_code' => strtoupper($this->faker->unique()->bothify('CTR-2024-####')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'penalty_markup' => $this->faker->randomElement([1.0, 1.2, 1.5, 2.0]),
            'status' => $this->faker->randomElement(['Active', 'Active', 'Active', 'Expired', 'Suspended']), // Tỉ lệ Active cao hơn
            'deleted_at' => null,
            'created_at' => $startDate,
            'updated_at' => now(),
        ];
    }
}