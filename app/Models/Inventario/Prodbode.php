<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Base\Sucursal;

class Prodbode extends Model
{
	/**
	* The database table used by the model.
	*
	* @var string
	*/
    protected $table = 'prodbode';

    public $timestamps = false;

    public static function prodbode(Producto $producto, $sucursal) {
	    $query = Prodbode::query();
	    $query->select('prodbode.*', DB::raw('(prodbode_cantidad - prodbode_reservada) as disponibles'));
	    $query->where('prodbode_serie', $producto->id);
	    $query->where('prodbode_sucursal', $sucursal);
	    return $query->first();
    }

    public static function actualizar(Producto $producto, $sucursal, $tipo, $cantidad, $ubicacion)
 	{
        // Validar sucursal
        $sucursal = Sucursal::find($sucursal);
        if(!$sucursal instanceof Sucursal) {
            return "No es posible recuperar sucursal prodbode, por favor verifique la información o consulte al administrador.";
        }

        // Validar unidades
        if(!is_numeric($cantidad) || $cantidad <= 0){
            return "No es posible recuperar unidades prodbode, por favor verifique la información o consulte al administrador.";
        }
        switch ($tipo) {
            case 'E':
                // Recuperar prodbode
                $prodbode = Prodbode::where('prodbode_serie', $producto->id)->where('prodbode_sucursal', $sucursal->id)->where('prodbode_ubicacion', $ubicacion)->first();

                if(!$prodbode instanceof Prodbode){
                    $prodbode = new Prodbode;
                    $prodbode->prodbode_serie = $producto->id;
                    $prodbode->prodbode_sucursal = $sucursal->id;
                    $prodbode->prodbode_ubicacion = $ubicacion;
                }
                if ($producto->producto_metrado == true) {
                    $prodbode->prodbode_metros = ($prodbode->prodbode_metros + $cantidad);
                }
                $prodbode->prodbode_cantidad = ($prodbode->prodbode_cantidad + $cantidad);
            break;
            case 'S':

                if ($producto->producto_maneja_serie != true) {
                    // Recuperar prodbode
                    $query = Prodbode::where('prodbode_serie', $producto->id)->where('prodbode_sucursal', $sucursal->id)->where('prodbode_ubicacion', $ubicacion);
                }else{
                    // Recuperar prodbode
                    $query = Prodbode::where('prodbode_serie', $producto->id )->where('prodbode_sucursal', $sucursal->id);
                }
                $prodbode = $query->first();

                // Validar prodbode
                if (!$prodbode instanceof Prodbode) {
                    return "NO es posible recuperar producto $producto->producto_nombre de la bodega $sucursal->sucursal_nombre, por favor verifique la información o consulte al administrador.";
                }
                // Validar disponibles
                if ($producto->producto_metrado == true) {
                    if ($cantidad > $prodbode->prodbode_metros) {
                        return "No existen suficientes unidades para salida producto {$producto->producto_nombre}, disponibles {$prodbode->prodbode_metros}, salida $cantidad, por favor verifique la información o consulte al administrador.";
                    }
                    $prodbode->prodbode_metros = ($prodbode->prodbode_metros - $cantidad);
                }
                if($cantidad > $prodbode->prodbode_cantidad){
                    return "No existen suficientes unidades para salida producto {$producto->producto_nombre}, disponibles {$prodbode->prodbode_cantidad}, salida $cantidad, por favor verifique la información o consulte al administrador.";
                }
                $prodbode->prodbode_cantidad = ($prodbode->prodbode_cantidad - $cantidad);
            break;

            default:
                return "No es posible recuperar tipo movimiento prodbode, por favor verifique la información o consulte al administrador.";
            break;
        }
		$prodbode->save();

        return $prodbode;
    }
    
    public static function reportExist( $sucursal, $producto)
    {
        $query = Prodbode::query();
        $query->select('prodbode_serie', 'prodbode_sucursal','prodbode_cantidad as cantidad', 'prodbode_metros as metros' );
        $query->where('prodbode_serie', $producto);
        $query->where('prodbode_sucursal', $sucursal);
        $cantidades = $query->get();
        return $cantidades;
    }
}
