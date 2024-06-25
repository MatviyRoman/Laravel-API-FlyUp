<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoSeeder extends Seeder
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
			    'language_id' => '1',
			    'title' => 'EGC Главная',
			    'keywords' => 'egc, главная, keywords',
			    'description' => 'EGC главная страница description',
			    'url' => null
		    ],
		    [
			    'page_id' => '2',
			    'language_id' => '1',
			    'title' => 'EGC Услуги',
			    'keywords' => 'egc, услуги, keywords',
			    'description' => 'EGC страница услуг description',
			    'url' => 'uslugi/razrabotka_saytov'
		    ],
		    [
			    'page_id' => '3',
			    'language_id' => '1',
			    'title' => 'EGC Контакты',
			    'keywords' => 'egc, контакты, keywords',
			    'description' => 'EGC страница контактов description',
			    'url' => 'kontakty'
		    ],
		    [
			    'page_id' => '4',
			    'language_id' => '1',
			    'title' => 'EGC Блог',
			    'keywords' => 'egc, блог, keywords',
			    'description' => 'EGC страница блога description',
			    'url' => 'blog'
		    ],
		    [
			    'page_id' => '1',
			    'language_id' => '2',
			    'title' => 'EGC Main',
			    'keywords' => 'egc, main, keywords',
			    'description' => 'EGC main page description',
			    'url' => null
		    ],
		    [
			    'page_id' => '2',
			    'language_id' => '2',
			    'title' => 'EGC Services',
			    'keywords' => 'egc, services, keywords',
			    'description' => 'EGC services page description',
			    'url' => 'services/development'
		    ],
		    [
			    'page_id' => '3',
			    'language_id' => '2',
			    'title' => 'EGC Contacts',
			    'keywords' => 'egc, контакты, keywords',
			    'description' => 'EGC contacts page description',
			    'url' => 'contacts'
		    ],
		    [
			    'page_id' => '4',
			    'language_id' => '2',
			    'title' => 'EGC Blog',
			    'keywords' => 'egc, blog, keywords',
			    'description' => 'EGC blog page description',
			    'url' => 'blog'
		    ],
		    [
			    'page_id' => '1',
			    'language_id' => '4',
			    'title' => 'EGC Main',
			    'keywords' => 'egc, main, keywords',
			    'description' => 'EGC main page description',
			    'url' => null
		    ],
		    [
			    'page_id' => '2',
			    'language_id' => '4',
			    'title' => 'EGC Services',
			    'keywords' => 'egc, services, keywords',
			    'description' => 'EGC services page description',
			    'url' => 'services/development'
		    ],
		    [
			    'page_id' => '3',
			    'language_id' => '4',
			    'title' => 'EGC Contacts',
			    'keywords' => 'egc, контакты, keywords',
			    'description' => 'EGC contacts page description',
			    'url' => 'contacts'
		    ],
		    [
			    'page_id' => '4',
			    'language_id' => '4',
			    'title' => 'EGC Blog',
			    'keywords' => 'egc, blog, keywords',
			    'description' => 'EGC blog page description',
			    'url' => 'blog'
		    ],
	    ];

	    DB::table('seos')->insert($data);
    }
}
