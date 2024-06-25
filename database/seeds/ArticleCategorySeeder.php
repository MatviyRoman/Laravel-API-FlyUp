<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleCategorySeeder extends Seeder
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
		    ],
		    [
			    'is_active' => 1,
			    'order' => 2,
		    ],
		    [
			    'is_active' => 0,
			    'order' => 3,
		    ]
	    ];

	    DB::table('article_categories')->insert($data);
    }
}
