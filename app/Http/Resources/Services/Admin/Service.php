<?php

namespace App\Http\Resources\Services\Admin;

use App\Http\Resources\Language;
use App\Service as ServiceModel;
use App\ServiceCategoryTranslate;
use Illuminate\Http\Resources\Json\Resource;
use App\ArticleAuthorTranslate;

class Service extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
	    return [
		    'id' => $this->id,
		    'is_active' => $this->is_active,
		    'order' => $this->order,
		    'image' => $this->image,
            'price' => $this->price,
            'price2' => $this->price2,
            'price3' => $this->price3,
            'is_delivery_required' => $this->is_delivery_required,
            'is_no_price' => $this->is_no_price,
		    'icon' => $this->icon,
		    'views' => $this->views,
		    'likes' => $this->likes,
		    'name' => $this->title,
		    'url' => $this->url,
            'article_category' => ServiceCategoryTranslate::where('service_category_id', $this->service_category_id)
                ->where('language_id', env('DEFAULT_LANG_ID', 1))
                ->select('service_category_id as id', 'title')
                ->first(),
		    'languages' => Language::collection(ServiceModel::find($this->id)->languages),
	    ];
    }
}
