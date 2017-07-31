<?php

namespace App\Models\Tecnico;

use Illuminate\Database\Eloquent\Model;

use App\Models\Inventario\Prodbode, App\Models\Tecnico\RemRepu, App\Models\Inventario\Producto, App\Models\Inventario\Lote, App\Models\Inventario\Inventario, App\Models\Base\Sucursal, App\Models\Base\Documentos;
use Validator, Auth;

class RemRepu2 extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'remrepu2';

    public $timestamps = false;

    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['remrepu2_cantidad'];

    public function isValid($data)
    {
        $rules = [
            'remrepu2_cantidad' => 'required|numeric|min:1',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return true;
        }
        $this->errors = $validator->errors();
        return false;
    }

    public static function createRemisionL($child, $facturado, $nofacturado, $devuelto, $usado) {
        // Recuperar Remrepu
        $father = RemRepu::find($child->remrepu2_remrepu1);
        if(!$father instanceof Remrepu){
            return "No es posible recuperar remision, por favor verifique la informacion o consulte al administrador.";
        }
        
        // Recupero instancia de sucursal 
        $origen  = Sucursal::find($father->remrepu1_sucursal);
        if (!$origen instanceof Sucursal) {
            return 'No es posible recuperar la sucursal de origen, por favor verifique la información ó consulte al administrador.';
        }

        // Consecutive sucursal_remr
        $consecutive = $origen->sucursal_remr + 1;

        // Duplicate remrepu and store remrepu2
        $item = $father->replicate();
        $item->remrepu1_sucursal = $origen->id;
        $item->remrepu1_numero = $consecutive;
        $item->remrepu1_tipo = 'L';
        $item->save();

        $remrepu2 = new RemRepu2;
        $remrepu2->remrepu2_producto = $child->remrepu2_producto;
        $remrepu2->remrepu2_remrepu1 = $item->id;
        $remrepu2->remrepu2_cantidad = 0;
        $remrepu2->remrepu2_facturado = $facturado;
        $remrepu2->remrepu2_no_facturado = $nofacturado;
        $remrepu2->remrepu2_devuelto = $devuelto;
        $remrepu2->remrepu2_usado = $usado;
        $remrepu2->save();


        if ($devuelto == 1) {
            // Recuperar sucursal => destino
            $destino = Sucursal::where('sucursal_nombre', '091 PROVISIONAL')->first();
            if(!$destino instanceof Sucursal) {
                return 'No es posible recuperar la sucursal de destino, por favor verifique la información ó consulte al administrador.';
            }
            
            // Recuperar instancia de producto 
            $producto = Producto::find($remrepu2->remrepu2_producto);
            if(!$producto instanceof Producto) {
                return 'No es posible recuperar el producto, por favor verifique la información ó consulte al administrador.';
            }

            // Validar Documento
            $documento = Documentos::where('documentos_codigo', RemRepu::$default_document)->first();
            if(!$documento instanceof Documentos) {
                return 'No es posible recuperar documentos,por favor verifique la información ó por favor consulte al administrador.';
            }
            // Detalle traslado Prodbode origen y destino
            $prodbodeOrigen = Prodbode::actualizar($producto, $origen->id, 'S', 1, "");
            if(!$prodbodeOrigen instanceof Prodbode) {
                return $prodbodeOrigen;
            }

            $prodbodeDestino = Prodbode::actualizar($producto, $destino->id, 'E', $remrepu2->remrepu2_devuelto, $destino->sucursal_defecto);
            if(!$prodbodeDestino instanceof Prodbode) {
                return $prodbodeDestino;
            }

            if ($producto->producto_maneja_serie == true) {

                $lote = Lote::actualizar($producto, $origen->id, '', 'S', 1, "" ,date('Y-m-d'), null);
                if (!$lote instanceof Lote) {
                    return $lote;
                }
                // Inventario
                $inventario = Inventario::movimiento($producto, $origen->id, $lote->lote_ubicacion,'REMR', $item->id, 0, 1, 0, 0,0, 0,$lote->id);
                if (!$inventario instanceof Inventario) {
                    return $inventario;
                }
                /**
                *Entrada Inventario a sucursal destino
                */
                $lote = Lote::actualizar($producto, $destino->id, '', 'E', 1, $destino->sucursal_defecto, date('Y-m-d'), null);
                if (!$lote instanceof Lote) {
                    return 'No es posible recuperar lote, por favor verifique la información ó por favor consulte al administrador';
                }
                // Inventario
                $inventario = Inventario::movimiento($producto, $destino->id, $destino->sucursal_defecto,'REMR', $item->id, 1, 0, 0, 0,0, 0, $lote->id);
                if (!$inventario instanceof Inventario) {
                    return $inventario;
                }
            }
        }

        // Update sucursal_remr 
        $origen->sucursal_remr = $consecutive;
        $origen->save();
        
        return 'OK';
    }
}
