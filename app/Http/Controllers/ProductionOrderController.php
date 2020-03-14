<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProductionOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = ProductionOrder::with('inventory')->orderBy('end_date','asc')->get();
        //Changing date format
        foreach($orders as $order) {
            $order->start_date = Carbon::createFromFormat('Y-m-d', $order->start_date)->format('d/m/Y');
            $order->exp_date = Carbon::createFromFormat('Y-m-d', $order->exp_date)->format('d/m/Y');
            if($order->end_date){
                $order->end_date = Carbon::createFromFormat('Y-m-d', $order->end_date)->format('d/m/Y');  
            }
        }
        $json = json_decode($orders, true);

        return $json;
    }

    public function getActiveOrders() {
        $orders = ProductionOrder::with('inventory')->where('state', 1)->orderBy('end_date','asc')->get();
        //Changing date format
        foreach($orders as $order) {
            $order->start_date = Carbon::createFromFormat('Y-m-d', $order->start_date)->format('d/m/Y');
            $order->exp_date = Carbon::createFromFormat('Y-m-d', $order->exp_date)->format('d/m/Y');
            if($order->end_date){
                $order->end_date = Carbon::createFromFormat('Y-m-d', $order->end_date)->format('d/m/Y');  
            }
        }

        $json = json_decode($orders, true);

        return $json;
    }

    public function getFinishedOrders() {
        $orders = ProductionOrder::with('inventory')->where('end_date', '!=', null)->orderBy('end_date','desc')->get();
        //Changing date format
        foreach($orders as $order) {
            $order->start_date = Carbon::createFromFormat('Y-m-d', $order->start_date)->format('d/m/Y');
            $order->exp_date = Carbon::createFromFormat('Y-m-d', $order->exp_date)->format('d/m/Y');
            if($order->end_date){
                $order->end_date = Carbon::createFromFormat('Y-m-d', $order->end_date)->format('d/m/Y');  
            }
        }

        $json = json_decode($orders, true);

        return $json;
    }

    public function getUnfinishedOrders() {
        $orders = ProductionOrder::with('inventory')->where('end_date', null)->get();
        //Changing date format
        foreach($orders as $order) {
            $order->start_date = Carbon::createFromFormat('Y-m-d', $order->start_date)->format('d/m/Y');
            $order->exp_date = Carbon::createFromFormat('Y-m-d', $order->exp_date)->format('d/m/Y');
            if($order->end_date){
                $order->end_date = Carbon::createFromFormat('Y-m-d', $order->end_date)->format('d/m/Y');  
            }
        }

        $json = json_decode($orders, true);

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
                'inventory_id' => array('required', 'exists:inventories,id','numeric'),
                'quantity'     => array('required', 'min:1', 'numeric'),
                'start_date'   => array('required', 'date_format:d/m/Y', 'after_or_equal:' . $today),
                'exp_date'     => array('required', 'date_format:d/m/Y', 'after_or_equal:start_date'),
                'workers'      => array('required', 'min:1', 'numeric'),
                'hours'        => array('required', 'min:1', 'numeric'),
                'state'        => array('required', 'boolean')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //Changing date format
        $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date);
        $exp_date = Carbon::createFromFormat('d/m/Y', $request->exp_date);

        //Validating that the inputed hour count is lesser than the number of hours in the selected date range
        if ($request->hours > ($start_date->diffInDays($exp_date) * 24)) {
            return response()->json(['La cantidad de horas especificada es mayor a la cantidad de horas que hay en el rango de fechas seleccionado.'], 404);
        }

        $order = New ProductionOrder();
        $order->fill($request->all());
        $order->start_date = $start_date->toDateString();
        $order->exp_date = $exp_date->toDateString();

        $order->save();

        //Rollbacking date formats to d/m/Y
        $order->start_date = $request->start_date;
        $order->exp_date = $request->exp_date;

        return response()->json($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductionOrder  $productionOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = ProductionOrder::with('inventory')->find($id);

        if(!$order) {
            return response()->json(['No se encontró la orden.'], 404);
        }

        //Changing date format
        $order->start_date = Carbon::createFromFormat('Y-m-d', $order->start_date)->format('d/m/Y');
        $order->exp_date = Carbon::createFromFormat('Y-m-d', $order->exp_date)->format('d/m/Y');
        if($order->end_date){
            $order->end_date = Carbon::createFromFormat('Y-m-d', $order->end_date)->format('d/m/Y');  
        }
        
        $json = json_decode($order, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductionOrder  $productionOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductionOrder $productionOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductionOrder  $productionOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Fetching the order
        $order = ProductionOrder::find($request['id']);

        if(!$order) {
            return response()->json(['No se encontró la orden.'], 404);
        }

        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'inventory_id' => array('required', 'exists:inventories,id','numeric'),
                'quantity'     => array('required', 'min:1', 'numeric'),
                'start_date'   => array('required', 'date_format:d/m/Y'),
                'exp_date'     => array('required', 'date_format:d/m/Y', 'after_or_equal:start_date'),
                'workers'      => array('required', 'min:1', 'numeric'),
                'hours'        => array('required', 'min:1', 'numeric'),
                'state'        => array('required', 'boolean')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if($order->end_date) {
            return response()->json(['La orden solicitada ya fue finalizada, por lo que ya no puede ser modificada.'], 422);
        }

        //Changing date format
        $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date);
        $exp_date = Carbon::createFromFormat('d/m/Y', $request->exp_date);

        //Validating that the inputed hour count is lesser than the number of hours in the selected date range
        if ($request->hours > ($start_date->diffInDays($exp_date) * 24)) {
            return response()->json(['La cantidad de horas especificada es mayor a la cantidad de horas que hay en el rango de fechas seleccionado.'], 404);
        }

        $order->fill($request->all());
        $order->start_date = $start_date->toDateString();
        $order->exp_date = $exp_date->toDateString();

        $order->save();

        //Rollbacking date formats to d/m/Y
        $order->start_date = $request->start_date;
        $order->exp_date = $request->exp_date;

        return response()->json($order);
    }

    public function finishOrder(Request $request)
    {
        $order = ProductionOrder::with('inventory')->find($request['id']);

        if(!$order) {
            return response()->json(['No se encontró la orden.'], 404);
        }

        if(!($order->state)) {
            return response()->json(['La orden solicitada se encuentra inactiva.'], 422);
        }
        
        if($order->end_date) {
            return response()->json(['Esta orden ya había sido dada por finalizada.'], 422);
        }
        
        if($order->start_date > Carbon::now()) {
            return response()->json(['La fecha de inicio de la orden es mayor a la actual.'], 422);
        }

        //TODO: Update inventory's stocks adding it the quantity made in this order
        
        $order->end_date = Carbon::now();

        $order->save();
        
        //Changing date format
        $order->start_date = Carbon::createFromFormat('Y-m-d', $order->start_date)->format('d/m/Y');
        $order->exp_date = Carbon::createFromFormat('Y-m-d', $order->exp_date)->format('d/m/Y');
        $order->end_date = $order->end_date->format('d/m/Y');  

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductionOrder  $productionOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductionOrder $productionOrder)
    {
        //
    }
}
