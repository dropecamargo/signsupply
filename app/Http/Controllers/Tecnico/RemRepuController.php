<?php

namespace App\Http\Controllers\Tecnico;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tecnico\RemRepu, App\Models\Tecnico\RemRepu2, App\Models\Tecnico\Orden, App\Models\Base\Tercero, App\Models\Inventario\Producto, App\Models\Base\Sucursal, App\Models\Base\Documentos, App\Models\Inventario\Prodbode, App\Models\Tecnico\TipoOrden, App\Models\Inventario\TipoAjuste;
use Log, DB, Auth;

class RemRepuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RemRepu::query();
            $query->select('remrepu1.*', 'sucursal_nombre',DB::raw("CONCAT((CASE WHEN tercero_persona = 'N' THEN CONCAT(tercero_nombre1,' ',tercero_nombre2,' ',tercero_apellido1,' ',tercero_apellido2,(CASE WHEN (tercero_razonsocial IS NOT NULL AND tercero_razonsocial != '') THEN CONCAT(' - ', tercero_razonsocial) ELSE '' END)) ELSE tercero_razonsocial END)) AS tecnico_nombre"));
            $query->join('tercero', 'remrepu1_tecnico', '=', 'tercero.id');
            $query->join('sucursal', 'remrepu1_sucursal', '=', 'sucursal.id');
            $query->where('remrepu1_orden',$request->orden_id);
            $query->orderBy('sucursal_nombre', 'desc');
            return response()->json($query->get());
        }
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
        if ($request->ajax()) {
            $data = $request->all();
            $remrepu = new RemRepu;
            if ($remrepu->isValid($data)) {
                DB::beginTransaction();
                try {
                    // Recupero instancia de orden
                    $orden = Orden::find($request->remrepu_orden);
                    if (!$orden instanceof Orden) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar orden, por favor verifique la información o consulte al administrador.']);
                    }

                    // Recuperar TipoOrden
                    $tipoorden = TipoOrden::find($orden->orden_tipoorden);
                    if (!$tipoorden instanceof TipoOrden) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar tipo de orden, por favor verifique la información o consulte al administrador.']);
                    }

                    // Recuperar TipoAjuste
                    $tipoajuste = TipoAjuste::find($tipoorden->tipoorden_tipoajuste);
                    if (!$tipoajuste instanceof TipoAjuste) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar tipo de ajuste, por favor verifique la información o consulte al administrador.']);
                    }

                    // Recupero instancia de documentos
                    $documentos = Documentos::where('documentos_codigo', RemRepu::$default_document)->first();
                    if (!$documentos instanceof Documentos) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar documentos, por favor verifique la información o consulte al administrador.']);
                    }

                    // Recupero instancia de sucursal
                    $sucursal  = Sucursal::find($request->sucursal);
                    if (!$sucursal instanceof Sucursal) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar sucursal, por favor verifique la información o consulte al administrador.']);
                    }

                    // Recupero tecnico
                    $tecnico  = Tercero::find($request->tecnico);
                    if (!$tecnico instanceof Tercero) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar tecnico, por favor verifique la información o consulte al administrador.']);
                    }

                    // Consecutive sucursal_remr
                    $consecutive = $sucursal->sucursal_remr + 1;

                    // RemRepu
                    $remrepu->remrepu1_orden = $orden->id;
                    $remrepu->remrepu1_sucursal = $sucursal->id;
                    $remrepu->remrepu1_tecnico = $tecnico->id;
                    $remrepu->remrepu1_numero = $consecutive;
                    $remrepu->remrepu1_documentos = $documentos->id;
                    $remrepu->remrepu1_tipo = 'R';
                    $remrepu->remrepu1_usuario_elaboro = Auth::user()->id;
                    $remrepu->remrepu1_fh_elaboro = date('Y-m-d H:m:s');
                    $remrepu->save();

                    foreach ($data['detalle'] as $value) {
                        // Recuperar Sucursal
                        $sucursal = Sucursal::find($data['sucursal']);
                        if(!$sucursal instanceof Sucursal){
                            DB::rollback();
                            return response()->json(['success' => false, 'errors' => 'No es posible recuperar la sucursal, por favor verifique la información o consulte al administrador.']);
                        }

                        // Recupero instancia de producto
                        $producto = Producto::where('producto_serie', $value['remrepu2_serie'])->first();
                        if(!$producto instanceof Producto) {
                            DB::rollback();
                            return response()->json(['success' => false, 'errors' => 'No es posible recuperar producto, por favor verifique la información o consulte al administrador.']);
                        }

                        if( !in_array($producto->tipoproducto->tipoproducto_codigo, explode(',', $tipoajuste->getTypesProducto()->tipoajuste_tipoproducto)) ){
                            DB::rollback();
                            return response()->json(['success' => false, 'errors' => 'El tipo de ajuste no es valido para generar una remision, por favor verifique la información o consulte al administrador.']);
                        }

                        // Validar sucursal prodbode
                        $validSucu = Prodbode::where('prodbode_sucursal', $sucursal->id)->where('prodbode_serie', $producto->id)->first();
                        if ($validSucu == null) {
                            DB::rollback();
                            return response()->json(['success'=> false, 'errors' => "El producto {$producto->producto_nombre} - {$producto->producto_serie} no corresponde a esa sucursal, por favor verificar información o consulte al administrador."]);
                        }

                        // Valido producto unico en la remision
                        $existente = DB::table('remrepu2')->where('remrepu2_producto', $producto->id)->where('remrepu2_remrepu1', $remrepu->id)->first();
                        if ($existente != null) {
                            DB::rollback();
                            return response()->json(['success'=> false, 'errors' => "Producto {$producto->producto_nombre} - {$producto->producto_serie} se encuentra repetido, por favor verificar información o consulte al administrador."]);
                        }

                        // Remrepu2
                        $remrepu2 = new RemRepu2;
                        $remrepu2->fill($value);
                        $remrepu2->remrepu2_remrepu1 = $remrepu->id;
                        $remrepu2->remrepu2_saldo = $value['remrepu2_cantidad'];
                        $remrepu2->remrepu2_producto = $producto->id;
                        $remrepu2->save();

                        // Traslado sucursalOriginal a provisinal
                        $result = RemRepu2::trasladoRemRepu($remrepu, $remrepu2, $value['remrepu2_cantidad']);
                        if($result != 'OK'){
                            DB::rollback();
                            return response()->json(['success' => false, 'errors' => $result]);
                        }
                    }

                    // Update sucursal_remr
                    $sucursal->sucursal_remr = $consecutive;
                    $sucursal->save();

                    DB::commit();
                    return response()->json(['success' => true, 'id' => $remrepu->id, 'remrepu1_numero' => $remrepu->remrepu1_numero ,'tecnico_nombre' => $tecnico->getName(), 'sucursal_nombre' => $sucursal->sucursal_nombre, 'remrepu1_tipo' => $remrepu->remrepu1_tipo]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $remrepu->errors]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }
}
