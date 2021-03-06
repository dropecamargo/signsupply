@extends('inventario.trasladosubicaciones.main')

@section('breadcrumb')
    <li class="active">Traslados de ubicación</li>
@stop

@section('module')
    <div id="trasladosubicaciones-main">
        <div class="box box-primary">
            <div class="box-body table-responsive">
                <table id="trasladosubicaciones-search-table" class="table table-bordered table-striped" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop
