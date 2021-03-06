@extends('contabilidad.documentos.main')

@section('breadcrumb')
<li><a href="{{route('documentos.index')}}">Documentos</a></li>
<li class="active">{{ $documento->documento_codigo }}</li>
@stop

@section('module')
<div class="box box-primary">
    <div class="box-body">
        <div class="row">
            <div class="form-group col-sm-3">
                <label class="control-label">Código</label>
                <div>{{ $documento->documento_codigo }}</div>
            </div>
            <div class="form-group col-sm-9">
                <label class="control-label">Nombre</label>
                <div>{{ $documento->documento_nombre }}</div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-6">
                <label class="control-label">Folder</label>
                <div>{{ $documento->folder_nombre }}</div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label class="control-label">Tipo consecutivo</label>
                <div>{{ $documento->documento_tipo_consecutivo ? config('koi.contabilidad.documento.consecutivo')[$documento->documento_tipo_consecutivo] : ''  }}</div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label class="control-label">Consecutivo</label>
                <div>{{ $documento->documento_consecutivo }}</div>
            </div>
            <div class="form-group col-sm-4 col-xs-8">
                <br>
                <label class="checkbox-inline" for="documento_actual">
                    <input type="checkbox" id="documento_actual" name="documento_actual" value="documento_actual" disabled {{ $documento->documento_actual ? 'checked': '' }}> Actual
                </label>
                <label class="checkbox-inline" for="documento_nif">
                    <input type="checkbox" id="documeto_nif" name="documeto_nif" value="documeto_nif" disabled {{ $documento->documento_nif ? 'checked': '' }}> Nif
                </label>
            </div>
        </div>
    </div>

    <div class="box-footer with-border">
        <div class="row">
            <div class="col-sm-offset-4 col-sm-2 col-xs-6 text-left">
                <a href=" {{ route('documentos.index')}} " class="btn btn-default btn-sm btn-block"> {{ trans('app.comeback') }}</a>
            </div>
            <div class="col-sm-2 col-xs-6 text-right">
                <a href=" {{ route('documentos.edit', ['documentos' => $documento->id])}}" class="btn btn-primary btn-sm btn-block">{{trans('app.edit')}}</a>
            </div>
        </div>
    </div>
</div>
@stop
