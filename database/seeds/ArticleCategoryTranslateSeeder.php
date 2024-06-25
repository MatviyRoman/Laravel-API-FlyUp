<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleCategoryTranslateSeeder extends Seeder
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
			    'article_category_id' => 1,
			    'language_id' => 1,
			    'title' => 'Редизайн сайтов',
			    'keywords' => 'egc, блог, услуги, редизайн сайтов, keywords',
			    'description' => 'EGC Редизайн сайтов description',
			    'url' => 'redizayn-saytov',
		    ],
		    [
			    'article_category_id' => 1,
			    'language_id' => 2,
			    'title' => 'Website Redesign',
			    'keywords' => 'blog, services, website redesign, keywords',
			    'description' => 'Site redesign description',
			    'url' => 'redesign-of-sites',
		    ],
		    [
			    'article_category_id' => 2,
			    'language_id' => 1,
			    'title' => 'Техническая поддержка',
			    'keywords' => 'блог, услуги, техническая поддержка, keywords',
			    'description' => 'Техническая поддержка услуги description',
			    'url' => 'tekhnicheskaya-podderzhka',
		    ],
		    [
			    'article_category_id' => 3,
			    'language_id' => 1,
			    'title' => 'Seo продвижение',
			    'keywords' => 'блог, услуги, seo, продвижение, keywords',
			    'description' => 'Seo продвижение услуги description',
			    'url' => 'seo-prodvizheniye',
		    ]
	    ];

	    DB::table('article_category_translates')->insert($data);
    }
}
