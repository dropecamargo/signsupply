<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Empresa extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'empresa';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $boolean = ['empresa_pedidoc'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['empresa_niif', 'empresa_iva', 'empresa_cc_contador', 'empresa_tj_contador', 'empresa_nm_contador', 'empresa_cc_revisor', 'empresa_tj_revisor', 'empresa_nm_revisor'];

    public static function getEmpresa()
    {
    	$query = Empresa::query();
        $query->select('empresa.*', 'tercero_nit', 'tercero_digito', 'tercero_regimen', 'tercero_tipo', 'tercero_persona', 'tercero_razonsocial', 'tercero_nombre1', 'tercero_nombre2', 'tercero_apellido1', 'tercero_apellido2', 'tercero_direccion', 'tercero_dir_nomenclatura', 'tercero_municipio', 'tercero_email', 'tercero_telefono1', 'tercero_telefono2', 'tercero_fax', 'tercero_celular', 'tercero_actividad', 'tercero_cc_representante', 'tercero_representante', 'tercero_responsable_iva', 'tercero_autoretenedor_cree', 'actividad_tarifa', 'tercero_gran_contribuyente', 'tercero_autoretenedor_renta', 'tercero_autoretenedor_ica');
		$query->join('tercero', 'empresa_tercero', '=', 'tercero.id');
		$query->leftJoin('municipio', 'tercero_municipio', '=', 'municipio.id');
		$query->leftJoin('actividad', 'tercero_actividad', '=', 'actividad.id');
    	return $query->first();
    }

    public function setEmpresaNmContadorAttribute($name)
    {
        $this->attributes['empresa_nm_contador'] = strtoupper($name);
    }

    public function setEmpresaNmRevisorAttribute($name)
    {
        $this->attributes['empresa_nm_revisor'] = strtoupper($name);
    }
}
