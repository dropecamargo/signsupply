<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log, Datatables, Cache;

use App\Models\Inventario\Grupo;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Grupo::query();
            $query->select('grupo.id as id', 'grupo_codigo', 'grupo_nombre');
            return Datatables::of($query)->make(true);
        }
        return view('inventario.grupos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventario.grupos.create');
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

            $grupo = new Grupo;
            if ($grupo->isValid($data)) {
                DB::beginTransaction();
                try {
                    // grupo
                    $grupo->fill($data);
                    $grupo->save();

                    // Commit Transaction
                    DB::commit();
                    // Forget cache
                    Cache::forget( Grupo::$key_cache );

                    return response()->json(['success' => true, 'id' => $grupo->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $grupo->errors]);
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
        $grupo = Grupo::findOrFail($id);
        if ($request->ajax()) {
            return response()->json($grupo);
        }
        return view('inventario.grupos.show', ['grupo' => $grupo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $grupo = Grupo::findOrFail($id);
        return view('inventario.grupos.edit', ['grupo' => $grupo]);
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

            $grupo = Grupo::findOrFail($id);
            if ($grupo->isValid($data)) {
                DB::beginTransaction();
                try {
                    // grupo
                    $grupo->fill($data);
                    $grupo->save();

                    // Commit Transaction
                    DB::commit();
                    // Forget cache
                    Cache::forget( Grupo::$key_cache );

                    return response()->json(['success' => true, 'id' => $grupo->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $grupo->errors]);
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