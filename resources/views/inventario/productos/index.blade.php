@extends('inventario.productos.main')

@section('breadcrumb')
    <li class="active">Productos</li>
@stop

@section('module')
    <div id="productos-main">
        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['id' => 'form-koi-search-producto-component', 'class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form']) !!}
                    <div class="form-group">
                        <label for="producto_serie" class="col-md-1 control-label">Serie</label>
                        <div class="col-md-2">
                            {!! Form::text('producto_serie', session('search_producto_serie'), ['id' => 'producto_serie', 'class' => 'form-control input-sm', 'placeholder' => 'Serie producto']) !!}
                        </div>

                        <label for="producto_nombre" class="col-md-1 control-label">Nombre</label>
                        <div class="col-md-7">
                            {!! Form::text('producto_nombre', session('search_producto_nombre'), ['id' => 'producto_nombre', 'class' => 'form-control input-sm input-toupper', 'placeholder' => 'Nombre del producto']) !!}
                        </div>
                        <div class="col-md-1">
                            <a href="#" class="btn btn-default btn-sm btn-import-modal"><i class="fa fa-upload"></i> Importar</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-2 col-xs-4">
                            <button type="button" class="btn btn-default btn-block btn-sm btn-clear">Limpiar</button>
                        </div>
                        <div class="col-md-2 col-xs-4">
                            <button type="button" class="btn btn-primary btn-block btn-sm btn-search">Buscar</button>
                        </div>
                        <div class="col-md-2 col-xs-4">
                            <a href="{{ route('productos.create') }}" class="btn btn-default btn-block btn-sm">
                                <i class="fa fa-plus"></i> Nuevo
                            </a>
                        </div>
                    </div>
                {!! Form::close() !!}

                <div class="table-responsive">
                    <table id="productos-search-table" class="table table-bordered table-striped" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th width="25%">Referencia</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
