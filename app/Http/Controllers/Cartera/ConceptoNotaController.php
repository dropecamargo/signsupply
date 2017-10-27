<?php

namespace App\Http\Controllers\Cartera;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Contabilidad\PlanCuenta;
use App\Models\Cartera\ConceptoNota;
use DB, Log, Datatables, Cache;

class ConceptoNotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ConceptoNota::query();
            $query->select('conceptonota.*', 'plancuentas_nombre');
            $query->join('plancuentas', 'conceptonota_plancuentas', '=', 'plancuentas.id');
            return Datatables::of($query)->make(true);
        }
        return view('cartera.conceptonotas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cartera.conceptonotas.create');
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
            $conceptonota = new ConceptoNota;
            if ($conceptonota->isValid($data)) {
                DB::beginTransaction();
                try {
                    $plancuentas = PlanCuenta::where('plancuentas_cuenta', $request->conceptonota_plancuentas)->first();
                    if(!$plancuentas instanceof PlanCuenta){
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar la cuenta, por favor verifique la informacion ó consulte al administrador.']);
                    }

                    // Valid correctly use the cuenta
                    $result = $plancuentas->validarSubnivelesCuenta();
                    if ($result != 'OK') {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => $result ]);
                    } 

                    $conceptonota->fill($data);
                    $conceptonota->fillBoolean($data);
                    $conceptonota->conceptonota_plancuentas = $plancuentas->id;
                    $conceptonota->save();

                    //Forget cache
                    Cache::forget( ConceptoNota::$key_cache );

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'id' => $conceptonota->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $conceptonota->errors]);
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
        $conceptonota = ConceptoNota::getConceptoNota($id);
        if ($request->ajax()) {
            return response()->json($conceptonota);
        }
        return view('cartera.conceptonotas.show', ['conceptonota' => $conceptonota]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $conceptonota = ConceptoNota::getConceptoNota($id);
        return view('cartera.conceptonotas.edit', ['conceptonota' => $conceptonota]);
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
            $conceptonota = ConceptoNota::findOrFail($id);
            if ($conceptonota->isValid($data)) {
                DB::beginTransaction();
                try {

                    // Recuperar cuenta
                    $plancuentas = PlanCuenta::where('plancuentas_cuenta', $request->conceptonota_plancuentas)->first();
                    if(!$plancuentas instanceof PlanCuenta){
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar la cuenta, por favor verifique la informacion ó consulte al administrador.']);
                    }

                    // Valid correctly use the cuenta
                    $result = $plancuentas->validarSubnivelesCuenta();
                    if ($result != 'OK') {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => $result ]);
                    }

                    // Conceptonota
                    $conceptonota->fill($data);
                    $conceptonota->fillBoolean($data);
                    $conceptonota->conceptonota_plancuentas = $plancuentas->id;
                    $conceptonota->save();

                    //Forget cache
                    Cache::forget( ConceptoNota::$key_cache );

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'id' => $conceptonota->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $conceptonota->errors]);
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
}
