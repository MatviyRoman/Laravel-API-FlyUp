<?php

namespace App\Observers;

use App\Http\Controllers\MainController;
use App\ServiceTranslate;

class ServiceObserver
{
    /**
     * Handle the service translate "created" event.
     *
     * @param  \App\ServiceTranslate  $serviceTranslate
     * @return void
     */
    public function created(ServiceTranslate $serviceTranslate)
    {
	    MainController::getSitemap();
    }

    /**
     * Handle the service translate "updated" event.
     *
     * @param  \App\ServiceTranslate  $serviceTranslate
     * @return void
     */
    public function updated(ServiceTranslate $serviceTranslate)
    {
	    if($serviceTranslate->getOriginal('url') != $serviceTranslate->url)
		    MainController::getSitemap();
    }

	/**
	 * Handle the User "deleted" event.
	 *
	 * @param  \App\User  $user
	 * @return void
	 */
	public function deleted(ServiceTranslate $serviceTranslate)
	{
		MainController::getSitemap();
	}
}
