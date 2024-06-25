<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassportSeeder extends Seeder
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
			    'user_id' => null,
			    'name' => 'password',
			    'secret' => 'kAUsFYysaEEi82JrFgzgYcU8SJzx8ubRkf7NJj8l',
			    'redirect' => 'http://localhost',
			    'personal_access_client' => '0',
			    'password_client' => '1',
			    'revoked' => '0'
		    ]
	    ];

	    DB::table('oauth_clients')->insert($data);
    }
}
