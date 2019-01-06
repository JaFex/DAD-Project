<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Http\Resources\Meal as MealResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Summary as SummaryResource;
use App\Meal;
use App\Order;
use App\Item;
use App\Invoice;
use App\Invoice_item;
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

    public function indexByState(String $state)
    {

        
        if($state !== 'active' && $state !== 'terminated' && $state !== 'paid' && $state !== 'not paid') {
            return response([
                'data' => 'Not Found Data'
            ], 404);
        }
        //return InvoiceResource::collection(Invoice::where('state', $state)->orderBy('date', 'asc')->paginate(10));
        return MealResource::collection(Meal::where('state', $state)->orderBy('id', 'desc')->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter()
    {
        $array = [];
        
        if(isset($_GET['active']) && $_GET['active'] == 'true') {
            array_push($array,"active");
        }
        if(isset($_GET['terminated']) && $_GET['terminated'] == 'true') {
            array_push($array,"terminated");
        }
        if(isset($_GET['paid']) && $_GET['paid'] == 'true') {
            array_push($array,"paid");
        }
        if(isset($_GET['not_paid']) && $_GET['not_paid'] == 'true') {
            array_push($array,"not paid");
        }
        if (empty($array)) {
            array_push($array, "terminated", "active");
        }

        if(!isset($_GET['date']) && !isset($_GET['waiter'])) {
            return MealResource::collection(Meal::whereIn('state', $array)->orderBy('start', 'desc')->paginate(10));
        }
        $sql = Meal::whereIn('state', $array);
        if(isset($_GET['date'])) {
            $sql->whereDate('start', '=', date('Y-m-d',strtotime($_GET['date'])));
        }
        if(isset($_GET['waiter'])) {
            $sql->where('responsible_waiter_id', $_GET['waiter']);
        }

        return MealResource::collection($sql->orderBy('start', 'desc')->paginate(10));
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
    public function allOrders(int $meal_id)
    {
        $meal = Meal::findOrFail($meal_id);
        return OrderResource::collection($meal->orders()->orderBy('start', 'desc')->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *  @param  int  $meal
     * @return \Illuminate\Http\Response
     */
    public function summaryItems(int $meal_id)
    {
        $meal = Meal::findOrFail($meal_id);
        $summarys = null;
        $i = 0;
        foreach ($meal->orders()->orderBy('item_id', 'asc')->orderBy('id', 'asc')->get() as $order) {
            $item = Item::findOrFail($order->item_id);
            $array = null;
            $array['order_id'] = $order->id;
            $array['item_id'] = $item->id;
            $array['item_name'] = $item->name;
            $array['item_type'] = $item->type;
            $array['item_price'] = $item->price;
            $summarys[$i] = new SummaryResource($array);
            $i++;
        }
        //$collection = collect($summarys)->forPage(isset($_GET['page'])?$_GET['page']:1, 10);
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return SummaryResource::collection(MealControllerAPI::paginate($summarys, 10, null, ['path' => $actual_link]));
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
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
            if($order->state !== "delivered"){
                if($order->state !== "not delivered") {
                    $order->state = "not delivered";
                    $order->end = Carbon::now();
                    $order->save();
                }
                $meal->total_price_preview = $meal->total_price_preview - $order->item->price;
            }
        }
        $meal->save();

        $array = null;
        $array['meal_id'] = $meal->id;
        $array['state'] = "pending";
        $array['date'] = Carbon::now();
        $array['total_price'] = $meal->total_price_preview;
        $invoice = Invoice::create($array);
        foreach (Order::where('meal_id', $meal->id)->where('state', 'delivered')->select('item_id', DB::raw('count(*) as total'))->groupBy('item_id')->get() as $counter) {
            $array = null;
            $array['invoice_id'] = $invoice->id;
            $array['item_id'] = $counter->item_id;
            $array['quantity'] = $counter->total;
            $array['unit_price'] = Item::findOrFail($counter->item_id)->price;
            $array['sub_total_price'] = $array['unit_price']*$counter->total;
            Invoice_item::create($array);
        }
        
        
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

    public function notPaid(Request $request, int $meal_id)
    {
        $meal = Meal::findOrFail($meal_id);
        $meal->state = 'not paid';

        $meal->invoice->state = 'not paid';

        $orders = Order::where('meal_id', $meal_id)->get();

        foreach ($orders as $order) {
            if($order->state != 'delivered'){
                $order->state = 'not delivered';
                $order->save();
            } 
        }

        $meal->invoice->save();

        $meal->save();
        
        return new MealResource($meal);
    }
}
