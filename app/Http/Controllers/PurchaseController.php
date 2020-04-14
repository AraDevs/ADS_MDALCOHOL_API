<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchases = Purchase::get();
        $json = json_decode($purchases, true);

        return $json;
    }

    public function getActivePurchases() {
        $purchases = Purchase::where('state',1)->get();
        $json = json_decode($purchases, true);

        return $json;
    }

    public function getDeletedPurchases() {
        $purchases = Purchase::where('state',0)->get();
        $json = json_decode($purchases, true);

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
        $today = Carbon::now()->format('d/m/Y');

        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'purchase_date' => array('required','date_format:d/m/Y', 'before_or_equal:' . $today),
                'payment_type'  => array('required','in:Contado,Crédito'),
                'perception'    => array('required','boolean'),
                'purchase_item' => array('required'),

                'purchase_item.*.inventory_id' => array('required','distinct','exists:inventories,id','numeric'),
                'purchase_item.*.price'        => array('required', 'regex:/^\d+(\.\d{1,2})?$/'),
                'purchase_item.*.quantity'     => array('required', 'min:1', 'numeric')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //Fetching the inventories to check if they are raw materials
        foreach($request['purchase_item'] as $purchaseItem) {
            $inventory = Inventory::find($purchaseItem['inventory_id']);
            if($inventory->state == 0) {
                return response()->json(['El producto ' . $inventory->name . ' no se encuentra activo.'], 422);
            }
            if($inventory->rawMaterial == null) {
                return response()->json([$inventory->name . ' no es una materia prima.'], 422);
            }
        }

        //Changing date format
        $purchase_date = Carbon::createFromFormat('d/m/Y', $request->purchase_date);

        //Saving purchase
        $purchase = New Purchase();
        $purchase->fill($request->all());
        $purchase->purchase_date = $purchase_date->toDateString();
        $purchase->state = 1;

        $purchase->save();

        //Saving each purchase item
        foreach($request['purchase_item'] as $pi) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->purchase_id = $purchase->id;
            $purchaseItem->inventory_id = $pi['inventory_id'];
            $purchaseItem->price = $pi['price'];
            $purchaseItem->quantity = $pi['quantity'];

            $purchaseItem->save();

            //Updating inventory's stock
            InventoryController::addStocks($pi['inventory_id'],$pi['quantity']);
        }

        //Rollbacking date formats to d/m/Y
        $purchase->purchase_date = $request->purchase_date;

        return response()->json($purchase);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase = Purchase::with('purchaseItem', 'purchaseItem.inventory', 'purchaseItem.inventory.rawMaterial.provider.partner')->find($id);

        if(!$purchase) {
            return response()->json(['No se encontró la compra.'], 404);
        }

        //Calculating extra fields
        $subtotal = 0;
        $perception_value = 0;
        $total = 0;

        foreach($purchase->purchaseItem as $purchaseItem){
            $subtotal = $subtotal + ($purchaseItem->price * $purchaseItem->quantity);
        }
        if($purchase->perception == 1) {
            $perception_value = $subtotal * 0.1;
        }

        $total = $subtotal + $perception_value;

        $purchase->subtotal = number_format($subtotal,2);
        $purchase->perception_value = number_format($perception_value,2);
        $purchase->total = number_format($total,2);

        $json = json_decode($purchase, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        //Fetching the purchase
        $purchase = Purchase::find($request['id']);

        if(!$purchase) {
            return response()->json(['No se encontró la compra.'], 404);
        }
        if($purchase->state == 0) {
            return response()->json(['La compra ya había sido eliminada.'], 422);
        }

        //Invalidating the purchase
        $purchase->state = 0;
        $purchase->save();

        foreach($purchase->purchaseItem as $pi) {
            //Updating inventory's stock
            InventoryController::removeStocks($pi->inventory_id,$pi->quantity);
        }

        return response()->json($purchase);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
