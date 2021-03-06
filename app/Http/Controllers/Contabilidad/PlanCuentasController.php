<?php

namespace App\Http\Controllers\Contabilidad;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Contabilidad\PlanCuenta, App\Models\Contabilidad\PlanCuentaNif, App\Models\Contabilidad\CentroCosto;
use DB, Log, Cache, Datatables;

class PlanCuentasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PlanCuenta::query();
            $query->select('plancuentas.id as id', 'plancuentas_cuenta', 'plancuentas_nivel', 'plancuentas_nombre', 'plancuentas_naturaleza', 'plancuentas_tercero', 'plancuentas_tasa', 'plancuentas_centro', 'plancuentas_equivalente', 'plancuentasn_cuenta');
            $query->leftJoin('plancuentasn', 'plancuentas_equivalente', '=', 'plancuentasn.id');

            // Persistent data filter
            if($request->has('persistent') && $request->persistent) {
                session(['search_plancuentas_cuenta' => $request->has('plancuentas_cuenta') ? $request->plancuentas_cuenta : '']);
                session(['search_plancuentas_nombre' => $request->has('plancuentas_nombre') ? $request->plancuentas_nombre : '']);
            }

            return Datatables::of($query)
                ->filter(function($query) use($request) {
                    // Cuenta
                    if($request->has('plancuentas_cuenta')) {
                        $query->whereRaw("plancuentas_cuenta LIKE '%{$request->plancuentas_cuenta}%'");
                    }
                    // Nombre
                    if($request->has('plancuentas_nombre')) {
                        $query->whereRaw("plancuentas_nombre LIKE '%{$request->plancuentas_nombre}%'");
                    }
                })
                ->make(true);
        }
        return view('contabilidad.plancuentas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contabilidad.plancuentas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();

            $plancuenta = new PlanCuenta;
            if ($plancuenta->isValid($data)) {
                DB::beginTransaction();
                try {
                    // Cuenta
                    $plancuenta->fill($data);
                    $plancuenta->fillBoolean($data);
                    $plancuenta->setNivelesCuenta();

                    if ($request->has('plancuentas_equivalente')) {
                        // Nif
                        $nif = PlanCuentaNif::find($request->plancuentas_equivalente);
                        if (!$nif instanceof PlanCuentaNif) {
                            DB::rollback();
                            return response()->json(['success' => false, 'errors' => "No es posible recuperar plan de cuenta NIF, por favor verifique la información o consulte a su administrador"]);
                        }

                        // Verifico que no existan subniveles de la cuenta que estoy realizando el asiento
                        $result = $nif->validarSubnivelesCuenta();
                        if($result != 'OK') {
                            return $result;
                        }
                        $plancuenta->plancuentas_equivalente = $nif->id;
                    }

                    $plancuenta->save();

                    // Commit Transaction
                    DB::commit();

                    //Forget cache
                    Cache::forget( PlanCuenta::$key_cache );
                    return response()->json(['success' => true, 'id' => $plancuenta->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $plancuenta->errors]);
        }
        abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $plancuenta = PlanCuenta::getCuenta($id);
        if($plancuenta instanceof PlanCuenta){
            if ($request->ajax()) {
                return response()->json($plancuenta);
            }
            return view('contabilidad.plancuentas.show', ['plancuenta' => $plancuenta]);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plancuenta = PlanCuenta::findOrFail($id);
        return view('contabilidad.plancuentas.edit', ['plancuenta' => $plancuenta]);
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
        if ($request->ajax()) {
            $data = $request->all();

            $plancuenta = PlanCuenta::findOrFail($id);
            if ($plancuenta->isValid($data)) {
                DB::beginTransaction();
                try {
                    // Cuenta
                    $plancuenta->fill($data);
                    $plancuenta->fillBoolean($data);
                    $plancuenta->setNivelesCuenta();

                    if ($request->has('plancuentas_equivalente')) {
                        // Nif
                        $nif = PlanCuentaNif::find($request->plancuentas_equivalente);
                        if (!$nif instanceof PlanCuentaNif) {
                            DB::rollback();
                            return response()->json(['success' => false, 'errors' => "No es posible recuperar plan de cuenta NIF, por favor verifique la información o consulte a su administrador"]);
                        }

                        // Verifico que no existan subniveles de la cuenta que estoy realizando el asiento
                        $result = $nif->validarSubnivelesCuenta();
                        if($result != 'OK') {
                            return response()->json(['success' => false, 'errors' => "No es posible que el plan de cuenta nif $nif->plancuentasn_nombre sea un equivalente, por favor verifique la información o consulte a su administrador" ]);
                        }
                        $plancuenta->plancuentas_equivalente = $nif->id;
                    }
                    $plancuenta->save();

                    // Commit Transaction
                    DB::commit();

                    //Forget cache
                    Cache::forget( PlanCuenta::$key_cache );
                    return response()->json(['success' => true, 'id' => $plancuenta->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $plancuenta->errors]);
        }
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display a level of account.
     *
     * @return \Illuminate\Http\Response
     */
    public function nivel(Request $request)
    {
        $nivel = '';
        switch (strlen($request->plancuentas_cuenta)) {
            case '1': $nivel = 1; break;
            case '2': $nivel = 2; break;
            case '4': $nivel = 3; break;
            case '6': $nivel = 4; break;
            case '8': $nivel = 5; break;
            case '11': $nivel = 6; break;
            case '13': $nivel = 7; break;
            case '15': $nivel = 8; break;
        }
        return response()->json(['success' => true, 'nivel' => $nivel]);
    }

    /**
     * Search plan cuentas.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if($request->has('plancuentas_cuenta')) {
            $plancuenta = PlanCuenta::where('plancuentas_cuenta', $request->plancuentas_cuenta)->first();
            if($plancuenta instanceof PlanCuenta) {
                return response()->json(['success' => true, 'plancuentas_nombre' => $plancuenta->plancuentas_nombre, 'plancuentas_tasa' => $plancuenta->plancuentas_tasa, 'plancuentas_centro' => $plancuenta->plancuentas_centro, 'plancuentas_naturaleza' => $plancuenta->plancuentas_naturaleza, 'plancuentas_tipo' => $plancuenta->plancuentas_tipo]);
            }
        }
        return response()->json(['success' => false]);
    }
}
