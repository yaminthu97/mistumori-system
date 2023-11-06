<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\InquirySeeder;
use Database\Seeders\ProjectSeeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // テーブルを切り捨てる
        DB::table('customers')->truncate();
        DB::table('projects')->truncate();
        DB::table('questions')->truncate();

        $this->call([
            CustomerSeeder::class,
            ProjectSeeder::class,
            InquirySeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
