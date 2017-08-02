<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log, Datatables;
use App\Models\Base\Sucursal;
use App\Models\Inventario\Producto,App\Models\Inventario\Prodbode,App\Models\Inventario\Impuesto,App\Models\Inventario\TipoAjuste;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Producto::query();
            $query->select('producto.id as id', 'producto_serie', 'producto_nombre','producto_referencia','producto_costo','producto_precio1','impuesto.impuesto_porcentaje');
            $query->join('impuesto', 'producto.producto_impuesto', '=', 'impuesto.id');
            // Persistent data filter
            if($request->has('persistent') && $request->persistent) {
                session(['search_producto_referencia' => $request->has('producto_referencia') ? $request->producto_referencia : '']);
                session(['search_producto_serie' => $request->has('producto_serie') ? $request->producto_serie : '']);
                session(['search_producto_nombre' => $request->has('producto_nombre') ? $request->producto_nombre : '']);
            }

            return Datatables::of($query)
                ->filter(function($query) use($request) {
                    // Serie
                    if($request->has('producto_serie')) {
                        $query->whereRaw("producto_serie LIKE '%{$request->producto_serie}%'");
                    } 
                    //Referencia
                    if($request->has('producto_referencia')) {
                        $query->whereRaw("producto_serie LIKE '%{$request->producto_referencia}%'");
                    }

                    // Nombre
                    if($request->has('producto_nombre')) {
                        $query->whereRaw("producto_nombre LIKE '%{$request->producto_nombre}%'");
                    }
                    //Ref and Serie equals filter
                    if ($request->has('equalsRef')) {
                        if($request->equalsRef == "true"){
                            $query->whereRaw('producto_serie = producto_referencia');
                        }else{
                            $query->select('producto.id as id','impuesto.impuesto_porcentaje','producto_maneja_serie','producto_serie', 'producto_nombre','producto_referencia','producto_costo','producto_precio1','prodbode.prodbode_cantidad','prodbode_serie', 'prodbode_sucursal');
                            $query->join('prodbode', 'producto.id','=','prodbode.prodbode_serie');
                            $query->whereRaw('prodbode_cantidad > 0');
                            $sucursal = Sucursal::find($request->officeSucursal);
                            ($sucursal instanceof Sucursal) ? $query->where('prodbode_sucursal', $sucursal->id) : '' ;
                            $query->groupBy('prodbode_serie');
                        }
                    }

                    // Orden only serie filter
                    if ($request->has('orden')) {
                        if($request->orden == "true"){
                            $query->where('producto_maneja_serie', true);
                            $query->where('producto_unidad', true);
                        }
                    }

                    // remision and sucursal filter
                    if ($request->has('remision') && $request->has('sucursal')) {
                        if( $request->remision == "true" && !empty($request->sucursal)) {
                            $query->select('producto.id as id','impuesto.impuesto_porcentaje','producto_maneja_serie','producto_serie', 'producto_nombre','producto_referencia','producto_costo','producto_precio1','prodbode.prodbode_cantidad','prodbode_serie', 'prodbode_sucursal');
                            $query->join('prodbode', 'producto.id','=','prodbode.prodbode_serie');
                            $sucursal = Sucursal::find($request->sucursal);
                            ($sucursal instanceof Sucursal) ? $query->where('prodbode_sucursal', $sucursal->id) : '' ;
                            $query->where('producto_maneja_serie', false);
                            $query->where('producto_metrado', false);
                            $query->where('producto_vence', false);
                            $query->where('producto_unidad', true);
                        }
}
                })
                ->make(true);
        }
        return view('inventario.productos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventario.productos.create');
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
            $producto = new Producto;
            if ($producto->isValid($data)) {

                DB::beginTransaction();
                try {
                    $impuesto = Impuesto::where('id', $request->producto_impuesto)->first();
                    if (!$impuesto instanceof Impuesto) {
                        DB::rollback();
                        return response()->json(['success' => false, 'errors' => 'No es posible recuperar IMPUESTO,verifique información ó por favor consulte al administrador.']);
                    }
                    // Producto
                    $producto->producto_serie = $request->producto_referencia;
                    $producto->fill($data);
                    $producto->producto_impuesto = $impuesto->id;
                    $producto->fillBoolean($data);
                    $producto->save();  

                    // Commit Transaction
                    DB::commit();

                    return response()->json(['success' => true, 'id' => $producto->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $producto->errors]);
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
        $producto = Producto::getProduct($id);
        if($producto instanceof Producto){
            if ( $request->ajax() ) {
                return response()->json($producto);
            }
            $prodbode  = $producto->prodbode();
            return view('inventario.productos.show', ['producto' => $producto, 'prodbode' => $prodbode]);
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
        $producto = Producto::findOrFail($id);
        return view('inventario.productos.edit', ['producto' => $producto]);
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
            $producto = Producto::findOrFail($id);
            if ($producto->isValid($data)) {
                if($producto->producto_serie != $producto->producto_referencia ) {
                    return response()->json(['success' => false, 'errors' => 'No es posible editar una serie, por favor verifique información o consulte con el administrador.']);
                }   
                DB::beginTransaction();
                try {
                    // Producto
                    $producto->fill($data);
                    $producto->fillBoolean($data);
                    $producto->save();

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'id' => $producto->id]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $producto->errors]);
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
     * Evaluate actions detail asiento.
     *
     * @return \Illuminate\Http\Response
     */
    public function evaluate(Request $request)
    {       
        $tipoMovimiento = '';
        // Prepare response
        $response = new \stdClass();
        $response->action = "";
        $response->tipo = "";
        $response->success = false;
        if ($request->has('tipoajuste')) {
            $tipoajuste = TipoAjuste::find($request->tipoajuste);
            if (!$tipoajuste instanceof TipoAjuste) {            
                $response->errors = "No es posible recuperar TIPO AJUSTE,verifique información ó por favor consulte al administrador.";
            }  
            $tipoMovimiento = $tipoajuste->tipoajuste_tipo;
        }else{
            $tipoMovimiento = $request->tipo;
        }

        $producto = Producto::where('producto_serie', $request->producto_serie)->first();
        if (!$producto instanceof Producto) {
            $response->errors = "No es posible recuperar PRODUCTO,verifique información ó por favor consulte al administrador.";
        }

        if ($producto->producto_unidad == true) {
            if($tipoMovimiento == 'E'){
                if ($producto->producto_maneja_serie == true) {
                    $action = 'modalSerie';
                    $response->action = $action;   
                    $response->tipo = $tipoMovimiento;
                    $response->success = true;
                }elseif($producto->producto_metrado == true){
                    $action = 'ProductoMetrado';
                    $response->action = $action;   
                    $response->tipo = $tipoMovimiento;
                    $response->success = true;
                }elseif($producto->producto_vence == true){
                    $action = 'ProductoVence';
                    $response->action = $action;   
                    $response->tipo = $tipoMovimiento;
                    $response->success = true;
                }else{
                    $action = 'NoSerieNoMetros';
                    $response->action = $action;   
                    $response->tipo = $tipoMovimiento;
                    $response->success = true;
                }  
            }else{
                $productoBode = Prodbode::where('prodbode_serie', $producto->id)->where('prodbode_sucursal' ,$request->sucursal)->where('prodbode_cantidad','>', 0)->orWhere('prodbode_metros','>', 0)->first();
                if ($productoBode instanceof Prodbode) {
                    if($productoBode->prodbode_cantidad > 0){
                        if ($producto->producto_maneja_serie == true) {
                            //Salidas Series
                            $action = 'modalSerie';
                            $response->action = $action;   
                            $response->tipo = $tipoMovimiento;
                            $response->success = true;
                        }elseif($producto->producto_metrado == true){
                            $action = 'ProductoMetrado';
                            $response->action = $action;   
                            $response->tipo = $tipoMovimiento;
                            $response->success = true;
                        }elseif($producto->producto_vence == true){
                            $action = 'ProductoVence';
                            $response->action = $action;   
                            $response->tipo = $tipoMovimiento;
                            $response->success = true;
                        }else{
                            $action = 'NoSerieNoMetros';
                            $response->action = $action;   
                            $response->tipo = $tipoMovimiento;
                            $response->success = true;
                        }   
                    }else{
                        $response->errors = "No hay unidades en BODEGA de esta SUCURSAL,verifique información ó por favor consulte al administrador.";
                        $response->success = false;
                    }            
                }else{
                    $response->errors = "No es posible recuperar PRODUCTO en PRODBODE,verifique información ó por favor consulte al administrador.";
                    $response->success = false;
                }
            }  
        }else{
            $response->errors = "No es posible realizar movimientos para productos que no manejan unidades";
            $response->success = false;
        }

        return response()->json($response);
    }
    /**
     * Validate actions detail asiento.
     *
     * @return \Illuminate\Http\Response
     */
    public function validation(Request $request)
    {
        // Prepare response
        $response = new \stdClass();
        $response->success = false;
        $response->asiento2_valor = $request->asiento2_valor;

       
        $response->errors = 'No es posible definir acción a validar, por favor verifique la información del asiento o consulte al administrador.';
        return response()->json($response);
    }

    /**
     * Search producto.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if($request->has('producto_serie')) {
            $query = Producto::query();
            $query->select('producto.id as id', 'producto_nombre', 'producto_serie','producto_costo','producto_precio1','impuesto.impuesto_porcentaje');
            $query->join('impuesto','producto_impuesto', '=', 'impuesto.id');
            $query->where('producto_serie', $request->producto_serie);
            $producto = $query->first();
            if($producto instanceof Producto) {
                return response()->json(['success' => true, 'id' => $producto->id, 'producto_nombre' => $producto->producto_nombre, 'producto_serie' => $producto->producto_serie ,'producto_costo'=>$producto->producto_costo, 'producto_precio1'=>$producto->producto_precio1, 'impuesto_porcentaje'=> $producto->impuesto_porcentaje]);
            }
        }
        return response()->json(['success' => false]);
    }
}
