@extends('admin.puntosventa.main')

@section('breadcrumb')
    <li class="active">Puntos de venta</li>
@stop

@section('module')
    <div class="box box-primary" id="puntosventa-main">
        <div class="box-body table-responsive">
            <table id="puntosventa-search-table" class="table table-bordered table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Prefijo</th>
                        <th>Resolución de facturación DIAN</th>
                        <th>Consecutivo</th>
                        <th>Activo</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop
