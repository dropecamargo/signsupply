<?php

namespace App\Http\Controllers\Comercial;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Comercial\ConfigSabanaVenta;

use DB, Datatables;

class ConfigSabanaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // $data = [];

            $query = ConfigSabanaVenta::query();
            if( $request->has('datatables') ) {
                $query->select('configsabanaventa_orden_impresion', 'configsabanaventa_agrupacion_nombre', 'configsabanaventa_agrupacion');
                $query->groupBy('configsabanaventa_orden_impresion');
                return Datatables::of($query)->make(true);
            }else if($request->has('agrupacion')){
                $query->select('configsabanaventa.*', 'linea.id as linea_codigo', 'linea_nombre');
                $query->join('linea', 'linea.id', '=', 'configsabanaventa.configsabanaventa_linea');
                $query->orderBy('configsabanaventa_orden_impresion', 'asc');
                $query->orderBy('configsabanaventa_grupo', 'asc');
                $query->orderBy('configsabanaventa_agrupacion', 'asc');
                $query->orderBy('configsabanaventa_unificacion', 'asc');
                $query->orderBy('linea_nombre', 'asc');
                $query->where('configsabanaventa_agrupacion',$request->agrupacion);
                return response()->json(['data' => $query->get()]);
            }
            return response()->json("Consulte con su administrador a ocurrido un error en la configuración", 500);
        }
        // dd($config);
        return view('comercial.configsabana.main');
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
        dd($request->all(), 'store');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $itemConfig = ConfigSabanaVenta::find($id);
        if (!$itemConfig instanceof ConfigSabanaVenta) {
            return response()->json("Consulte con su administrador a ocurrido un error al querer eliminar este registro", 500);
        }
        $itemConfig->delete();
        return response()->json("Ítem eliminado correctamente", 200);
    }
}
