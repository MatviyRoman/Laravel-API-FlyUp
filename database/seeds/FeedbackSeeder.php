<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackSeeder extends Seeder
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
				'name' => 'Doon',
				'email' => 'test@mail.com',
				'message' => 'Сообщение',
				'comment' => '',
				'language_id' => 1,
                'service_id' => 1,
                'file' => '/images/services/headset.svg',
                'type' => 'feedback',
//				'page_id' => 1,
				'is_viewed' => 0
			],
			[
				'name' => 'Doondex',
				'email' => 'test@mail.com',
				'message' => 'Сообщение',
				'comment' => 'Комментарий',
				'language_id' => 2,
                'service_id' => 2,
                'file' => '/images/services/file.svg',
                'type' => 'message',
//				'page_id' => 3,
				'is_viewed' => 1
			]
		];

		DB::table('feedback')->insert($data);
	}
}
