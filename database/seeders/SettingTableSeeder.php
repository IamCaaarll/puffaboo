<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('setting')->insert([
            'id_setting' => 1,
            'company_name' => 'Puff a Boo',
            'address' => 'Pasig',
            'phone' => '09123456789',
            'note_type' => 1, 
            'discount' => 5,
            'logo_path' => '/img/logo.png',
            'member_card_path' => '/img/member.png',
        ]);
    }
}
