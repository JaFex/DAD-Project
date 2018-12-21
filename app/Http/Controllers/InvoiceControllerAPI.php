<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\Invoice as InvoiceResource;
use App\Invoice;

class InvoiceControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return InvoiceResource::collection(Invoice::where('state', 'pending')->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *  @param  int  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function items(int $invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        return InvoiceResource::collection($invoice->invoice_items()->paginate(10));
    }
}
