<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeZoneSeeder extends Seeder
{
    public function run(): void
    {
        $timezones = [
            ['name' => 'UTC', 'offset' => '+00:00', 'abbreviation' => 'UTC'],
            ['name' => 'Asia/Kolkata', 'offset' => '+05:30', 'abbreviation' => 'IST'],
            ['name' => 'Asia/Dubai', 'offset' => '+04:00', 'abbreviation' => 'GST'],
            ['name' => 'Asia/Riyadh', 'offset' => '+03:00', 'abbreviation' => 'AST'],
            ['name' => 'Asia/Karachi', 'offset' => '+05:00', 'abbreviation' => 'PKT'],
            ['name' => 'Europe/London', 'offset' => '+00:00', 'abbreviation' => 'GMT/BST'],
            ['name' => 'Europe/Paris', 'offset' => '+01:00', 'abbreviation' => 'CET'],
            ['name' => 'America/New_York', 'offset' => '-05:00', 'abbreviation' => 'EST'],
            ['name' => 'America/Chicago', 'offset' => '-06:00', 'abbreviation' => 'CST'],
            ['name' => 'America/Los_Angeles', 'offset' => '-08:00', 'abbreviation' => 'PST'],
            ['name' => 'Asia/Tokyo', 'offset' => '+09:00', 'abbreviation' => 'JST'],
            ['name' => 'Asia/Shanghai', 'offset' => '+08:00', 'abbreviation' => 'CST'],
            ['name' => 'Asia/Singapore', 'offset' => '+08:00', 'abbreviation' => 'SGT'],
            ['name' => 'Asia/Bangkok', 'offset' => '+07:00', 'abbreviation' => 'ICT'],
            ['name' => 'Asia/Jakarta', 'offset' => '+07:00', 'abbreviation' => 'WIB'],
            ['name' => 'Australia/Sydney', 'offset' => '+10:00', 'abbreviation' => 'AEST'],
            ['name' => 'Pacific/Auckland', 'offset' => '+12:00', 'abbreviation' => 'NZST'],
            ['name' => 'Africa/Cairo', 'offset' => '+02:00', 'abbreviation' => 'EET'],
            ['name' => 'Africa/Johannesburg', 'offset' => '+02:00', 'abbreviation' => 'SAST'],
            ['name' => 'Asia/Kathmandu', 'offset' => '+05:45', 'abbreviation' => 'NPT'],
        ];

        foreach ($timezones as $tz) {
            DB::table('timezone')->updateOrInsert(
                ['name' => $tz['name']],
                $tz
            );
        }
    }
}
