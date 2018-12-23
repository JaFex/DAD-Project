<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\Invoice as InvoiceResource;
use App\Http\Resources\InvoiceItem as InvoiceItemResource;
use App\Invoice;
use PDF;

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
        return InvoiceResource::collection(Invoice::where('state', $state)->orderBy('date', 'asc')->paginate(10));
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
        $pdf = PDF::loadView('pdf', compact('invoice'));
        return $pdf->download('invoice.pdf');
    }
}
