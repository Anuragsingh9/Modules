<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Newsletter\Entities\Swagger;
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
     * Show the form for creating a new resource.
     * @return Response
     */

    /**
     * @OA\Post(
     *     path="/newsletter/news/swagger/demo",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function create(Request $request)
    {
        $param = [
        'name' => $request->name,
        'email' => $request->email,
        'address' => $request->address,
        ];
         return Swagger::create($param);
//        dd($param);
//        return view('newsletter::create');
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
     * Show the specified resource.
     * @return Response
     */

    /**
     * @OA\Get(
     *     path="/newsletter/news/get/demo",
     *     @OA\Response(response="200", description="An example resource")
     * )
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
