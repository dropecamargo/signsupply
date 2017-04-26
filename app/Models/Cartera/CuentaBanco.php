<?php

namespace App\Models\Cartera;

use Illuminate\Database\Eloquent\Model;

use App\Models\BaseModel;
use Cache, Validator;

class CuentaBanco extends BaseModel
{
    /**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table = 'cuentabanco';

	public $timestamps = false;

	/**
     * The key used by cache store.
     *
     * @var static string
     */
    public static $key_cache = '_cuentabancos';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = ['cuentabanco_nombre','cuentabanco_numero'];

    protected $boolean = ['cuentabanco_activa'];

	public function isValid($data)
	{
		$rules = [
			'cuentabanco_nombre' => 'required|max:50',
			'cuentabanco_banco' => 'required',
			'cuentabanco_plancuentas' => 'required',
			'cuentabanco_numero' => 'required|max:25',
		];

		$validator = Validator::make($data, $rules);
    	if ($validator->passes()) {
            return true;
        }
		$this->errors = $validator->errors();
		return false;
	}

	public static function getCuentaBanco($id){
        $query = CuentaBanco::query();
        $query->select('cuentabanco.*','banco_nombre', 'plancuentas_nombre','plancuentas_cuenta');
        $query->join('banco', 'cuentabanco_banco', '=', 'banco.id');
        $query->join('plancuentas', 'cuentabanco_plancuentas', '=', 'plancuentas.id');
        $query->where('cuentabanco.id', $id);
        return $query->first();
	}

}
