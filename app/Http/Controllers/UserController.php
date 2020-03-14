<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $json = json_decode($users, true);

        return $json;
    }

    public function getActiveUsers() {
        $users = User::where("state",1)->get();
        $json = json_decode($users, true);

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
                'username'            => array('required','unique:users','min:4','max:100'),
                'name'         => array('required','min:4','max:100'),
                'pass'         => array('required','min:4','max:100'),
                'user_type'         => array('required','in:Administracion,Produccion'),
                'state'           => array('required', 'boolean'),
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = New User();
        $user->fill($request->all());
        $user->pass = Hash::make($request->pass);
        $user->save();

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json(['No se encontró el usuario.'], 404);
        }

        $json = json_decode($user, true);

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
        //Fetching the user
        $user = User::find($request['id']);

        if(!$user) {
            return response()->json(['No se encontró el usuario.'], 404);
        }

        //Validations
        $validator = Validator::make($request->all(),
            $rules = array(
                'username'            => array('required','min:4','max:100'),
                'name'         => array('required','min:4','max:100'),
                'pass'         => array('min:4','max:100'),
                'user_type'         => array('required','in:Administracion,Produccion','min:4','max:100'),
                'state'           => array('required', 'boolean'),
            )
        );
        
        //If a validation fails, it returns a json containing the error list with a 422 status code
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->fill($request->all());

        if($request->pass !=""){
            $user->pass = Hash::make($request->pass);    
        }

        $user->save();

        return response()->json($user);
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
