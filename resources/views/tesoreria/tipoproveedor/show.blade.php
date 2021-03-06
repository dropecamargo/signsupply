@extends('tesoreria.tipoproveedor.main')

@section('breadcrumb')
    <li><a href="{{ route('tipoproveedores.index')}}">Tipo de proveedor</a></li>
    <li class="active">{{ $tipoproveedor->id }}</li>
@stop

@section('module')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-5">
                    <label class="control-label">Nombre</label>
                    <div>{{ $tipoproveedor->tipoproveedor_nombre }}</div>
                </div>
                <div class="col-md-5 col-xs-12 col-sm-12">
                    <label class="control-label">Plan cuentas</label>
                    <div>{{ $tipoproveedor->plancuentas_cuenta}} - {{ $tipoproveedor->plancuentas_nombre }}</div>
                </div>
                <div class="col-sm-2 col-xs-8">
                    <label class="checkbox-inline" for="tipoproveedores_activo">
                        <input type="checkbox" id="tipoproveedores_activo" name="tipoproveedores_activo" value="tipoproveedores_activo" disabled {{ $tipoproveedor->tipoproveedor_activo ? 'checked': '' }}> Activo
                    </label>
                </div>
            </div>
        </div>
        <div class="box-footer with-border">
            <div class="row">
                <div class="col-sm-2 col-sm-offset-4 col-xs-6 text-left">
                    <a href=" {{ route('tipoproveedores.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.comeback') }}</a>
                </div>
                <div class="col-sm-2 col-xs-6 text-right">
                    <a href=" {{ route('tipoproveedores.edit', ['tipoproveedores' => $tipoproveedor->id])}}" class="btn btn-primary btn-sm btn-block"> {{trans('app.edit')}}</a>
                </div>
            </div>
        </div>
    </div>
@stop
