<?php

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     *  新しくSeederを追加した場合は、下記のコマンドを実行する必要があります。
     *    composer dump-autoload
     *  @link https://readouble.com/laravel/6.x/ja/seeding.html?header=%25E3%2582%25B7%25E3%2583%25BC%25E3%2583%2580%25E3%2581%25AE%25E5%25AE%259F%25E8%25A1%258C
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // テーブルを切り捨てる
        DB::table('users')->truncate();
        $this->call(UserSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
