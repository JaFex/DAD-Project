<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\Invoice as InvoiceResource;
use App\Http\Resources\InvoiceItem as InvoiceItemResource;
use App\Invoice;
use App\Item;
use PDF;
use App\Meal;

class InvoiceControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(String $state)
    {

        
        if($state !== 'paid' && $state !== 'pending' && $state !== 'not paid') {
            return response([
                'data' => 'Not Found Data'
            ], 404);
        }
        if($state === 'paid') {
            return InvoiceResource::collection(Invoice::where('state', $state)->orderBy('date', 'desc')->paginate(10)); 
        }
        return InvoiceResource::collection(Invoice::where('state', $state)->orderBy('date', 'asc')->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter()
    {
        $array = [];
        
        if(isset($_GET['pending']) && $_GET['pending'] == 'true') {
            array_push($array,"pending");
        }
        if(isset($_GET['paid']) && $_GET['paid'] == 'true') {
            array_push($array,"paid");
        }
        if(isset($_GET['not_paid']) && $_GET['not_paid'] == 'true') {
            array_push($array,"not paid");
        }
        if (empty($array)) {
            array_push($array, "pending");
        }

        if(!isset($_GET['date']) && !isset($_GET['waiter'])) {
            return InvoiceResource::collection(Invoice::whereIn('state', $array)->orderBy('invoices.date', 'desc')->paginate(10));
        }

        if(isset($_GET['waiter'])) {
            $sql = Invoice::join('meals', 'invoices.meal_id', '=', 'meals.id')->whereIn('invoices.state', $array)->where('meals.responsible_waiter_id', $_GET['waiter']);
        } else {
            $sql = Invoice::whereIn('state', $array);
        }
        if(isset($_GET['date'])) {
            $sql->whereDate('invoices.date', '=', date('Y-m-d',strtotime($_GET['date'])));
        }
        

        return InvoiceResource::collection($sql->orderBy('invoices.date', 'desc')->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *  @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function items(int $invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        return InvoiceItemResource::collection($invoice->invoice_items()->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *  @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $invoice_id)
    {
        $this->validate(request(), [
            'state' => 'required|in:paid,not paid',
            'name' => ['required', 'string', 'regex:/^[\pL\s]+$/u'],
            'nif' => 'required|numeric|min:100000000|max:999999999'
        ]);
        $invoice = Invoice::findOrFail($invoice_id);
        $array = null;
        $array['state'] = $request['state'];
        $invoice->meal->update($array);
        $invoice->update($request->all());
        return new InvoiceResource($invoice);
    }
    

    /**
     * Display a listing of the resource.
     *  @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF(int $invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        if($invoice->state !== 'paid') {
            return response([
                'data' => 'Invoice need to be paid for Download'
            ], 405);
        }
        $items = [];
        foreach($invoice->invoice_items as $invoice_item) {
            array_push($items,Item::withTrashed()->findOrFail($invoice_item->item_id));
        }
        $pdf = PDF::loadView('pdf', compact('invoice', 'items'));
        return $pdf->download('invoice.pdf');
    }

    public function notPaid(Request $request, int $invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $invoice->state = 'not paid';

        $invoice->meal->state = 'not paid';

        $invoice->save();
        
        return new InvoiceResource($invoice);
    }
}
