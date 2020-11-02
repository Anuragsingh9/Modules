<?php

namespace App\Http\Controllers;

use App\Http\Resources\SwaggerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Swagger;
/**
 * @OA\Info(title="My First API", version="0.1")
 */
class SwagerDemoController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('newsletter::index');
    }



    /**
     * @SWG\Post(
     *      path="/api/swagger/demo",
     *      operationId="Post testing",
     *      summary="Add User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="name",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="address",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Example extended response",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/Http/Resources/SwaggerResource"
     *              )
     *          )
     *     ),
     *     )
     */
    public function create(Request $request)
    {
        $param = [
        'name' => $request->name,
        'email' => $request->email,
        'address' => $request->address,
        ];
         $swg = Swagger::create($param);
//        return new SwaggerResource($swg);
        return (new SwaggerResource($swg));

    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * @SWG\Get(
     *   path="/get/demo",
     *   summary="Get Testing",
     *   operationId="testing",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          type="string"
     *      ),
     * )
     *
     */
    public function show()
    {
        $news = Swagger::get();
        return $news;
//        return view('newsletter::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('newsletter::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
