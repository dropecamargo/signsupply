<?php

namespace App\Models\Cartera;

use Illuminate\Database\Eloquent\Model;

use App\Models\BaseModel;
use Validator,DB;

class Factura1 extends BaseModel
{
    /**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table = 'factura1';

	public $timestamps = false;

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    public static $default_document = 'FACT';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = ['factura1_cuotas','factura1_primerpago','factura1_plazo','factura1_observaciones','factura1_bruto','factura1_descuento','factura1_iva','factura1_retencion','factura1_total'];


    protected $boolean = ['factura1_anular'];

	public function isValid($data)
	{
		$rules = [
		    'factura1_sucursal' => 'required|numeric',
		    'factura1_numero' => 'required|numeric',
		    'factura1_puntoventa' => 'required|numeric',
			'factura1_cuotas' => 'required|numeric|min:0',
			'factura1_plazo' => 'required|numeric|min:0',
			'factura1_primerpago' => 'required|date',
			'factura1_descuento' => 'required|numeric',
			'factura1_bruto' => 'required|numeric',
			'factura1_iva' => 'required|numeric',
			'factura1_total' => 'required|numeric'
		];

		$validator = Validator::make($data, $rules);
    	if ($validator->passes()) {
            // Validar Carrito
            $factura2 = isset($data['factura2']) ? $data['factura2'] : null;
            if(!isset($factura2) || $factura2 == null || !is_array($factura2) || count($factura2) == 0) {
                $this->errors = 'Por favor ingrese el detalle para realizar la factura2.';
                return false;
            }

            return true;
        }

		$this->errors = $validator->errors();
		return false;
	}
	public static function getFactura($id)
	{
		$query = Factura1::query();
		$query->select('factura1.*','sucursal_nombre','puntoventa.*','tercero_nit',DB::raw("CONCAT(tercero_nombre1, ' ', tercero_nombre2, ' ', tercero_apellido1, ' ', tercero_apellido2) as tercero_nombre"));
		$query->join('sucursal','factura1.factura1_sucursal','=', 'sucursal.id');
		$query->join('tercero','factura1.factura1_tercero','=', 'tercero.id');
		$query->join('puntoventa','factura1.factura1_puntoventa','=', 'puntoventa.id');
		$query->where('factura1.id',$id);
		return $query->first();
	}

	public function validar(){
		// Valido Fecha
		$diferencia =  strtotime('now')-strtotime($this->factura1_fh_elaboro);
		$diferencia_dias = intval((($diferencia/60)/60)/24);
		if ($diferencia_dias > 30) {
			return false;
		}
		// Valido atributos de estos models
		$factura2 = Factura2::where('factura2_factura1', $this->id)->first();
		$factura3 = Factura3::where('factura3_factura1', $this->id)->get();

		if ($factura2->factura2_devueltas > 0) {
			return false;
		}
		// Update saldo factura3
		foreach ($factura3 as $value) {
			if (($value->factura3_valor - $value->factura3_saldo) > 0) {
				return false;
			}
			$value->factura3_saldo = 0;
			$value->save();
		}

		return true;
	}

}
