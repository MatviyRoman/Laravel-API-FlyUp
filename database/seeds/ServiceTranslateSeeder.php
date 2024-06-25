<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTranslateSeeder extends Seeder
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
				'service_id' => '1',
				'language_id' => '1',
				'title' => 'Разработка сайтов',
				'description' => 'Интернет-магазины, посадочные страницы, другие сложные решения',
				'url' => ''
			],
            [
                'service_id' => '2',
                'language_id' => '1',
                'title' => 'Мобильные приложения',
                'description' => 'Для бизнеса, игры, дополненная и виртуальная реальности',
                'url' => ''
            ],
            [
                'service_id' => '3',
                'language_id' => '1',
                'title' => 'Дизайн',
                'description' => 'Логотипы, UI/UX (сайты, мобильные приложения), печатная продукция',
                'url' => ''
            ],
            [
                'service_id' => '4',
                'language_id' => '1',
                'title' => 'Outstaffing',
                'description' => 'HR, формирование удаленной команды разработчиков',
                'url' => ''
            ],
            [
                'service_id' => '5',
                'language_id' => '1',
                'title' => 'Консультирования',
                'description' => 'Написание функциональных спецификаций, стартапы',
                'url' => ''
            ],
            [
                'service_id' => '6',
                'language_id' => '1',
                'title' => 'Маркетинг',
                'description' => 'SEO, контекстная реклама, копирайтинг',
                'url' => ''
            ],
            [
                'service_id' => '7',
                'language_id' => '1',
                'title' => 'Хостинг-решения',
                'description' => '',
                'url' => ''
            ],
            [
                'service_id' => '8',
                'language_id' => '1',
                'title' => 'Blockchain',
                'description' => '',
                'url' => ''
            ],
            [
                'service_id' => '9',
                'language_id' => '1',
                'title' => 'Контроль качества',
                'description' => '',
                'url' => ''
            ]
		];

		DB::table('service_translates')->insert($data);
	}
}
