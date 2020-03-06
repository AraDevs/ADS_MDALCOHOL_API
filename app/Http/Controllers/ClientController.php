<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\PartnerController;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::with('partner', 'seller')->get();
        $json = json_decode($clients, true);

        return $json;
    }

    public function getActiveClients() {
        $clients = Client::with('partner', 'seller')->whereHas('partner',function($q) {
            $q->where('state', 1);
        })->get();
        $json = json_decode($clients, true);

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

                'business_name' => array('required','unique:clients','min:4','max:100'),
                'dui'           => array('required','unique:clients,dui', 'regex:"^\d{8}[-]\d$"', 'not_in:00000000-0'),
                'registry_no'    => array('required','unique:clients,registry_no','size:8','regex:"^\d{8}$"'),
                'person_type'   => array('required','in:Natural,Jurídica'),
                'seller_id'     => array('required','exists:sellers,id','numeric')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $partner = PartnerController::store($request['partner']);

        $client = New Client();
        $client->fill($request->all());
        $client->partner_id = $partner->id;

        $client->save();

        return response()->json($client);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::with('partner')->find($id);

        if(!$client) {
            return response()->json(['No se encontró el cliente.'], 404);
        }

        $json = json_decode($client, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Fetching the client
        $client = Client::find($request['id']);

        if(!$client) {
            return response()->json(['No se encontró el cliente.'], 404);
        }

        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'partner.name'            => array('required','unique:partners,name,'.$client->partner_id,'min:4','max:100'),
                'partner.address'         => array('required','min:4','max:1000'),
                'partner.municipality_id' => array('required','exists:municipalities,id','numeric'),
                'partner.nit'             => array('required','unique:partners,nit,'.$client->partner_id, 'regex:"^\d{4}[-]\d{6}[-]\d{3}[-]\d$"', 'not_in:0000-000000-000-0'),
                'partner.phone'           => array('required','regex:"^[267]{1}[0-9]{7}( {1}[0-9]{4})*$"'),
                'partner.state'           => array('required', 'boolean'),

                'id'            => array('required','numeric'),
                'business_name' => array('required','unique:clients,business_name,'.$client->id,'min:4','max:100'),
                'dui'           => array('required','unique:clients,dui,'.$client->id, 'regex:"^\d{8}[-]\d$"', 'not_in:00000000-0'),
                'registry_no'   => array('required','unique:clients,registry_no,'.$client->id,'size:8','regex:"^\d{8}$"'),
                'person_type'   => array('required','in:Natural,Jurídica'),
                'seller_id'     => array('required','exists:sellers,id','numeric')
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $partner = PartnerController::update($request['partner'], $client->partner_id);

        $client->fill($request->all());

        $client->save();

        $client->partner = $partner;

        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
    }
}
