<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Seller;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\BillItem;
use App\Models\Partner;
use App\Models\Municipality;

use PDF;
use Carbon\Carbon;

class PdfController extends Controller
{
    public function salesByClient($id)
    {
        $data = Bill::where('client_id', $id)->get();

        if($data->isEmpty()){
            $client = Client::find($id);
            $pdf = \PDF::loadView('salesByClient', ['client'=>$client]);
            
        }else{
            $pdf = \PDF::loadView('salesByClient', ['data'=>$data]);
            
        }
        
        $current_date_time = Carbon::now()->toDateTimeString();
        $pdf->save(storage_path().'_SalesByClient.pdf');
        return $pdf->download($current_date_time.'salesByClient.pdf');
    }

    public function salesBySeller($id)
    {
        $data = Bill::whereHas('client.seller', function($offerQuery) use(&$id){
            $offerQuery->where('id', '=', $id);})->get();

        if($data->isEmpty()){
            $seller = Seller::find($id);
            $pdf = \PDF::loadView('salesBySeller', ['seller'=>$seller]);

        }else{
            $pdf = \PDF::loadView('salesBySeller', ['data'=>$data]);

        }

        $current_date_time = Carbon::now()->toDateTimeString();
        $pdf->save(storage_path().'_salesBySeller.pdf');
        return $pdf->download($current_date_time.'salesBySeller.pdf');
    }


    public function salesByProduct($id)
    {
        $data = BillItem::where("inventory_id",$id)->get();

        if($data->isEmpty()){

            $inventory = Inventory::find($id);
            $pdf = \PDF::loadView('salesByProduct', ['inventory'=>$inventory]);
            
        }else{

            $pdf = \PDF::loadView('salesByProduct', ['data'=>$data]);
            
        }
        
        $current_date_time = Carbon::now()->toDateTimeString();
        $pdf->save(storage_path().'_salesByProduct.pdf');
        return $pdf->download($current_date_time.'salesByProduct.pdf');
    }

    public function salesByLocation($id)
    {
        $data = Partner::where("municipality_id",$id)->get();

        if($data->isEmpty()){
            $municipality = Municipality::find($id);
            $pdf = \PDF::loadView('salesByLocation', ['municipality'=>$municipality]);
            
        }else{

            $pdf = \PDF::loadView('salesByLocation', ['data'=>$data]);
            
        }
        
        $current_date_time = Carbon::now()->toDateTimeString();
        $pdf->save(storage_path().'_salesByLocation.pdf');
        return $pdf->download($current_date_time.'salesByLocation.pdf');
    }


    public function salesDeleted()
    {
        $data = Bill::where('state', 0)->get();

        if($data->isEmpty()){
            $state = "No se encontraron facturas eliminadas a esta fecha";
            $pdf = \PDF::loadView('salesDeleted', ['state'=>$state]);
            
        }else{
            $pdf = \PDF::loadView('salesDeleted', ['data'=>$data]);
            
        }
        
        $current_date_time = Carbon::now()->toDateTimeString();
        $pdf->save(storage_path().'_salesDeleted.pdf');
        return $pdf->download($current_date_time.'salesDeleted.pdf');
    }
}