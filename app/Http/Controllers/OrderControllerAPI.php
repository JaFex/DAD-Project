<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\Order as OrderResource;
use App\Order;

class OrderControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {//$request->user()->id
        return OrderResource::collection(Order::whereIn('state', ['confirmed', 'in preparation'])->where('responsible_cook_id', '66')->orWhere('state', 'confirmed')->whereNull('responsible_cook_id')->orderBy('state', 'desc')->orderBy('start', 'asc')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
