<?php

namespace App\Http\Controllers\Contabilidad;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Contabilidad\TipoActivo, App\Models\Contabilidad\PlanCuenta;
use DB, Log, Datatables, Cache;

class TipoActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = TipoActivo::query();
            return Datatables::of($query)->make(true);
        }
        return view('contabilidad.tipoactivo.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contabilidad.tipoactivo.create');
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
            $tipoactivo = new TipoActivo;
            if ($tipoactivo->isValid($data)) {
                DB::beginTransaction();
                try {
                    // TipoActivo
                    $tipoactivo->fill($data);
                    $tipoactivo->fillBoolean($data);
                    $tipoactivo->save();

                    // Commit Transaction
                    DB::commit();

                    // Forget cache
                    Cache::forget( TipoActivo::$key_cache );
                    return response()->json(['success' => true, 'id' =>$tipoactivo->id]);
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $tipoactivo->errors]);
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
        $tipoactivo = TipoActivo::find($id);
        if ($request->ajax()) {
            return response()->json($tipoactivo);
        }
        return view('contabilidad.tipoactivo.show', ['tipoactivo' => $tipoactivo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipoactivo = TipoActivo::findOrFail($id);
        return view('contabilidad.tipoactivo.edit', ['tipoactivo' => $tipoactivo]);
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
            $tipoactivo = TipoActivo::findOrFail($id);
            if ($tipoactivo->isValid($data)) {
                DB::beginTransaction();
                try {
                    // TipoActivo
                    $tipoactivo->fill($data);
                    $tipoactivo->fillBoolean($data);
                    $tipoactivo->save();

                    // Commit Transaction
                    DB::commit();

                    // Forget cache
                    Cache::forget( TipoActivo::$key_cache );
                    return response()->json(['success' => true, 'id' =>$tipoactivo->id]);
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $tipoactivo->errors]);
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
