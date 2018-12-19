<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\Order as OrderResource;
use App\Order;
use App\Meal;
use App\Item;
use App\Rules\MealIsActive;
use Carbon\Carbon;

class OrderControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OrderResource::collection(Order::whereIn('state', ['confirmed', 'in preparation'])->where('responsible_cook_id', \Auth::guard('api')->user()->id)->orWhere('state', 'confirmed')->whereNull('responsible_cook_id')->orderBy('state', 'desc')->orderBy('start', 'asc')->paginate(10));
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
            'item_id' => 'required|exists:items,id',
            'meal_id' => ['required', 'exists:meals,id', new MealIsActive]
        ]);
        $meal = Meal::findOrFail($request["meal_id"]);
        $item = Item::findOrFail($request["item_id"]);
        $meal->total_price_preview = $meal->total_price_preview + $item->price;
        $meal->save();
        $request['state'] = 'pending';
        $request['responsible_cook_id'] = NULL;
        $request['start'] = Carbon::now();
        $request['end'] = NULL;
        
        return new OrderResource(Order::create($request->all()));
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
     * @param  int $order_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $order->update($request->all());
        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
