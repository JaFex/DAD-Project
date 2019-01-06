<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Item as ItemResource;
use App\Item;
use Illuminate\Validation\Rule;



class ItemControllerAPI extends Controller
{

    public function index()
    {
        return ItemResource::collection(Item::withTrashed()->paginate(10));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return ItemResource::collection(Item::orderBy('type', 'desc')->paginate(8));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function get(int $item_id)
    {
        return new ItemResource(Item::withTrashed()->findOrFail($item_id));//::where('id', $item_id)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function getByType(string $type)
    {
        return ItemResource::collection(Item::where('type', $type)->orderBy('type', 'desc')->get());
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
            'description' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'type' => 'required', Rule::in(['drink', 'dish']),
            'file' => 'required|image'
        ]);

        $item = new Item();
        $item->description = $request['description'];
        $item->name = $request['name'];
        $item->price = $request['price'];
        $item->type = $request['type'];
        $item->photo_url = basename($request->file('file')->store('public/items'));
        $item->save();   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'description' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'type' => 'required', Rule::in(['drink', 'dish']),
        ]);

        if($request->file('file')){
            $request->validate([
                'file' => 'image'
            ]);
            if($item->photo_url != null){
                Storage::delete('public/items/'.$item->photo_url);
            }
            $item->photo_url = basename($request->file('file')->store('public/items'));
        }

        
        $item->description = $request['description'];
        $item->name = $request['name'];
        $item->price = $request['price'];
        $item->type = $request['type'];
        $item->save();
        return new ItemResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
    }

    public function restore($id)
    {
        $item = Item::withTrashed()->findOrFail($id);
        $item->restore();
    }
}
