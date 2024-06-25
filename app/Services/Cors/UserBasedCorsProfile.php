<?php
namespace App\Services\Cors;

use Spatie\Cors\CorsProfile\DefaultProfile;

class UserBasedCorsProfile extends DefaultProfile
{
	public function addCorsHeaders($response)
	{
		return $response
			->header('Access-Control-Allow-Origin', $this->allowedOriginsToString())
			->header('Access-Control-Allow-Credentials', 'true')
			->header('Access-Control-Allow-Headers', '*')
			->header('Access-Control-Expose-Headers', '*');
	}

	public function addPreflightHeaders($response)
	{
		return $response
			->header('Access-Control-Allow-Methods', $this->toString($this->allowMethods()))
			->header('Access-Control-Allow-Credentials', 'true')
			->header('Access-Control-Allow-Headers', '*')
			->header('Access-Control-Allow-Origin', $this->allowedOriginsToString())
			->header('Access-Control-Expose-Headers', '*')
			->header('Access-Control-Max-Age', $this->maxAge());
	}
}