<?php
namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\Pedido1;
use Validator;

class Pedido2 extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pedido2';

    public $timestamps = false;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pedido2_cantidad','pedido2_saldo','pedido2_precio'];

    public function isValid($data)
    {
        $rules = [
            'pedido2_pedido1' => 'numeric',
            'pedido2_serie' => 'numeric',
            'pedido2_cantidad' => 'required|numeric',
            //'pedido2_saldo' => 'required|numeric',
            'pedido2_precio' => 'numeric'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return true;
        }
        $this->errors = $validator->errors();
        return false;
    }

   public function storePedido2(Array $data){
        $response = new \stdClass();
        $response->success = false;
        if(!isset($data['Id']) || empty($data['Id'])){
            //Validate Product
            $producto = Producto::where('producto_serie',$data['Producto'])->first();
            if(!$producto instanceof Producto){
                $response->error = "No es posible recuperar Producto, por favor verifique la información del asiento o consulte al administrador.";
                return $response;
            }
            //Validate Esquals Ref & Serie
            if ($producto->producto_serie != $producto->producto_referencia) {
                $response->error = "No es posible recuperar referencia del Producto, por favor verifique la información del asiento o consulte al administrador.";
                return $response;
            }
            $this->pedido2_pedido1 = $data['Pedido'] ?: '';
            $this->pedido2_serie = $producto->id ?: '';
            $this->pedido2_cantidad = $data['Cantidad'] ?: 0;
            $this->pedido2_precio = $data['Precio'] ?: 0;
            $this->save();
        }
        //Response items
        $response->success = true;
        $response->producto_serie = $producto->producto_serie;
        $response->producto_nombre = $producto->producto_nombre;
        $response->pedido2_cantidad = $this->pedido2_cantidad;
        $response->pedido2_precio = $this->pedido2_precio;
        $response->id = $this->id;
        return $response;
    }

    public static function getPedido2($id)
    {
        $query = Pedido2::query();
        $query->select('pedido2.*','producto_serie','producto_nombre')->where('pedido2_pedido1',$id);
        $query->join('producto', 'pedido2_serie', '=' ,'producto.id');
        $query->orderBy('pedido2.id', 'asc');
        return  $query->get();
    }
}
