<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $this->call(LanguageSeeder::class);
//	    $this->call(InterfaceEntitySeeder::class);
//	    $this->call(InterfaceTranslateSeeder::class);
//	    $this->call(PageSeeder::class);
//	    $this->call(PageInterfaceEntitiesSeeder::class);
//	    $this->call(SeoSeeder::class);
//	    $this->call(FeedbackSeeder::class);
	    $this->call(PassportSeeder::class);
//	    $this->call(ServiceSeeder::class);
//	    $this->call(ServiceTranslateSeeder::class);
//	    $this->call(ArticleCategorySeeder::class);
//	    $this->call(ArticleCategoryTranslateSeeder::class);
    }
}
