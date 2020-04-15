<?php

namespace App\Http\Controllers;

use App\Models\SpecialPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecialPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $specialPrices = SpecialPrice::with('client', 'inventory')->get();
        $json = json_decode($specialPrices, true);

        return $json;
    }

    public function getActivePrices() {
        $specialPrices = SpecialPrice::with('client', 'inventory')->where('state', 1)->get();
        $json = json_decode($specialPrices, true);

        return $json;
    }

    public function getPricesByInventory($inventoryId)
    {
        $specialPrices = SpecialPrice::with('client', 'inventory')->where('inventory_id', $inventoryId)->get();
        $json = json_decode($specialPrices, true);

        return $json;
    }
    
    public function getPricesByClient($clientId)
    {
        $specialPrices = SpecialPrice::with('client', 'inventory')->where('client_id', $clientId)->get();
        $json = json_decode($specialPrices, true);

        return $json;
    }

    public function getPriceByInventoryAndClient($inventoryId, $clientId)
    {
        $specialPrice = SpecialPrice::where('client_id', $clientId)->where('inventory_id', $inventoryId)->first();
        
        if(!$specialPrice) {
            return response()->json(['No se encontró el precio.'], 404);
        }
        
        $json = json_decode($specialPrice, true);

        return $json;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Fetching the price, if it exists
        $specialPrice = SpecialPrice::where('inventory_id', $request['inventory_id'])->where('client_id', $request['client_id'])->first();
        
        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'inventory_id' => array('required', 'exists:inventories,id','numeric'),
                'client_id'    => array('required', 'exists:clients,id','numeric'),
                'price'        => array('required', 'regex:/^\d+(\.\d{1,2})?$/'),
                'state'        => array('required', 'boolean')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //If it didn't exist, lets create a new one
        if(!$specialPrice) {
            $specialPrice = New SpecialPrice();
        }

        $specialPrice->fill($request->all());

        $specialPrice->save();

        return response()->json($specialPrice);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SpecialPrice  $specialPrice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $specialPrice = SpecialPrice::with('client', 'inventory')->find($id);

        if(!$specialPrice) {
            return response()->json(['No se encontró el precio.'], 404);
        }

        $json = json_decode($specialPrice, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SpecialPrice  $specialPrice
     * @return \Illuminate\Http\Response
     */
    public function edit(SpecialPrice $specialPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SpecialPrice  $specialPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SpecialPrice $specialPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SpecialPrice  $specialPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpecialPrice $specialPrice)
    {
        //
    }
}
