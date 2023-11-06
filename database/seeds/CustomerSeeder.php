<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {

            $customersData = [
                [
                    'id' => 1,
                    'customer_name' => 'Customer 1',
                    'description' => 'Sample data for customer, customer 1',
                    'created_user_id' => 2,
                    'updated_user_id' => 2,
                ],
                [
                    'id' => 2,
                    'customer_name' => 'Customer 2',
                    'description' => 'Sample data for customer, customer 2',
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ],
                [
                    'id' => 3,
                    'customer_name' => 'Customer 3',
                    'description' => 'Sample data for customer, customer 3',
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ]
            ];

            foreach ($customersData as $data) {
                $customers  = new Customer();
                $customers->fill($data)->save();
            }
        });
    }
}
