@extends('contabilidad.tipoactivo.main')

@section('breadcrumb')
    <li><a href="{{ route('tipoactivos.index')}}">Tipo gasto</a></li>
    <li><a href="{{ route('tipoactivos.show', ['tipoactivo' => $tipoactivo->id]) }}">{{ $tipoactivo->id }}</a></li>
	<li class="active">Editar</li>
@stop

@section('module')
	<div class="box box-primary" id="tipoactivo-create">
		{!! Form::open(['id' => 'form-tipoactivo', 'data-toggle' => 'validator']) !!}
			<div class="box-body" id="render-form-tipoactivo">
				{{-- Render form tipoactivo --}}
			</div>

			<div class="box-footer clearfix">
                <div class="row">
                    <div class="col-sm-2 col-sm-offset-4 col-xs-6 text-left">
						<a href="{{ route('tipoactivos.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.cancel') }}</a>
                    </div>
                    <div class="col-sm-2 col-xs-6 text-right">
						<button type="submit" class="btn btn-primary btn-sm btn-block">{{ trans('app.save') }}</button>
                    </div>
                </div>
            </div>
		{!! Form::close() !!}
	</div>
@stop
