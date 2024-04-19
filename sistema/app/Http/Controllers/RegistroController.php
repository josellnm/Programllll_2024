<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return registro::get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Registro::create($request->all());
        return response()->json(['msg'=>'ok'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Registro $registro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Registro $registro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Registro $registro)
    {
        $registro::where('idRegistro', $request['idRegistro'])->update([
            'nombre'=>$request['nombre'],
            'apellido'=>$request['apellido'],
            'edad'=>$request['edad'],
            'telefono'=>$request['telefono'],
            'correo'=>$request['correo'],
            'password'=>$request['password']
        ]);
        return response()->json(['msg'=>'ok'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,Registro $registro)
    {
        $registro::where('idRegistro', $request['idRegistro'])->delete();
        return response()->json(['msg'=>'ok'], 200);
    }
}
