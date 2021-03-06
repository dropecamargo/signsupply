<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Validator, Cache;

class PuntoVenta extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'puntoventa';

    public $timestamps = false;

    /**
     * The key used by cache store.
     *
     * @var static string
     */
    public static $key_cache = '_points_of_sale';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['puntoventa_nombre', 'puntoventa_prefijo', 'puntoventa_resolucion_dian','puntoventa_numero', 'puntoventa_encabezado','puntoventa_frase','puntoventa_footer1', 'puntoventa_footer2','puntoventa_observacion'];

    protected $nullable = ['puntoventa_resolucion_dian','puntoventa_prefijo'];

    protected $boolean = ['puntoventa_activo'];

    public function isValid($data)
    {
        $rules = [
            'puntoventa_nombre' => 'required|max:200|unique_with:puntoventa,puntoventa_prefijo',
            'puntoventa_prefijo' => 'max:4',
            'puntoventa_numero' => 'min:0',
            'puntoventa_encabezado' => 'max:200',
            'puntoventa_frase' => 'max:200',
            'puntoventa_footer1' => 'max:200',
            'puntoventa_footer2' => 'max:200'
        ];

        if ($this->exists){
            $rules['puntoventa_nombre'] .= ',puntoventa_nombre,' . $this->id;
            $rules['puntoventa_prefijo'] .= ',puntoventa_prefijo,' . $this->id;
        }else{
            $rules['puntoventa_nombre'] .= '|required';
            $rules['puntoventa_prefijo'] .= '|required';
        }

        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return true;
        }
        $this->errors = $validator->errors();
        return false;
    }

    public static function getPuntosVenta()
    {
        if (Cache::has( self::$key_cache )) {
            return Cache::get( self::$key_cache );
        }

        return Cache::rememberForever( self::$key_cache , function() {
            $query = PuntoVenta::query();
            $query->orderby('puntoventa_nombre', 'asc');
            $collection = $query->lists('puntoventa_nombre', 'id');
            $collection->prepend('', '');
            return $collection;
        });
    }
}
