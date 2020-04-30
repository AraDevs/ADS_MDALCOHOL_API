<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use PDF;
use Carbon\Carbon;

class PdfController extends Controller
{
    public function salesByClient($id)
    {
        $data = Bill::where('client_id', $id)->get();

        $pdf = \PDF::loadView('salesByClient', ['data'=>$data]);

        
        $current_date_time = Carbon::now()->toDateTimeString();
        $pdf->save(storage_path().'_SalesByClient.pdf');

        

        return $pdf->download('salesByClient.pdf');
    }
}