<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
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
                'name' => 'ru',
                'flag' => '/images/flags/ru.jpg'
            ],
            [
                'order' => '1',
                'name' => 'en',
                'flag' => '/images/flags/en.jpg'
            ],
            [
                'order' => '2',
                'name' => 'fi',
                'flag' => '/images/flags/fi.jpg'
            ],
            [
                'order' => '3',
                'name' => 'it',
                'flag' => '/images/flags/it.jpg'
            ]
        ];
//        $json = file_get_contents('../json/iso_639-2.json');
//        $json_data = json_decode($json, true);
//        $order = 0;
//        $data = [];
//        foreach ($json_data as $short_name => $fullNames) {
//            $order++;
//            $flag = '/images/flags/' . $short_name . '.jpg';
//
//            $int = '';
//            for ($i = 0; $i < count($fullNames['int']); $i++) {
//                if ($i) {
//                    $int .= '; ' . $fullNames['int'][$i];
//                } else {
//                    $int .= $fullNames['int'][$i];
//                }
//            }
//
//            $native = '';
//            for ($i = 0; $i < count($fullNames['native']); $i++) {
//                if ($i) {
//                    $native .= '; ' . $fullNames['native'][$i];
//                } else {
//                    $native .= $fullNames['native'][$i];
//                }
//            }
//            array_push(
//                $data,
//                [
//                    'order' => $order,
//                    'short_name' => $short_name,
//                    'flag' => $flag,
//                    'native_name' => $native,
//                    'english_name' => $int
//                ]
//            );
//        }

	    DB::table('languages')->insert($data);
    }
}
