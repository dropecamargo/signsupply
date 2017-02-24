@extends('admin.tiposactividad.main')

@section('breadcrumb')
	<li><a href="{{ route('tiposactividad.index') }}">Tipos actividad</a></li>
	<li><a href="{{ route('tiposactividad.show', ['tiposactividad' => $tipoactividad->id]) }}">{{ $tipoactividad->id }}</a></li>
	<li class="active">Editar</li>
@stop

@section('module')
	<div class="box box-success" id="tiposactividad-create">
		{!! Form::open(['id' => 'form-tiposactividad', 'data-toggle' => 'validator']) !!}
			<div class="box-body" id="render-form-tipoactividad">
				{{-- Render form tipoactividad --}}
			</div>
			
	        <div class="box-footer with-border">
	        	<div class="row">
					<div class="col-md-2 col-md-offset-4 col-sm-6 col-xs-6 text-left">
						<a href="{{ route('tiposactividad.show', ['tiposactividad' => $tipoactividad->id]) }}" class="btn btn-default btn-sm btn-block">{{ trans('app.cancel') }}</a>
					</div>
					<div class="col-md-2 col-sm-6 col-xs-6 text-right">
						<button type="submit" class="btn btn-primary btn-sm btn-block">{{ trans('app.save') }}</button>
					</div>
				</div>
			</div>
		{!! Form::close() !!}
	</div>
@stop