<?php

namespace App\Http\Controllers\Cartera;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Cartera\Factura1, App\Models\Cartera\Factura2, App\Models\Cartera\Factura3;
use App\Models\Comercial\Pedidoc1,App\Models\Comercial\Pedidoc2;
use App\Models\Inventario\Producto,App\Models\Inventario\SubCategoria,App\Models\Inventario\Lote,App\Models\Inventario\Prodbode,App\Models\Inventario\Inventario,App\Models\Inventario\Rollo;
use App\Models\Base\Tercero,App\Models\Base\PuntoVenta,App\Models\Base\Documentos,App\Models\Base\Sucursal, App\Models\Base\Contacto; 
use DB, Log, Datatables,Auth;

class Factura1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Factura1::query();
            $query->select('factura1.*', 'tercero_nit', 'tercero_razonsocial', 'tercero_nombre1', 'tercero_nombre2', 'tercero_apellido1', 'tercero_apellido2', 'puntoventa.puntoventa_prefijo','sucursal.sucursal_nombre',DB::raw("(CASE WHEN tercero_persona = 'N'
                    THEN CONCAT(tercero_nombre1,' ',tercero_nombre2,' ',tercero_apellido1,' ',tercero_apellido2,
                            (CASE WHEN (tercero_razonsocial IS NOT NULL AND tercero_razonsocial != '') THEN CONCAT(' - ', tercero_razonsocial) ELSE '' END)
                        )
                    ELSE tercero_razonsocial END)
                AS tercero_nombre")
            );
            $query->join('tercero','factura1_tercero', '=', 'tercero.id');
            $query->join('puntoventa','factura1_puntoventa', '=', 'puntoventa.id');
            $query->join('sucursal','factura1_sucursal', '=', 'sucursal.id');
            return Datatables::of($query)->make(true);
        }
        return view('cartera.facturas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cartera.facturas.create');
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
            $factura1 = new Factura1;
            if ($factura1->isValid($data)) {
                DB::beginTransaction();
                try {
                    //Validar documentos
                    $documento = Documentos::where('documentos_codigo', Factura1::$default_document)->first();
                    if (!$documento instanceof Documentos) {
                        DB::rollback();
                        return response()->json(['success'=> false, 'errors' => 'No es posible recuperar documentos,por favor verifique la información ó por favor consulte al administrador.']);
                    }
                    // Validar cliente
                    $cliente = Tercero::where('tercero_nit',$request->factura1_tercero)->first();
                    if (!$cliente instanceof Tercero) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar cliente, por favor verifique la información o consulte al administrador']);
                    }
                    // Validar contacto
                    $contacto = Contacto::find($request->factura1_tercerocontacto);
                    if(!$contacto instanceof Contacto) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar contacto, por favor verifique la información o consulte al administrador.']);
                    }

                    // Validar tercero contacto
                    if($contacto->tcontacto_tercero != $cliente->id) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'El contacto seleccionado no corresponde al cliente, por favor seleccione de nuevo el contacto o consulte al administrador.']);
                    }
                    // VAlidar vendedor
                    $vendedor = Tercero::find($request->factura1_vendedor);
                    if (!$vendedor instanceof Tercero) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar vendedor, por favor verifique la información o consulte al administrador']);
                    }
                    // Validar sucursal
                    $sucursal = Sucursal::find($request->factura1_sucursal);
                    if(!$sucursal instanceof Sucursal) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar sucursal,por favor verifique la información ó por favor consulte al administrador.']);
                    }
                    // Validar punto venta
                    $puntoventa = PuntoVenta::find($request->factura1_puntoventa);
                    if(!$puntoventa instanceof PuntoVenta) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar punto venta,por favor verifique la información ó por favor consulte al administrador.']);
                    }
                    $pedidoc1 = Pedidoc1::where('pedidoc1_sucursal', $sucursal->id)->where('pedidoc1_numero', $request->factura1_pedido)->first();
                    if (!$pedidoc1 instanceof Pedidoc1) {
                        DB:rollback();
                        return response()->json(['success'=> false, 'errors'=>'No es posible recuperar punto venta,por favor verifique la información ó por favor consulte al administrador.']);
                    }
                    // Consecutive punto venta 
                    $consecutive = $puntoventa->puntoventa_numero + 1;

                    // Factura1
                    $factura1->fill($data);
                    $factura1->factura1_sucursal = $sucursal->id;
                    $factura1->factura1_pedidoc1 = $pedidoc1->id;
                    $factura1->factura1_numero = $consecutive;
                    $factura1->factura1_puntoventa = $puntoventa->id;
                    $factura1->factura1_prefijo = $puntoventa->puntoventa_prefijo;
                    $factura1->factura1_documentos = $documento->id;
                    $factura1->factura1_tercero = $cliente->id;
                    $factura1->factura1_tercerocontacto = $contacto->id;
                    $factura1->factura1_vendedor = $vendedor->id;
                    $factura1->factura1_usuario_elaboro = Auth::user()->id;
                    $factura1->factura1_fh_elaboro = date('Y-m-d H:m:s');
                    $factura1->save();
                    foreach ($data['factura2'] as $item) {
                        $producto = Producto::where('producto_serie', $item['producto_serie'])->first();
                        if (!$producto instanceof Producto) {
                            DB::rollback();
                            return response()->json(['success' => false , 'errors' => 'No es posible recuperar producto, por favor verifique la información ó por favor consulte al administrador.']);
                        }
                        //SubCategoria validate
                        $subcategoria = SubCategoria::find($producto->producto_subcategoria);
                        if (!$subcategoria instanceof SubCategoria) {
                            DB::rollback();
                            return response()->json(['success' => false, 'errors' => 'No es posible recuperar subcategoria, por favor verifique información o consulte al administrador']);
                        }
                        //prepare detalle2
                        $pedidoc2 = Pedidoc2::where('pedidoc2_pedidoc1', $pedidoc1->id)->where('pedidoc2_producto',$producto->id)->first();
                        if (!$pedidoc2 instanceof Pedidoc2) {
                            DB::rollback();
                            return response()->json(['success'=>false , 'errors'=> 'No es posible recuperar subcategoria, por favor verifique información o consulte al administrador']);
                        }
                        if ($producto->producto_maneja_serie != true) {

                            //Detalle factura
                            $factura2 = new Factura2;
                            $factura2->fill($item);
                            $factura2->factura2_factura1 = $factura1->id;
                            $factura2->factura2_producto = $producto->id;
                            $factura2->factura2_subcategoria = $subcategoria->id;
                            $factura2->factura2_margen = $subcategoria->subcategoria_margen_nivel1;
                            $factura2->save();
                            // Prodbode
                            $result = Prodbode::actualizar($producto, $sucursal->id, 'S', $factura2->factura2_cantidad);
                            if($result != 'OK') {
                                DB::rollback();
                                return response()->json(['success' => false, 'errors'=> $result]);
                            }
                        }
                        if ($producto->producto_maneja_serie == true) {

                            // Prodbode
                            $result = Prodbode::actualizar($producto, $sucursal->id, 'S', 1);
                            if($result != 'OK') {
                                DB::rollback();
                                return response()->json(['success' => false, 'errors'=> $result]);
                            }
                            // Detalle factura
                            $factura2 = new Factura2;
                            $factura2->fill($item);
                            $factura2->factura2_factura1 = $factura1->id;
                            $factura2->factura2_producto = $producto->id;
                            $factura2->factura2_subcategoria = $subcategoria->id;
                            $factura2->factura2_margen = $subcategoria->subcategoria_margen_nivel1;
                            $factura2->save();
                            $lote = Lote::actualizar($producto, $sucursal->id, "", 'S', 1, $factura1->factura1_fecha, null);
                            if (!$lote instanceof Lote) {
                                DB::rollback();
                                return response()->json(['success' => false, 'errors' => 'No es posible recuperar lote, por favor verifique la información ó por favor consulte al administrador']);
                            }
                            // Inventario
                            $inventario = Inventario::movimiento($producto, $sucursal->id, 'FACT', $factura1->id, 0, 1, 0, 0,$factura2->factura2_costo, $factura2->factura2_costo,$lote->id);
                            if (!$inventario instanceof Inventario) {
                                DB::rollback();
                                return response()->json(['success' => false,'errors '=> $inventario]);
                            }
                        }else if($producto->producto_metrado == true){

                            $items = isset($item['items']) ? $item['items'] : null;
                            foreach ($items as $key => $valueItem) {
                                if ($valueItem > 0) {
                                    
                                     list($text, $rollo) = explode("_", $key);
                                    // Individualiza en rollo --- $rollo hace las veces de lote 
                                    $rollo = Rollo::actualizar($producto, $sucursal->id, 'S', $rollo, $factura1->factura1_fecha, $valueItem);
                                    if (!$rollo instanceof Rollo) {
                                        DB::rollback();
                                        return response()->json(['success' => false, 'errors' => $rollo]);
                                    }
                                    // Inventario
                                    $inventario = Inventario::movimiento($producto, $sucursal->id, 'FACT', $factura1->id, 0, 0, 0, $valueItem, $factura2->factura2_costo, $factura2->factura2_costo,0,$rollo->id);
                                    if (!$inventario instanceof Inventario) {
                                        DB::rollback();
                                        return response()->json(['success' => false,'errors '=> $inventario]);
                                    }
                                }
                            }
                        }else{
                            $items = isset($item['items']) ? $item['items'] : null;
                            foreach ($items as $key => $value) {
                                list($text, $lote) = explode("_", $key);

                                if ($value > 0) {
                                    // Individualiza en lote
                                    $lote = Lote::actualizar($producto, $sucursal->id, $lote, 'S', $value);
                                    if (!$lote instanceof Lote) {
                                        DB::rollback();
                                        return response()->json(['success' => false, 'errors' => $lote]);
                                    }
                                    // Inventario
                                    $inventario = Inventario::movimiento($producto, $sucursal->id, 'FACT', $factura1->id, 0, $value, 0, 0, $factura2->factura2_costo, $factura2->factura2_costo,$lote->id);
                                    if (!$inventario instanceof Inventario) {
                                        DB::rollback();
                                        return response()->json(['success' => false,'errors '=> $inventario]);
                                    }
                                }
                            }
                        }
                    }
                    $factura3 = Factura3::storeFactura3($factura1);
                    if (!$factura3) {
                        DB::rollback();
                        return response()->json(['success'=> false, 'errors'=>'No es posible realizar factura3,por favor verifique la información ó por favor consulte al administrador']);
                    }
                    // Update consecutive puntoventa_numero in PuntoVenta
                    $puntoventa->puntoventa_numero = $consecutive;
                    $puntoventa->save();

                    // Update pedidoc1_factura1 in pedidoc1
                    $pedidoc1->pedidoc1_factura1 = $factura1->id;
                    $pedidoc1->save();

                    DB::commit();
                    return response()->json(['success'=>true , 'id' => $factura1->id]);
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]); 
                }
            }   
            return response()->json(['success' => false, 'errors' => $factura1->errors]);
        }
        abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $factura = Factura1::getFactura($id);
        if(!$factura instanceof Factura1) {
            abort(404);
        }
         if($request->ajax()) {
            return response()->json($factura);
        }
        return view('cartera.facturas.show', ['factura' => $factura]);
    }

    /**
     * Show the form for search the specified resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->ajax()) {
            $query = Factura1::query();
            $query->select('factura1.id as id','factura1_numero', 'factura1_sucursal', 'factura1_tercero',DB::raw("(CASE WHEN tercero_persona = 'N'
                    THEN CONCAT(tercero_nombre1,' ',tercero_nombre2,' ',tercero_apellido1,' ',tercero_apellido2,
                            (CASE WHEN (tercero_razonsocial IS NOT NULL AND tercero_razonsocial != '') THEN CONCAT(' - ', tercero_razonsocial) ELSE '' END)
                        )
                    ELSE tercero_razonsocial END)
                AS tercero_nombre"), 'tercero.tercero_nit');
            $query->where('factura1_sucursal', $request->factura_sucursal)->where('factura1_numero', $request->factura_numero);
            $query->join('tercero' , 'factura1.factura1_tercero', '=', 'tercero.id');
            $factura1 = $query->first();
            if (!$factura1 instanceof Factura1) {
                return response()->json([ 'success' => false ]);
            }            
            return response()->json(['success' => true , 'id' => $factura1->id , 'cliente' => $factura1->tercero_nombre, 'nit' => $factura1->tercero_nit  ]);
        }
        abort(403);
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
    /**
     * Anular the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function anular(Request $request, $id)
    {
        if ($request->ajax()) {
            $factura1 = Factura1::findOrFail($id);
            DB::beginTransaction();
            try {
                if(!$factura1->validar()){
                    DB::rollback();
                    return response()->json(['success' => false , 'errors' => 'Factura NO puede ser anulada']);
                }
                // Factura
                $factura1->factura1_anulada = true;
                $factura1->factura1_usuario_anulo = Auth::user()->id;
                $factura1->factura1_fh_anulo = date('Y-m-d H:m:s');
                $factura1->save();

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'msg' => 'Factura anulada con exito.']);
            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
        abort(403);
    }
}
