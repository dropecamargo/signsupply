@extends('tecnico.conceptostecnico.main')

@section('breadcrumb')
    <li><a href="{{ route('conceptostecnico.index')}}">Concepto técnico</a></li>
    <li class="active">{{ $conceptotecnico->id }}</li>
@stop

@section('module')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <label>Nombre</label>
                    <div>{{ $conceptotecnico->conceptotec_nombre }}</div>
                </div>
                <div class="col-sm-2 col-xs-8"><br>
                    <label class="checkbox-inline" for="conceptotec_activo">
                        <input type="checkbox" id="conceptotec_activo" name="conceptotec_activo" value="conceptotec_activo" disabled {{ $conceptotecnico->conceptotec_activo ? 'checked': '' }}> Activo
                    </label>
                </div>
            </div>
        </div>
        <div class="box-footer with-border">
            <div class="row">
                <div class="col-sm-2 col-sm-offset-4 col-xs-6 text-left">
                    <a href=" {{ route('conceptostecnico.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.comeback') }}</a>
                </div>
                <div class="col-sm-2 col-xs-6 text-right">
                    <a href=" {{ route('conceptostecnico.edit', ['conceptotecnico' => $conceptotecnico->id])}}" class="btn btn-primary btn-sm btn-block"> {{trans('app.edit')}}</a>
                </div>
            </div>
        </div>
    </div>
@stop
