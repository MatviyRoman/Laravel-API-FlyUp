<?php

namespace App\Observers;

use App\ArticleTranslate;
use App\Http\Controllers\MainController;

class ArticleObserver
{
    /**
     * Handle the article translate "created" event.
     *
     * @param  \App\ArticleTranslate  $articleTranslate
     * @return void
     */
    public function created(ArticleTranslate $articleTranslate)
    {
	    MainController::getSitemap();
    }

    /**
     * Handle the article translate "updated" event.
     *
     * @param  \App\ArticleTranslate  $articleTranslate
     * @return void
     */
    public function updated(ArticleTranslate $articleTranslate)
    {
	    if($articleTranslate->getOriginal('url') != $articleTranslate->url)
		    MainController::getSitemap();
    }

	/**
	 * Handle the User "deleted" event.
	 *
	 * @param  \App\User  $user
	 * @return void
	 */
	public function deleted(ArticleTranslate $articleTranslate)
	{
		MainController::getSitemap();
	}
}
