<?php

namespace App\Http\Controllers\Contabilidad;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Contabilidad\Documento, App\Models\Contabilidad\Folder;
use DB, Log, Datatables, Cache;

class DocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Documento::query();
            $query->select('documento.id as id', 'documento_codigo', 'documento_nombre', 'folder_codigo', 'folder.id as folder_id');
            $query->leftJoin('folder', 'documento_folder', '=', 'folder.id');
            return Datatables::of($query)->make(true);
        }
        return view('contabilidad.documentos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contabilidad.documentos.create');
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
            $documento = new Documento;

            if ($documento->isValid($data)) {
                DB::beginTransaction();
                try {
                    // Recuperar folder
                    if( !empty($request->documento_folder) ){
                        $folder = Folder::find($request->documento_folder);
                        if(!$folder instanceof Folder){
                            DB::rollback();
                            return response(['success' => false, 'errors' => 'No es posible recuperar el folder, por favor verifique la información o consulte al administrador.']);
                        }

                        $documento->documento_folder = $folder->id;
                    }

                    // Documento
                    $documento->fill($data);
                    $documento->fillBoolean($data);
                    $documento->save();

                    // Commit Transaction
                    DB::commit();

                    //Forget cache
                    Cache::forget( Documento::$key_cache );
                    return response()->json(['success' => true, 'id' => $documento->id ]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $documento->errors]);
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
        $documento = Documento::getDocument($id);
        if($documento instanceof Documento){
            if ($request->ajax()) {
                return response()->json($documento);
            }
            return view('contabilidad.documentos.show', ['documento' => $documento]);
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
        $documento = Documento::findOrFail($id);
        return view('contabilidad.documentos.edit', ['documento' => $documento]);
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

            $documento = Documento::findOrFail($id);
            if ($documento->isValid($data)) {
                DB::beginTransaction();
                try {
                    // Recuperar folder
                    if( !empty($request->documento_folder) ){
                        $folder = Folder::find($request->documento_folder);
                        if(!$folder instanceof Folder){
                            DB::rollback();
                            return response(['success' => false, 'errors' => 'No es posible recuperar el folder, por favor verifique la información o consulte al administrador.']);
                        }

                        $documento->documento_folder = $folder->id;
                    }

                    // Documento
                    $documento->fill($data);
                    $documento->fillBoolean($data);
                    $documento->save();

                    // Commit Transaction
                    DB::commit();

                    //Forget cache
                    Cache::forget( Documento::$key_cache );
                    return response()->json(['success' => true, 'id' => $documento->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $documento->errors]);
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
     * Filter documentos.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        if($request->has('folder')) {
            $data = Documento::select('id', 'documento_nombre')->where('documento_folder', $request->folder)->get();
            return response()->json(['success' => true, 'documents' => $data]);
        }
        return response()->json(['success' => false]);
    }
}
