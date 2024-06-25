<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
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
			    'name' => 'index',
		    ],
		    [
			    'name' => 'services_development',
		    ],
		    [
			    'name' => 'contacts',
		    ],
		    [
			    'name' => 'blog',
		    ]
	    ];

	    DB::table('pages')->insert($data);
    }
}
