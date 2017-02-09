@extends('admin.sucursales.main')

@section('breadcrumb')
    <li><a href="{{ route('sucursales.index')}}">Sucursales</a></li>
    <li class="active">{{ $sucursal->id }}</li>
@stop

@section('module')
    <div class="box box-warning">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-md-2 col-sm-6 col-xs-6 text-left">
                    <a href=" {{ route('sucursales.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.comeback') }}</a>
                </div>
                <div class="col-md-2 col-md-offset-8 col-sm-6 col-xs-6 text-right">
                    <a href=" {{ route('sucursales.edit', ['sucursales' => $sucursal->id])}}" class="btn btn-primary btn-sm btn-block"> {{trans('app.edit')}}</a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="form-group col-md-8">
                    <label class="control-label">Nombre</label>
                    <div>{{ $sucursal->sucursal_nombre }}</div>
                    <label class="control-label">Direccion</label>
                    <div>{{ $sucursal->sucursal_direccion }}</div>
                </div>
            </div>
        </div>
    </div>
@stop