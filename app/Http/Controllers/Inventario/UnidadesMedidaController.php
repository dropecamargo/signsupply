<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Inventario\Unidad;
use DB, Log, Datatables, Cache;

class UnidadesMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Unidad::query();
            return Datatables::of($query)->make(true);
        }
        return view('inventario.unidades.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventario.unidades.create');
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
            $unidad = new Unidad;
            if ($unidad->isValid($data)) {
                DB::beginTransaction();
                try {
                    // unidad
                    $unidad->fill($data);
                    $unidad->fillBoolean($data);
                    $unidad->save();

                    // Commit Transaction
                    DB::commit();

                    // Forget cache
                    Cache::forget( Unidad::$key_cache );
                    return response()->json(['success' => true, 'id' => $unidad->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $unidad->errors]);
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
        $unidad = Unidad::findOrFail($id);
        if ($request->ajax()) {
            return response()->json($unidad);
        }
        return view('inventario.unidades.show', ['unidad' => $unidad]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unidad = Unidad::findOrFail($id);
        return view('inventario.unidades.edit', ['unidad' => $unidad]);
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

            $unidad = Unidad::findOrFail($id);
            if ($unidad->isValid($data)) {
                DB::beginTransaction();
                try {
                    // unidad
                    $unidad->fill($data);
                    $unidad->fillBoolean($data);
                    $unidad->save();

                    // Commit Transaction
                    DB::commit();

                    // Forget cache
                    Cache::forget( Unidad::$key_cache );
                    return response()->json(['success' => true, 'id' => $unidad->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $unidad->errors]);
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
