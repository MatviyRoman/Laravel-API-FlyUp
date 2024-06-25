<?php

namespace App\Http\Resources\ServiceCategories\Admin;

use App\Http\Resources\Language;
use App\ServiceCategory as ServiceCategoryModel;
use Illuminate\Http\Resources\Json\Resource;

class ServiceCategory extends Resource
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
		    'name' => $this->title,
		    'image' => $this->image,
		    'url' => $this->url,
		    'languages' => Language::collection(ServiceCategoryModel::find($this->id)->languages),
	    ];
    }
}
