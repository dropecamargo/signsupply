<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use App\Models\Base\Empresa;
use Validator, DB;

class Asiento extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'asiento1';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['asiento1_mes', 'asiento1_ano', 'asiento1_dia', 'asiento1_folder', 'asiento1_documento', 'asiento1_numero', 'asiento1_detalle', 'asiento1_documentos', 'asiento1_id_documentos'];

    public function isValid($data)
    {
        $rules = [
            'asiento1_mes' => 'required|integer',
            'asiento1_ano' => 'required|integer',
            'asiento1_dia' => 'required|integer',
            'asiento1_numero' => 'required|integer',
            'asiento1_folder' => 'required|integer',
            'asiento1_documento' => 'required|integer',
            'asiento1_beneficiario' => 'required'
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            // Validar mes || año en curso
            if ( $data['asiento1_ano'] == date('Y') && (date('n') < $data['asiento1_mes'])) {
                $this->errors = "La fecha no puede ser mayor al mes en curso";
                return false;
            }
            // Armo fecha del asiento
            $fecha_asiento = "{$data['asiento1_ano']}-{$data['asiento1_mes']}-{$data['asiento1_dia']}";

            // Recuperar empresa
            $empresa = Empresa::select('empresa_fecha_cierre_contabilidad as fecha_cierre')->first();
            if (!$empresa instanceof Empresa) {
                $this->errors = 'No es posible definir fecha para el asiento contable';
                return false;
            }
            // Validar contra fecha de cierre
            if (strtotime($fecha_asiento) <= strtotime($empresa->fecha_cierre) ) {
                $this->errors = "La fecha que intenta realizar el asiento: $fecha_asiento no esta PERMITIDA. Es menor a la del cierre contable : $empresa->fecha_cierre";
                return false;
            }
            return true;
        }
        $this->errors = $validator->errors();
        return false;
    }

    public static function getAsiento($id)
    {
        $query = Asiento::query();
        $query->select('asiento1.*', 'folder_nombre', 'documento_nombre', 't.tercero_nit', 'documento_tipo_consecutivo', DB::raw("(CASE WHEN t.tercero_persona = 'N' THEN CONCAT(t.tercero_nombre1,' ',t.tercero_nombre2,' ',t.tercero_apellido1,' ',t.tercero_apellido2) ELSE t.tercero_razonsocial END) as tercero_nombre"), 'u.username as username_elaboro');
        $query->join('tercero as t', 'asiento1_beneficiario', '=', 't.id');
        $query->join('tercero as u', 'asiento1_usuario_elaboro', '=', 'u.id');
        $query->join('documento', 'asiento1_documento', '=', 'documento.id');
        $query->join('folder', 'asiento1_folder', '=', 'folder.id');
        $query->where('asiento1.id', $id);
        return $query->first();
    }

    public function setAsiento1DetalleAttribute($detail)
    {
        $this->attributes['asiento1_detalle'] = strtoupper($detail);
    }
}
