<?php

namespace App\Http\Controllers\Contabilidad;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Contabilidad\CentroCosto;
use DB, Log, Datatables;

class CentroCostoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CentroCosto::query();
            $query->select('centrocosto.*', DB::raw("CONCAT(centrocosto_codigo,centrocosto_centro) AS centro_codigo"));
            return Datatables::of($query)->make(true);
        }
        return view('contabilidad.centroscosto.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contabilidad.centroscosto.create');
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

            $centrocosto = new CentroCosto;
            if ($centrocosto->isValid($data)) {
                DB::beginTransaction();
                try {
                    // Centro costo
                    $centrocosto->fill($data);
                    $centrocosto->fillBoolean($data);
                    $centrocosto->save();

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'id' => $centrocosto->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $centrocosto->errors]);
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
        $centrocosto = CentroCosto::findOrFail($id);
        if ($request->ajax()) {
            return response()->json($centrocosto);
        }
        return view('contabilidad.centroscosto.show', ['centrocosto' => $centrocosto]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $centrocosto = CentroCosto::findOrFail($id);
        return view('contabilidad.centroscosto.edit', ['centrocosto' => $centrocosto]);
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
            $centrocosto = CentroCosto::findOrFail($id);
            if ($centrocosto->isValid($data)) {
                DB::beginTransaction();
                try {
                    // Centro costo
                    $centrocosto->fill($data);
                    $centrocosto->fillBoolean($data);
                    $centrocosto->save();

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'id' => $centrocosto->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $centrocosto->errors]);
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
