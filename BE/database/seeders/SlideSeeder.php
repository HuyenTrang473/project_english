<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('slides')->truncate();

        DB::table('slides')->insert([
            [
                'hinh_anh' => 'https://cloudfront-ap-southeast-2.images.arcpublishing.com/tvnz/PQSMVOET6RHL3OVEJ4WLDSEEFY.jpg',
            ],
            [
                'hinh_anh' => 'https://healthylife.com/wfh/wp-content/uploads/2015/07/banner.jpg',
            ],
            [
                'hinh_anh' => 'https://cloudfront-ap-southeast-2.images.arcpublishing.com/tvnz/IXQLJFAVHFGHNMPZ3MOFCSAZTE.jpg',
            ],

        ]);
    }
}
