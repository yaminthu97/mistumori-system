<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * ステージングor本番環境用の管理アカウントデータ投入
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {

            $usersData = [
                [
                    'id'         => 1,
                    'role'       => 1,
                    'login_id'   => 'mtm@gmail.com',
                    'name'       => 'MTM',
                    'email'      => 'mtm@gmail.com',
                    'password'   => Hash::make('test'),
                    'created_user_id' => 1,
                ],
                [
                    'id'         => 2,
                    'role'       => 0,
                    'login_id'   => 'sale@gmail.com',
                    'name'       => 'Sale',
                    'email'      => 'sale@gmail.com',
                    'password'   => Hash::make('test'),
                    'created_user_id' => 2,
                ],
                [
                    'id'         => 3,
                    'role'       => 1,
                    'login_id'   => 'mtm2@gmail.com',
                    'name'       => 'MTM2',
                    'email'      => 'mtm2@gmail.com',
                    'password'   => Hash::make('test'),
                    'created_user_id' => 3,
                ],
            ];

            foreach ($usersData as $data) {
                $users = new User();
                $users->fill($data)->save();
            }
        });
    }
}
