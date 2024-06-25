<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessengerSeeder extends Seeder
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
                'order' => '0',
                'name' => 'Telegram',
                'flag' => '/images/messengers/telegram.png'
            ],
            [
                'order' => '1',
                'name' => 'Viber',
                'flag' => '/images/messengers/viber.png'
            ],
            [
                'order' => '2',
                'name' => 'WhatsApp',
                'flag' => '/images/messengers/whatsapp.png'
            ],
            [
                'order' => '3',
                'name' => 'IMO',
                'flag' => '/images/messengers/imo.png'
            ]
        ];
        DB::table('messengers')->insert($data);
    }
}
