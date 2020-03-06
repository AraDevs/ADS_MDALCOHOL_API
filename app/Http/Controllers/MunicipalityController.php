<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $municipalities = Municipality::with('department')->select('id', 'name', 'department_id')->where('state', 1)->get();
        $json = json_decode($municipalities, true);

        return $json;
    }

    public function getByDepartmentId($departmentId)
    {
        $municipalities = Municipality::select('id', 'name')->where('department_id', $departmentId)->where('state', 1)->get();

        if(count($municipalities) == 0) {
          return response()->json(['No se encontraron municipios para el departamento dado.'], 404);
        }

        $json = json_decode($municipalities, true);

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Municipality  $municipality
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $municipality = Municipality::with('department')->select('id', 'name', 'department_id')
        ->where('id', $id)->first();

        if(!$municipality) {
            return response()->json(['No se encontró el municipio.'], 404);
        }

        $json = json_decode($municipality, true);

        return $json;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Municipality  $municipality
     * @return \Illuminate\Http\Response
     */
    public function edit(Municipality $municipality)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Municipality  $municipality
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Municipality $municipality)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Municipality  $municipality
     * @return \Illuminate\Http\Response
     */
    public function destroy(Municipality $municipality)
    {
        //
    }
}
