<?php

namespace App\Observers;

use App\Http\Controllers\MainController;
use App\Seo;

class PageObserver
{
    /**
     * Handle the seo "created" event.
     *
     * @param  \App\Seo  $seo
     * @return void
     */
    public function created(Seo $seo)
    {
	    MainController::getSitemap();
    }

    /**
     * Handle the seo "updated" event.
     *
     * @param  \App\Seo  $seo
     * @return void
     */
    public function updated(Seo $seo)
    {
	    if($seo->getOriginal('url') != $seo->url) {
		    MainController::getSitemap();
	    }
    }

	/**
	 * Handle the User "deleted" event.
	 *
	 * @param  \App\User  $user
	 * @return void
	 */
	public function deleted(Seo $seo)
	{
		MainController::getSitemap();
	}
}
