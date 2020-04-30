<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Seller;
use App\Models\Client;
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
            $offerQuery->where('id', '=', $id);
        })->get();

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
}