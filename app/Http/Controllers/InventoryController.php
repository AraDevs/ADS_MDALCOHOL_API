<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\RawMaterial;
use App\Models\SpecialPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventories = Inventory::with('rawMaterial.provider')->get();
        $json = json_decode($inventories, true);

        return $json;
    }

    public function getActiveInventories() {
        $inventories = Inventory::where("state",1)->get();
        $json = json_decode($inventories, true);

        return $json;
    }

    public function getActiveFinalProducts() {
        $inventories = Inventory::where("type","Producto final")->where("state",1)->get();
        $json = json_decode($inventories, true);

        return $json;
    }

    public function getActiveRawMaterials() {
        $inventories = Inventory::where("type","Materia prima")->where("state",1)->with("rawMaterial.provider.partner")->get();
        $json = json_decode($inventories, true);

        return $json;
    }

    //Gets the list of inventories with the prices that are assigned to the given client id.
    public function getProductsByClient($clientId) {
        $inventories = Inventory::where("state",1)->where("type","Producto final")->get();

        foreach($inventories as $inventory) {
            $price = SpecialPrice::where("inventory_id",$inventory->id)->where("client_id",$clientId)->first();
            if($price != null) {
                if ($price->state == 1){
                    $inventory->price = $price->price;
                }
            }
        }

        $json = json_decode($inventories, true);

        return $json;
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
        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'name'            => array('required','min:4','max:100'),
                'description'         => array('required','min:4','max:500'),
                'price'         => array('required','regex:/^\d+(\.\d{1,2})?$/','min:0'),
                'stock'         => array('required','','min:0','integer'),
                'type'           => array('required', 'in:Materia prima,Producto final'),
                'state'         =>array('required', 'boolean')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inventory = New Inventory();
        $inventory->fill($request->all());
        $inventory->save();

        if($request->has('provider_id')){
            $raw = new RawMaterial();
            $raw->inventory_id = $inventory->id;
            $raw->provider_id = $request->provider_id;
            $raw->save();
        }

        return response()->json($inventory);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventory = Inventory::find($id);

        if(!$inventory) {
            return response()->json(['No se encontró el registro de inventario.'], 404);
        }

        $json = json_decode($inventory, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Fetching the inventory
        $inventory = Inventory::find($request['id']);

        if(!$inventory) {
            return response()->json(['No se encontró el registro de inventario.'], 404);
        }

        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'name'            => array('required','min:4','max:100'),
                'description'         => array('required','min:4','max:500'),
                'price'         => array('required','regex:/^\d+(\.\d{1,2})?$/','min:0'),
                'stock'         => array('required','','min:0','integer'),
                'state'         =>array('required', 'boolean')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inventory->fill($request->all());
        if($request->has('provider_id')){
            $raw = RawMaterial::find($request->raw_id);
            $raw->provider_id = $request->provider_id;
            $raw->save();
        }

        $inventory->save();

        return response()->json($inventory);
    }

    //TODO: Improve this function xd
    public static function addStocks($inventoryId, $qtyToAdd) {
        $inventory = Inventory::find($inventoryId);
        $inventory->stock = $inventory->stock + $qtyToAdd;
        $inventory->save();
    }

    public static function removeStocks($inventoryId, $qtyToRemove) {
        $inventory = Inventory::find($inventoryId);
        $inventory->stock = $inventory->stock - $qtyToRemove;
        $inventory->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
