<?php

namespace App\Observers;

use App\ArticleCategoryTranslate;
use App\Http\Controllers\MainController;

class ArticleCategoryObserver
{
    /**
     * Handle the article category translate "created" event.
     *
     * @param  \App\ArticleCategoryTranslate  $articleCategoryTranslate
     * @return void
     */
    public function created(ArticleCategoryTranslate $articleCategoryTranslate)
    {
	    MainController::getSitemap();
    }

    /**
     * Handle the article category translate "updated" event.
     *
     * @param  \App\ArticleCategoryTranslate  $articleCategoryTranslate
     * @return void
     */
    public function updated(ArticleCategoryTranslate $articleCategoryTranslate)
    {
	    if($articleCategoryTranslate->getOriginal('url') != $articleCategoryTranslate->url)
		    MainController::getSitemap();
    }

    /**
     * Handle the article category translate "deleted" event.
     *
     * @param  \App\ArticleCategoryTranslate  $articleCategoryTranslate
     * @return void
     */
    public function deleted(ArticleCategoryTranslate $articleCategoryTranslate)
    {
	    MainController::getSitemap();
    }
}
