<?php

namespace App\Http\Controllers;

use App\ArticleCategory;
use App\Language;
use App\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/api/user/blog/search",
     *     tags={"User Blog"},
     *     summary="Search article",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="language_id",
     *          type="integer",
     *          in="query",
     *     ),
     *     @SWG\Parameter(
     *          name="value",
     *          type="string",
     *          in="query"
     *     ),
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=401, description="Unauthenticated"),
     *     @SWG\Response(response=404, description="Resource Not Found"),
     * )
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function searchBlog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric|exists:languages,id',
            'value' => 'required|string|max:255',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        App::setLocale(Language::find($request->language_id)->name);

        // article is active

        $result['articles'] = DB::table('articles')
            ->where('articles.is_active', 1)
            ->join('article_translates', 'articles.id', '=', 'article_translates.article_id')
            ->where('article_translates.language_id', $request->language_id)
            ->whereIn('articles.article_category_id', ArticleCategory::where('is_active', 1)->pluck('id'))
            ->where('article_translates.title', 'LIKE', '%' . $request->value . '%')
            ->join('article_category_translates', 'articles.article_category_id', '=', 'article_category_translates.article_category_id')
            ->where('article_category_translates.language_id', $request->language_id)
            ->orderBy('articles.views', 'desc')
            ->select('articles.id', 'article_translates.title', 'article_translates.url', 'article_category_translates.url as category')
            ->take(5)
            ->get();

        $result['services'] = DB::table('services')
            ->where('services.is_active', 1)
            ->join('service_translates', 'services.id', '=', 'service_translates.service_id')
            ->where('service_translates.language_id', $request->language_id)
            ->where('service_translates.title', 'LIKE', '%' . $request->value . '%')
            ->orderBy('services.views', 'desc')
            ->select('services.id',
                'service_translates.title',
                'service_translates.url')
            ->take(5)
            ->get();

        return response($result, 200);
    }

    public static function getUrl($str)
    {
        if ($str == null)
            return null;
        $translit = array(
            "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ё" => "e", "Ж" => "zh", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "ts", "Ч" => "ch", "Ш" => "sh", "Щ" => "shch", "Ъ" => "", "Ы" => "y", "Ь" => "", "Э" => "e", "Ю" => "yu", "Я" => "ya",
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "shch", "ъ" => "", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
            "A" => "a", "B" => "b", "C" => "c", "D" => "d", "E" => "e", "F" => "f", "G" => "g", "H" => "h", "I" => "i", "J" => "j", "K" => "k", "L" => "l", "M" => "m", "N" => "n", "O" => "o", "P" => "p", "Q" => "q", "R" => "r", "S" => "s", "T" => "t", "U" => "u", "V" => "v", "W" => "w", "X" => "x", "Y" => "y", "Z" => "z"
        );
        $str = strtr($str, $translit);
        $str = preg_replace("/[^a-zA-Z0-9_]/i", "-", $str);
        $str = preg_replace("/\-+/i", "-", $str);
        $str = preg_replace("/(^\-)|(\-$)/i", "", $str);

        return $str;
    }

    public static function sessionIncrement($model, $column = 'views', $amount = 1)
    {
        if ($model->id && isset($model->$column)) {
            $session = array();
            $session = Session::get($model->getTable() . '.' . $column);
            if ($session) {
                if (!in_array($model->id, $session)) {
                    Session::push($model->getTable() . '.' . $column, $model->id);
                    $model->increment($column, $amount);
                    Setting::find(1)->increment('all_views');
                }
            } else {
                $session[] = $model->id;
                Session::put($model->getTable() . '.' . $column, $session);
                $model->increment($column, $amount);
                Setting::find(1)->increment('all_views');
            }
        } else {
            die('sessionIncrement columns error');
        }
    }

    public static function isLike($table, $id)
    {
        if ($id) {
            $session = Session::get($table . '.likes');
            if ($session) {
                if (in_array($id, $session)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @SWG\Get(
     *     path="/api/pages",
     *     tags={"User Page"},
     *     summary="Get pages",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Bad request"),
     *     @SWG\Response(response=401, description="Unauthenticated"),
     *     @SWG\Response(response=404, description="Resource Not Found"),
     * )
     */
}
