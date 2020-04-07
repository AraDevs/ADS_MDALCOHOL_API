<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Client;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = Bill::with('client')->get();
        $json = json_decode($bills, true);

        return $json;
    }

    public function getActiveBills() {
        $bills = Bill::with('client')->where('state',1)->get();
        $json = json_decode($bills, true);

        return $json;
    }

    public function getDeletedBills() {
        $bills = Bill::with('client')->where('state',0)->get();
        $json = json_decode($bills, true);

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
                'client_id'    => array('required','exists:clients,id','numeric'),
                'bill_date'    => array('required','date_format:d/m/Y', 'before_or_equal:' . $today),
                'payment_type' => array('required','in:Contado,Credito'),
                'bill_type'    => array('required','in:Consumidor final,Credito fiscal,Notas de credito,Notas de debito'),
                'perception'   => array('required','boolean'),
                'bill_item'    => array('required'),

                'bill_item.*.inventory_id' => array('required','distinct','exists:inventories,id','numeric'),
                'bill_item.*.price'        => array('required', 'regex:/^\d+(\.\d{1,2})?$/'),
                'bill_item.*.quantity'     => array('required', 'min:1', 'numeric')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //Fetching the client to check if it is active
        $client = Client::find($request['client_id']);
        if($client->partner->state == 0) {
            return response()->json(['El cliente especificado no se encuentra activo.'], 422);
        }

        //Fetching the inventories to check if they are final products and if it has enough stock
        foreach($request['bill_item'] as $billItem) {
            $inventory = Inventory::find($billItem['inventory_id']);
            if($inventory->state == 0) {
                return response()->json(['El producto ' . $inventory->name . ' no se encuentra activo.'], 422);
            }
            if($inventory->rawMaterial != null) {
                return response()->json([$inventory->name . ' no es un producto final.'], 422);
            }
            if($inventory->stock < $billItem['quantity']) {
                return response()->json(['La existencia de ' . $inventory->name . ' es menor a la cantidad solicitada.'], 422);
            }
        }

        //Changing date format
        $bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date);

        //Saving bill
        $bill = New Bill();
        $bill->fill($request->all());
        $bill->bill_date = $bill_date->toDateString();
        $bill->state = 1;

        $bill->save();

        //Saving each bill item
        foreach($request['bill_item'] as $bi) {
            $billItem = new BillItem();
            $billItem->bill_id = $bill->id;
            $billItem->inventory_id = $bi['inventory_id'];
            $billItem->price = $bi['price'];
            $billItem->quantity = $bi['quantity'];

            $billItem->save();

            //Updating inventory's stock
            InventoryController::removeStocks($bi['inventory_id'],$bi['quantity']);
        }

        //Rollbacking date formats to d/m/Y
        $bill->bill_date = $request->bill_date;

        return response()->json($bill);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = Bill::with('client', 'billItem', 'billItem.inventory')->find($id);

        if(!$bill) {
            return response()->json(['No se encontró la factura.'], 404);
        }

        //Calculating extra fields
        $subtotal = 0;
        $iva = 0;
        $perception_value = 0;
        $total = 0;

        foreach($bill->billItem as $billItem){
            $subtotal = $subtotal + ($billItem->price * $billItem->quantity);
        }
        if($bill->bill_type == 'Credito fiscal') {
            $iva = $subtotal * 0.13;
        }
        if($bill->perception == 1) {
            $perception_value = $subtotal * 0.1;
        }

        $total = $subtotal + $iva + $perception_value;

        $bill->subtotal = number_format($subtotal,2);
        $bill->iva = number_format($iva,2);
        $bill->perception_value = number_format($perception_value,2);
        $bill->total = number_format($total,2);

        $json = json_decode($bill, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function edit(Bill $bill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Fetching the bill
        $bill = Bill::find($request['id']);

        if(!$bill) {
            return response()->json(['No se encontró la factura.'], 404);
        }
        if($bill->state == 0) {
            return response()->json(['La factura ya había sido eliminada.'], 422);
        }

        //Invalidating the bill
        $bill->state = 0;
        $bill->save();

        foreach($bill->billItem as $bi) {
            //Updating inventory's stock
            InventoryController::addStocks($bi->inventory_id,$bi->quantity);
        }

        return response()->json($bill);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bill $bill)
    {
        //
    }
}
