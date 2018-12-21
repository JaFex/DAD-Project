<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Meal as MealResource;
use App\Http\Resources\Order as OrderResource;
use App\Meal;
use Carbon\Carbon;

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
     * Display a listing of the resource.
     *  @param  int  $meal
     * @return \Illuminate\Http\Response
     */
    public function orders(int $meal_id)
    {
        $meal = Meal::findOrFail($meal_id);
        return OrderResource::collection($meal->orders()->whereIn('state', ['pending', 'confirmed'])->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *  @param  int  $meal
     * @return \Illuminate\Http\Response
     */
    public function toDelived(int $meal_id)
    {
        $meal = Meal::findOrFail($meal_id);
        return OrderResource::collection($meal->orders()->whereIn('state', ['prepared', 'delivered', 'not delivered'])->orderBy('state', 'asc')->paginate(10));
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
            'table_number' => 'required|exists:restaurant_tables,table_number|unique:meals,table_number,NULL,id,state,active'
        ]);
        $request['start'] = Carbon::now();
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
        return MealResource::collection(Meal::findOrFail($meal_id));
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
     * @param  int $meal_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $meal_id)
    {
        /*$meal = Meal::findOrFail($meal_id);
        $meal->update($request->all());
        $meal->save();
        return new MealResource($meal);*/
    }


     /**
     * Update the specified resource in storage.
     *
     * @param  int $meal_id
     * @return \Illuminate\Http\Response
     */
    public function canBeTerminated(int $meal_id)
    {
        $meal = Meal::findOrFail($meal_id);

        foreach ($meal->orders as $order){
            if($order->state !== "delivered"){
                return response([
                    'terminated' => false
                ], 200);
            }
        }
        return response([
            'terminated' => true
        ], 200);
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $meal_id
     * @return \Illuminate\Http\Response
     */
    public function terminated(Request $request, int $meal_id)
    {
        $this->validate(request(), [
            'state' => 'required|in:terminated'
        ]);
        $meal = Meal::findOrFail($meal_id);
        $request['end'] = Carbon::now();
        $meal->update($request->all());
        foreach ($meal->orders as $order){
            if($order->state !== "delivered" && $order->state !== "not delivered" ){
                $order->state = "not delivered";
                $order->end = Carbon::now();
                $order->save();
                $meal->total_price_preview = $meal->total_price_preview - $order->item->price;
            } else if($order->state === "delivered") {
                
            }
        }
        
        $meal->save();
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
