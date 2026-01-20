<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class InboundTicketFactory extends Factory
{
    public function definition(): array
    {
        $contractIds = DB::table('contracts')->where('status', 'Active')->pluck('id')->toArray();
        $contractId = !empty($contractIds) ? $this->faker->randomElement($contractIds) : 1;

        return [
            'contract_id' => $contractId,
            'expected_date' => $this->faker->dateTimeBetween('-1 month', '+1 week'),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Rejected', 'Completed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}