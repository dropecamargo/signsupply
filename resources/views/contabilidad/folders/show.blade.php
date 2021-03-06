@extends('contabilidad.folders.main')

@section('breadcrumb')
    <li><a href="{{ route('folders.index')}}">Folders</a></li>
    <li class="active">{{ $folder->folder_codigo }}</li>
@stop

@section('module')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="form-group col-md-2">
                    <label class="control-label">Código</label>
                    <div>{{ $folder->folder_codigo }}</div>
                </div>

                <div class="form-group col-md-8">
                    <label class="control-label">Nombre</label>
                    <div>{{ $folder->folder_nombre }}</div>
                </div>
            </div>
        </div>

        <div class="box-footer with-border">
            <div class="row">
                <div class="col-sm-offset-4 col-sm-2 col-xs-6 text-left">
                    <a href=" {{ route('folders.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.comeback') }}</a>
                </div>
                <div class="col-sm-2 col-xs-6 text-right">
                    <a href=" {{ route('folders.edit', ['folders' => $folder->id])}}" class="btn btn-primary btn-sm btn-block"> {{trans('app.edit')}}</a>
                </div>
            </div>
        </div>
    </div>
@stop
