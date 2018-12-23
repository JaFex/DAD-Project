<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RestaurantTable as RestaurantTableResource;
use App\RestaurantTable;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class RestaurantTableControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RestaurantTableResource::collection(RestaurantTable::all());
    }

    public function paginate()
    {
        return RestaurantTableResource::collection(RestaurantTable::withTrashed()->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|unique:restaurant_tables,table_number'
        ]);
        $table = new RestaurantTable();
        $table->fill($request->all());
        $table->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!RestaurantTable::where('table_number', $id)->exists()){
            throw new ModelNotFoundException();
        }
        RestaurantTable::where('table_number', $id)->delete();
        return response()->json(['msg'=>'Table deleted'], 200);

    }

    public function restore($id)
    {
        if(!RestaurantTable::withTrashed()->where('table_number', $id)->exists()){
            throw new ModelNotFoundException();
        }
        RestaurantTable::withTrashed()->where('table_number', $id)->restore();
        return response()->json(['msg'=>'Table restored'], 200);

    }
}
