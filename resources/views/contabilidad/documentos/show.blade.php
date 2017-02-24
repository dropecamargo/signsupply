@extends('contabilidad.documentos.main')

@section('breadcrumb')
<li><a href="{{route('documentos.index')}}">Documentos</a></li>
<li class="active">{{ $documento->documento_codigo }}</li>
@stop

@section('module')
<div class="box box-success">
    <div class="box-body">
        <div class="row">
            <div class="form-group col-md-3">
                <label class="control-label">Código</label>
                <div>{{ $documento->documento_codigo }}</div>
            </div>
            <div class="form-group col-md-9">
                <label class="control-label">Nombre</label>
                <div>{{ $documento->documento_nombre }}</div>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col-md-6">
                <label class="control-label">Folder</label>
                <div>{{ $documento->folder_nombre }}</div>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col-md-4">
                <label class="control-label">Tipo consecutivo</label>
                <div>{{ $documento->documento_tipo_consecutivo ? config('koi.contabilidad.documento.consecutivo')[$documento->documento_tipo_consecutivo] : ''  }}</div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-4">
                <label class="control-label">Consecutivo</label>
                <div>{{ $documento->documento_consecutivo }}</div>
            </div>
        </div>
    </div>
    
    <div class="box-footer with-border">
        <div class="row">
            <div class="col-md-2 col-md-offset-4 col-sm-6 col-xs-6 text-left">
                <a href=" {{ route('documentos.index')}} " class="btn btn-default btn-sm btn-block"> {{ trans('app.comeback') }}</a>
            </div>
            <div class="col-md-2 col-sm-6 col-xs-6 text-right">
                <a href=" {{ route('documentos.edit', ['documentos' => $documento->id])}}" class="btn btn-primary btn-sm btn-block">{{trans('app.edit')}}</a>
            </div>
        </div>
    </div>
</div>
@stop
