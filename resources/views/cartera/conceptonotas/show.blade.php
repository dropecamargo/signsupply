@extends('cartera.conceptonotas.main')

@section('breadcrumb')
    <li><a href="{{ route('conceptonotas.index')}}">Concepto de nota</a></li>
    <li class="active">{{ $conceptonota->id }}</li>
@stop

@section('module')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="form-group col-md-4">
                    <label class="control-label">Nombre</label>
                    <div>{{ $conceptonota->conceptonota_nombre }}</div>
                </div>
                <div class="form-group col-md-2 col-xs-8 col-sm-3"><br>
                    <label class="checkbox-inline" for="conceptonota_activo">
                        <input type="checkbox" id="conceptonota_activo" name="conceptonota_activo" value="conceptonota_activo" disabled {{ $conceptonota->conceptonota_activo ? 'checked': '' }}> Activo
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label class="control-label">Nombre</label>
                    <div>{{$conceptonota->plancuentas_cuenta}} - {{ $conceptonota->plancuentas_nombre }}</div>
                </div>
            </div>
        </div>

        <div class="box-footer with-border">
            <div class="row">
                <div class="col-md-2 col-md-offset-4 col-sm-6 col-xs-6 text-left">
                    <a href=" {{ route('conceptonotas.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.comeback') }}</a>
                </div>
                <div class="col-md-2 col-sm-6 col-xs-6 text-right">
                    <a href=" {{ route('conceptonotas.edit', ['conceptonota' => $conceptonota->id])}}" class="btn btn-primary btn-sm btn-block"> {{trans('app.edit')}}</a>
                </div>
            </div>
        </div>
    </div>
@stop
