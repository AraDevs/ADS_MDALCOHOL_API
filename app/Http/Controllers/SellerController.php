<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::all();
        $json = json_decode($sellers, true);

        return $json;
    }

    public function getActiveSellers() {
        $sellers = Seller::where("state",1)->get();
        $json = json_decode($sellers, true);

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
                'name'            => array('required','unique:partners,name','min:4','max:100'),
                'seller_code'         => array('required','min:4','max:100'),
                'state'           => array('required', 'boolean'),
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seller = New Seller();
        $seller->fill($request->all());

        $seller->save();

        return response()->json($seller);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = Seller::find($id);

        if(!$seller) {
            return response()->json(['No se encontró el vendedor.'], 404);
        }

        $json = json_decode($seller, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $seller)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Fetching the seller
        $seller = Seller::find($request['id']);

        if(!$seller) {
            return response()->json(['No se encontró el vendedor.'], 404);
        }

        //Validations
        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'name'            => array('required','unique:partners,name','min:4','max:100'),
                'seller_code'         => array('required','min:4','max:100'),
                'state'           => array('required', 'boolean'),
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seller->fill($request->all());

        $seller->save();

        return response()->json($seller);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller)
    {
        //
    }
}
