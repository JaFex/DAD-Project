<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Meal as MealResource;
use App\Meal;

class MealControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MealResource::collection(Meal::whereNull('end')->where('responsible_waiter_id', \Auth::guard('api')->user()->id)->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'table_number' => 'required|exists:restaurant_tables,table_number|unique:meals,table_number,NULL,id,state,active',
            'start' => 'required|date'
        ]);
        $request['state'] = 'active';
        $request['total_price_preview'] = 0;
        $request['responsible_waiter_id'] = \Auth::guard('api')->user()->id;
        
        return new MealResource(Meal::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $meal
     * @return \Illuminate\Http\Response
     */
    public function show(int $meal_id)
    {
        return MealResource::collection(Meal::findOrFail($meal_id)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function edit(Meal $meal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $meal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $meal_id)
    {
        $meal = Meal::findOrFail($meal_id);
        $meal->update($request->all());
        return new MealResource($meal);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meal $meal)
    {
        //
    }
}
