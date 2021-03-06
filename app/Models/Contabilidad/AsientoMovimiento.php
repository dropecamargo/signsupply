<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\Producto, App\Models\Tesoreria\Facturap1;

class AsientoMovimiento extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'asientomovimiento';

    public $timestamps = false;

    public function store(Asiento2 $asiento2, Array $data)
    {
        $response = new \stdClass();
        $response->success = false;

        // Validar factura
        if(!isset($data['Tipo']) || trim($data['Tipo']) == '') {
            $response->error = "Tipo es obligatorio para generar movimiento, por favor verifique la información del asiento o consulte al administrador.";
            return $response;
        }

        // Movimientos factura proveedor FP
        if( $data['Tipo'] == 'FP')
        {
            $result = $this->storeFacturap($asiento2, $data);
            if($result != 'OK') {
                $response->error = $result;
                return $response;
            }

        // Movimientos inventario padre IP, inventario hijos IH
        }else if( in_array($data['Tipo'], ['IP', 'IH']) ) {

            $result = $this->storeInventario($asiento2, $data);
            if($result != 'OK') {
                $response->error = $result;
                return $response;
            }

        // Movimientos factura padre F, factura hijos FH
        }else if( in_array($data['Tipo'], ['F', 'FH'])){
            $result = $this->storeFactura($asiento2, $data);
            if($result != 'OK') {
                $response->error = $result;
                return $response;
            }
        }

        $this->movimiento_tipo = $data['Tipo'];
        $this->movimiento_asiento2 = $asiento2->id;
        $this->save();

        $response->success = true;
        return $response;
    }

    public function storeFacturap(Asiento2 $asiento2, Array $data)
    {
        // Validar valor
        if(!isset($data['Valor']) || !is_numeric($data['Valor']) || $data['Valor'] <= 0) {
            return "Valor no puede ser menor o igual a 0.";
        }

        // Validar factura
        if(!isset($data['Naturaleza']) || trim($data['Naturaleza']) == '') {
            return "Naturaleza es obligatoria.";
        }

        $this->movimiento_facturap = $data['Factura'];
        $this->movimiento_nuevo = $data['Nuevo'];
        $this->movimiento_valor = $data['Valor'];
        $this->movimiento_item = $data['Cuotas'];
        $this->movimiento_observaciones = isset($data['Detalle']) ? $data['Detalle']: '';

        return 'OK';
    }

    public function storeInventario(Asiento2 $asiento2, Array $data)
    {
        switch ($data['Tipo']) {
            // Inventario padre
            case 'IP':
                // Validar valor
                if(!isset($data['Valor']) || !is_numeric($data['Valor']) || $data['Valor'] <= 0) {
                    return "Valor no puede ser menor o igual a 0.";
                }

                // Validar sucursal
                if(!isset($data['Sucursal']) || !is_numeric($data['Sucursal']) || $data['Sucursal'] <= 0) {
                    return "Sucursal es obligatoria.";
                }

                // Validar naturaleza
                if(!isset($data['Naturaleza']) || trim($data['Naturaleza']) == '') {
                    return "Naturaleza es obligatoria.";
                }

                // Validar producto
                if(!isset($data['Producto']) || trim($data['Producto']) == '') {
                    return "Producto es obligatoria.";
                }

                $this->movimiento_valor = $data['Valor'];
                $this->movimiento_sucursal = $data['Sucursal'];
                $this->movimiento_producto = $data['Producto'];
            break;

            // Inventario hijos
            case 'IH':
                // Validar producto
                if(!isset($data['Item']) || trim($data['Item']) == '') {
                    return "Item es obligatorio.";
                }

                if(isset($data['Serie']) && trim($data['Serie']) != '') {
                    $this->movimiento_serie = $data['Serie'];
                }

                if(isset($data['Valor']) && trim($data['Valor']) != '') {
                    $this->movimiento_valor = $data['Valor'];
                }

                $this->movimiento_item = $data['Item'];
            break;
        }
        return 'OK';
    }

    public function storeFactura(Asiento2 $asiento2, Array $data)
    {
        switch ($data['Tipo']) {
            // Factura padre
            case 'F':
                $this->movimiento_factura = $data['Factura'];
            break;

             // Factura hijo
            case 'FH':
                // Validar factura -> child
                if(isset($data['FacturaChild']) && trim($data['FacturaChild']) != '') {
                    $this->movimiento_factura4 = $data['FacturaChild'];
                }

                if(isset($data['Valor']) && trim($data['Valor']) != '') {
                    $this->movimiento_valor = $data['Valor'];
                }
            break;
        }
        $this->movimiento_nuevo = $data['Nuevo'];
        return 'OK';
    }
}
