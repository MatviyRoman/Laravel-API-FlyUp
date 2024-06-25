<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Image;
use Storage;

class StorageController extends Controller
{

	/**
	 * @SWG\Post(
	 *     path="/api/uploadfile",
	 *     tags={"File"},
	 *     summary="Upload file",
	 *     consumes={"multipart/form-data"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          in="formData",
	 *          name="file",
	 *          required=true,
	 *          type="file"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="formData",
	 *          name="folder",
	 *          type="string"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="use_case",
	 *          description="use case",
	 *          type="string",
	 *          in="query",
	 *          enum={"icon", "seo", "cover"}
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	public function uploadFile(Request $request)
	{
		$validator = Validator::make($request->all() ,[
			'file' => 'required',
			'size' => 'numeric',
		]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $fileInfo = pathinfo($request->file->getClientOriginalName());

        $filename = $request->file('file')->hashName();

        if ($fileInfo['extension'] == 'svg') {
            $filename .= 'svg';
        }

        $path = Storage::disk('uploads')->putFileAs($request->folder, $request->file('file'), $filename);
        $size = 2000;
		Storage::disk('public')->makeDirectory('min/uploads/' . $request->folder);

		if ($request->has('use_case')) {
			switch ($request->use_case) {
				case 'icon':
					break;
				case 'seo':
					self::compressImage($path, $size);
					break;
				case 'cover':
					if(!is_dir('min/uploads/' . $request->folder))
						Storage::disk('public')->makeDirectory('min/uploads/' . $request->folder);
					self::saveMinImage($path, $size, 500);
					break;
			}
		}

		return response()->json(['url' => 'uploads/' . $path], 200);
	}

	public function uploadCKEditorImage()
	{
		$CKEditor = Input::get('CKEditor');
		$funcNum = Input::get('CKEditorFuncNum');
		$url = '';
		if (Input::hasFile('upload')) {
			$file = Input::file('upload');
			if ($file->isValid()) {
				$path = Storage::disk('uploads')->put('images', $file);
				$img = Image::make('uploads/' . $path);
				if ($img->height() > $img->width() && $img->height() > 1000) {
					$img->heighten(1000);
				} elseif ($img->width() > $img->height() && $img->width() > 1000) {
					$img->widen(1000);
				}
				$img->save('uploads/' . $path, 75);
				$url = url('uploads/' . $path);
			} else {
                return ['uploaded' => false, 'error' => ['message' => 'An error occured while uploading the file.'] ];
			}
		} else {
            return ['uploaded' => false, 'error' => ['message' => 'No file uploaded.'] ];
		}

		return ['uploaded' => true, 'url' => $url ];
	}

	public static function compressImage($path, $size)
	{
		$image = Image::make('uploads/' . $path);
		if($image->height() >= $image->width() && $image->height() > $size)
			$image->heighten($size);
		elseif($image->width() >= $image->height() && $image->width() > $size)
			$image->widen($size);
		$image->save('uploads/' . $path, 80);
	}

	public static function saveMinImage($path, $size, $min = 500)
	{
		$image = Image::make('uploads/' . $path);
		if($image->height() >= $image->width() && $image->height() > $min)
			$image->heighten($min);
		elseif($image->width() >= $image->height() && $image->width() > $min)
			$image->widen($min);
		$image->save('min/uploads/' . $path, 80);

		self::compressImage($path, $size);
	}

	/**
	 * @SWG\Delete(
	 *     path="/api/destroyfile",
	 *     tags={"File"},
	 *     summary="Remove file from storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="url",
	 *          description="File url",
	 *          required=true,
	 *          type="string",
	 *          in="query"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroyFile(Request $request)
	{
		$validator = Validator::make($request->all() ,[
			'url' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 400);
		}
		if (unlink($request->url))
			return response('Successful operation', 200);
		else
			return response('Bad request', 400);
	}
}
