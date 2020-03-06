<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\PartnerController;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $providers = Provider::with('partner')->get();
        $json = json_decode($providers, true);

        return $json;
    }

    public function getActiveProviders() {
        $providers = Provider::with('partner')->whereHas('partner',function($q) {
            $q->where('state', 1);
        })->get();
        $json = json_decode($providers, true);

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
                'partner.name'            => array('required','unique:partners,name','min:4','max:100'),
                'partner.address'         => array('required','min:4','max:1000'),
                'partner.municipality_id' => array('required','exists:municipalities,id','numeric'),
                'partner.nit'             => array('required','unique:partners,nit', 'regex:"^\d{4}[-]\d{6}[-]\d{3}[-]\d$"', 'not_in:0000-000000-000-0'),
                'partner.phone'           => array('required','regex:"^[267]{1}[0-9]{7}( {1}[0-9]{4})*$"'),
                'partner.state'           => array('required', 'boolean'),

                'seller_phone'            => array('required','regex:"^[267]{1}[0-9]{7}( {1}[0-9]{4})*$"')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $partner = PartnerController::store($request['partner']);

        $provider = New Provider();
        $provider->fill($request->all());
        $provider->partner_id = $partner->id;

        $provider->save();

        return response()->json($provider);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $provider = Provider::with('partner')->find($id);

        if(!$provider) {
            return response()->json(['No se encontró el proveedor.'], 404);
        }

        $json = json_decode($provider, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
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
        //Fetching the provider
        $provider = Provider::find($request['id']);

        if(!$provider) {
            return response()->json(['No se encontró el proveedor.'], 404);
        }

        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'partner.name'            => array('required','unique:partners,name,'.$provider->partner_id,'min:4','max:100'),
                'partner.address'         => array('required','min:4','max:1000'),
                'partner.municipality_id' => array('required','exists:municipalities,id','numeric'),
                'partner.nit'             => array('required','unique:partners,nit,'.$provider->partner_id, 'regex:"^\d{4}[-]\d{6}[-]\d{3}[-]\d$"', 'not_in:0000-000000-000-0'),
                'partner.phone'           => array('required','regex:"^[267]{1}[0-9]{7}( {1}[0-9]{4})*$"'),
                'partner.state'           => array('required', 'boolean'),

                'id'            => array('required','numeric'),
                'seller_phone'  => array('required','regex:"^[267]{1}[0-9]{7}( {1}[0-9]{4})*$"')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $partner = PartnerController::update($request['partner'], $provider->partner_id);

        $provider->fill($request->all());

        $provider->save();

        $provider->partner = $partner;

        return response()->json($provider);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Provider $provider)
    {
        //
    }
}
