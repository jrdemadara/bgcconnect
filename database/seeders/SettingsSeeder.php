<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'multilevel_size' => 5,
            'direct_points' => 10,
            'downline_points' => 5,
            'activity_points' => 10,
            'channel_size' => 5,
            'last_channel' => 5,
            'verification_expiry' => 1800,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
