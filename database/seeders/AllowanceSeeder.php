<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('allowances')->insert(
            [
                [
                    'country' => 'PL',
                    'amount' => 10
                ],
                [
                    'country' => 'DE',
                    'amount' => 50
                ],
                [
                    'country' => 'GB',
                    'amount' => 75
                ],
            ]
        );
    }
}
