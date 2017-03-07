<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log, Datatables, Cache;
use App\Models\Inventario\Linea;

class LineaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Linea::query();
            $query->select('linea.id' , 'linea.linea_nombre' , 'linea.linea_activo');
            return Datatables::of($query)->make(true);
        }
        return view('inventario.lineas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventario.lineas.create');
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
            $linea = new Linea;
            if ($linea->isValid($data)) {
                DB::beginTransaction();
                try {
                    // linea
                    $linea->fill($data);
                    $linea->fillBoolean($data);
                    $linea->save();

                    // Commit Transaction
                    DB::commit();
                    //Forget cache
                    Cache::forget( Linea::$key_cache );

                    return response()->json(['success' => true, 'id' => $linea->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $linea->errors]);
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
        $linea = Linea::findOrFail($id);
        if ($request->ajax()) {
            return response()->json($linea);
        }
        return view('inventario.lineas.show', ['lineas' => $linea]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $linea = Linea::findOrFail($id);
        return view('inventario.lineas.edit', ['lineas' => $linea]);
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

            $linea = Linea::findOrFail($id);
            if ($linea->isValid($data)) {
                DB::beginTransaction();
                try {
                    // linea
                    $linea->fill($data);
                    $linea->fillBoolean($data);
                    $linea->save();

                    // Commit Transaction
                    DB::commit();

                    return response()->json(['success' => true, 'id' => $linea->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $linea->errors]);
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