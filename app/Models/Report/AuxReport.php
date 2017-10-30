<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;
use DB, Log;

class AuxReport extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
	*/
    protected $table = 'auxreporte';

    public $timestamps = false;

    public static function insertInTable($document, $regionales, $type, $rollback) 
    {
    	$valuesRegionales = array(
    		'1V' => 'auxreporte_double0',
    		'1D' => 'auxreporte_double1',
    		'1d' => 'auxreporte_double2',
    		'2V' => 'auxreporte_double3',
    		'2D' => 'auxreporte_double4',
    		'2d' => 'auxreporte_double5',
    		'3V' => 'auxreporte_double5',
    		'3D' => 'auxreporte_double6',
    		'3d' => 'auxreporte_double7',
    		'4V' => 'auxreporte_double8',
    		'4D' => 'auxreporte_double9',
    		'4d' => 'auxreporte_double10',
    		'5V' => 'auxreporte_double11',
    		'5D' => 'auxreporte_double12',
    		'5d' => 'auxreporte_double13',
    	);
    	DB::beginTransaction();
    	try {
    		if ($type == 'F') {

    			$d = [];
	    		foreach ($document as $key => $item) 
	    		{
	    			$referenciaVenta = $item->regional.'V';
	    			$referenciaDescuento = $item->regional.'D';

			        $auxReport = new AuxReport;
			        $auxReport->auxreporte_varchar1 = $item->unidadnegocio_nombre; 
			        $auxReport->auxreporte_varchar2 = $item->linea_nombre; 
			        $auxReport->auxreporte_varchar3 = $item->categoria_nombre; 
			        $auxReport->auxreporte_varchar4 = $item->subcategoria_nombre; 
			        $auxReport->$valuesRegionales[$referenciaVenta] = $item->ventas;
			    	$auxReport->$valuesRegionales[$referenciaDescuento] = $item->descuentos; 
			        $auxReport->auxreporte_integer1 = $item->unidadnegocio; 
			        $auxReport->auxreporte_integer2 = $item->linea; 
			        $auxReport->auxreporte_integer3 = $item->categoria; 
			        $auxReport->auxreporte_integer4 = $item->subcategoria; 
			        $auxReport->save();
	    		}
    		} elseif ($type == 'DEV') {
    			
    		}

    		if ($rollback) 
    			DB::rollback();

    	} catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
    		abort(500);
    	}
    }
}