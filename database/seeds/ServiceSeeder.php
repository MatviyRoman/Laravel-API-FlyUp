<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $data = [
		    [
			    'is_active' => 1,
			    'order' => 1,
			    'icon' => '/images/services/file.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 2,
			    'icon' => '/images/services/headset.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 3,
			    'icon' => '/images/services/bar-chart.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 4,
			    'icon' => '/images/services/briefcase.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 5,
			    'icon' => '/images/services/file1.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 6,
			    'icon' => '/images/services/cloud.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 7,
			    'icon' => '/images/services/pen.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 8,
			    'icon' => '/images/services/center-align.svg',
		    ],
		    [
			    'is_active' => 1,
			    'order' => 9,
			    'icon' => '/images/services/coins.svg',
		    ]
	    ];

	    DB::table('services')->insert($data);
    }
}
