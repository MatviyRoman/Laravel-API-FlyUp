<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use GuzzleHttp;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Http\Resources\PageCollection;
use App\Http\Resources\PageAll as PageResource;
use App\InterfaceTranslate;
use App\Language;
use App\Page;
use App\Seo;
use DB;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{

	public function index(Request $request)
	{
		if ($request->number && $request->number != 0)
			$number = $request->number;
		else
			$number = env('DEFAULT_SERVICES_PER_PAGE', 20);
		return new PageCollection(
			Page::with('seos')->with('languages')->paginate($number)
		);
	}

	public function edit(Request $request, $id)
	{
		if ($request->has(['language_id'])) {
			if (Language::find($request->language_id)) {
				if (Seo::where('page_id', $id)->where('language_id', $request->language_id)->count()) {
					return new PageResource(
						Seo::where('page_id', $id)
							->where('language_id', $request->language_id)
							->first()
					);
				} else {
					$data = [
						'id' => $id,
						'url' => '',
						'title' => '',
						'keywords' => '',
						'description' => ''
					];
					return [
						'data' => $data,
						'language' => DB::table('languages')->where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
						'languages' => DB::table('languages')->orderBy('order')->select('id', 'name', 'flag')->get()
					];
				}
			} else {
				return response('Language does not exist', 400);
			}
		} else {
			return response('Need language_id', 400);
		}
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'language_id' => 'required|numeric|exists:languages,id',
			'page_id' => 'required|numeric|exists:pages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$data = $request->data;
		if (array_key_exists('url', $data) && $request->page_id != 1) {
			if ($request->page_id == 2) {
				$data['url'] = MainController::getUrl(InterfaceTranslate::getTranslate(3, $request->language_id)) . "/" . MainController::getUrl($request->data['url']);
			} else {
				$data['url'] = MainController::getUrl($request->data['url']);
			}
		}
		Seo::updateOrCreate(
			['language_id' => $request->language_id, 'page_id' => $request->page_id],
			$data
		);
		return response('', 200);
	}

	public function show($lang = 'ru')
	{
		$result['lang'] = $lang;

		$lang = Language::where('name', $lang)->select('id')->firstOrFail();
		$result['langs'] = Language::orderBy('order')->select('id', 'name', 'flag')->get();

		$pages = DB::table('pages')
			->join('seos', 'pages.id', '=', 'seos.page_id')
			->where('seos.language_id', $lang->id)
			->select('pages.id', 'pages.name', 'seos.url', 'seos.title', 'seos.keywords', 'seos.description')
			->get();

		$services = DB::table('services')
			->where('services.is_active', 1)
			->join('service_translates', 'services.id', '=', 'service_translates.service_id')
			->where('service_translates.language_id', $lang->id)
			->orderBy('services.order')
			->select('services.name', 'service_translates.url', 'service_translates.title', 'service_translates.keywords',
				'service_translates.description', 'service_translates.caption', 'service_translates.content', 'services.icon')
			->get();

		foreach ($pages as $key => $page) {
			$result['pages'][$page->name]['id'] = $page->id;
			$result['pages'][$page->name]['url'] = $page->url;
			$result['pages'][$page->name]['title'] = $page->title;
			$result['pages'][$page->name]['seo'][$i = 0]['name'] = 'keywords';
			$result['pages'][$page->name]['seo'][$i++]['content'] = $page->keywords;
			$result['pages'][$page->name]['seo'][$i]['name'] = 'description';
			$result['pages'][$page->name]['seo'][$i]['content'] = $page->description;
		}

		foreach ($services as $key => $service) {
			$result['services'][$key]['name'] = $service->name;
			$result['services'][$key]['url'] = $service->url;
			$result['services'][$key]['icon'] = $service->icon;
			$result['services'][$key]['caption'] = $service->caption;
			$result['services'][$key]['content'] = $service->content;
			$result['services'][$key]['title'] = $service->title;
			$result['services'][$key]['seo'][$i = 0]['name'] = 'keywords';
			$result['services'][$key]['seo'][$i++]['content'] = $service->keywords;
			$result['services'][$key]['seo'][$i]['name'] = 'description';
			$result['services'][$key]['seo'][$i]['content'] = $service->description;
		}

		$result['interface'] = DB::table('interface_entities')
			->join('interface_translates', 'interface_entities.id', '=', 'interface_translates.interface_entity_id')
			->where('interface_translates.language_id', '=', $lang->id)
			->select('interface_entities.name', 'interface_translates.value')
			->pluck('value', 'name');

		return response($result, 200);
	}

	public function getRoutes()
	{
		$result['pages'] = DB::table('pages')
			->join('seos', 'pages.id', '=', 'seos.page_id')
			->join('languages', 'seos.language_id', '=', 'languages.id')
			->select('languages.name as lang', 'seos.url', 'pages.name as component')
			->get();

		$result['services'] = DB::table('services')
			->where('services.is_active', 1)
			->join('service_translates', 'services.id', '=', 'service_translates.service_id')
			->join('languages', 'service_translates.language_id', '=', 'languages.id')
			->select('languages.name as lang', 'service_translates.url', 'services.name as component')
			->get();

		$result['articles'] = DB::table('articles')
			->where('articles.is_active', 1)
			->join('article_translates', 'articles.id', '=', 'article_translates.article_id')
			->join('languages', 'article_translates.language_id', '=', 'languages.id')
			->select('languages.name as lang', 'article_translates.url')
			->get();

		return response($result, 200);
	}
}
