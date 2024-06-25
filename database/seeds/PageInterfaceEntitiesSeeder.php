<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageInterfaceEntitiesSeeder extends Seeder
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
			    'page_id' => '1',
			    'interface_entity_id' => '1',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '2',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '3',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '4',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '5',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '6',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '7',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '8',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '9',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '10',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '11',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '12',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '13',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '14',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '15',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '16',
		    ],
		    [
			    'page_id' => '1',
			    'interface_entity_id' => '17',
		    ],
		    [
			    'page_id' => '2',
			    'interface_entity_id' => '1',
		    ],
		    [
			    'page_id' => '2',
			    'interface_entity_id' => '2',
		    ],
		    [
			    'page_id' => '2',
			    'interface_entity_id' => '4',
		    ],
		    [
			    'page_id' => '2',
			    'interface_entity_id' => '18',
		    ],
		    [
			    'page_id' => '2',
			    'interface_entity_id' => '19',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '1',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '2',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '18',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '3',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '7',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '8',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '9',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '10',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '11',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '12',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '13',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '14',
		    ],
		    [
			    'page_id' => '3',
			    'interface_entity_id' => '15',
		    ],
	    ];

	    DB::table('page_interface_entities')->insert($data);
    }
}
