<?php
/**
 * Created by PhpStorm.
 * User: doon
 * Date: 27.07.18
 * Time: 20:17
 */

namespace App\Http\Controllers\API;

use App\InterfaceGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use DB;
use App\Http\Resources\InterfaceGroups\Admin\InterfaceGroupCollection;
use App\Http\Controllers\MainController;

class InterfaceGroupController extends Controller
{
    public function index(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'page_id' => 'required|numeric|exists:pages,id',
//        ]);

//        if ($validator->fails())
//            return response()->json(['errors' => $validator->errors()], 400);

        $interfaceGroups = DB::table('interface_groups')
            ->where('interface_groups.page_id', $request->page_id);

//        if ($request->has('method') && $request->has('field')) {
//            $interfaceGroups->orderBy($request->field, $request->method);
//        } else {
//            $interfaceGroups->orderBy('order');
//        }

        return $interfaceGroups->select('id', 'name', 'title')->paginate(20);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
//            'page_id' => 'required|numeric|exists:pages,id',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $interfaceGroup = InterfaceGroup::create([
            'name' => MainController::getUrl($request->title),
            'title' => $request->title,
            'page_id' => $request->page_id
        ]);

        return response('Successful operation', 200);
    }

    public function updateField(Request $request, $id)
    {
        if (InterfaceGroup::find($id)->update([$request->field => $request->value]))
            return response('Successful operation', 200);
    }

    /**
     * @SWG\Delete(
     *     path="/api/interfaceGroup/{id}",
     *     tags={"InterfaceGroup"},
     *     summary="Remove interface group from storage",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="interface group id",
     *          type="integer",
     *          in="path"
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return InterfaceGroup::destroy($id);
    }
}