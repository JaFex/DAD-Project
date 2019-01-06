<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Resources\Order as OrderResource;
use App\Order;
use App\Http\Resources\User as UserResource;
use App\User;
use App\Http\Resources\Meal as MealResource;
use App\Meal;
use App\Item;
use App\Rules\MealIsActive;
use Carbon\Carbon;
use DateTime;
use DatePeriod;
use DateInterval;

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
     * Display a listing of the resource.
     *
     * @param  int  $order_id
     * @return \Illuminate\Http\Response
     */
    public function getWaiter(int $order_id)
    {
        $order = Order::findOrFail($order_id);
        $meal = $order->meal;
        $user = $meal->responsible_waiter;
        return new UserResource($user);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $order_id
     * @return \Illuminate\Http\Response
     */
    public function getMeal(int $order_id)
    {
        $order = Order::findOrFail($order_id);
        $meal = $order->meal;
        return new MealResource($meal);
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
        $this->validate(request(), [
            'state' => 'required|in:confirmed,in preparation,prepared,delivered,not delivered'
        ]);
        if($request["state"] === "delivered" || $request["state"] === "not delivered") {
            $request['end'] = Carbon::now();
        }else if($request["state"] === "in preparation") {
            $request['responsible_cook_id'] = \Auth::guard('api')->user()->id;
        }
        
        $order = Order::findOrFail($order_id);
        $order->update($request->all());
        $order->save();
        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $order_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_id)
    {
        $dateNow = Carbon::now();
        $now = Carbon::now();
        $dateNow->sub(new DateInterval('PT5S'));

        $order = Order::findOrFail($order_id);
        $dateOrder = $order->start;//date('Y-m-d H:i:s',+);
        
        $meal = $order->meal;
        $meal->total_price_preview = $meal->total_price_preview - $order->item->price;
        $meal->save();

        if ($dateNow > $dateOrder) {
            return response([
                'order_id' => $order->id,
                'message' => "You missed the amount of time to remove!",
                'test' => [['Now time', $now], ['Now -5 secunds', $dateNow], ['Date Order', $dateOrder]]
            ], 401);
        }
        $order->delete();
        
        return new OrderResource($order);
    }

    public function ordersCooksAvarage()
    {
        return DB::query()->fromSub(function ($query) {
            $query->from('orders')
                ->select('responsible_cook_id', DB::raw('Date(start) as date'), DB::raw('COUNT(*) as total'))->groupBy('date', 'responsible_cook_id');}, 'a')
                ->select('a.responsible_cook_id', DB::raw('AVG(a.total) as average'))
                ->groupBy('a.responsible_cook_id')
                ->get();   
    }

    public function ordersByMonth(){
        return Order::select(DB::raw("count(*) as 'total', DATE_FORMAT(start, '%m-%Y') date"))->groupBy('date')->get();
    }
}
