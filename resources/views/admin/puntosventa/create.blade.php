@extends('admin.puntosventa.main')

@section('breadcrumb')
    <li><a href="{{ route('puntosventa.index')}}">Punto de venta</a></li>
	<li class="active">Nueva</li>
@stop

@section('module')
	<div class="box box-primary" id="puntosventa-create">
		{!! Form::open(['id' => 'form-puntosventa', 'data-toggle' => 'validator']) !!}
			<div class="box-body" id="render-form-puntosventa">
				{{-- Render form puntosventa --}}
			</div>

	        <div class="box-footer with-border">
	        	<div class="row">
					<div class="col-sm-2 col-sm-offset-4 col-xs-6 text-left">
						<a href="{{ route('puntosventa.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.cancel') }}</a>
					</div>
					<div class="col-sm-2 col-xs-6 text-right">
						<button type="submit" class="btn btn-primary btn-sm btn-block">{{ trans('app.create') }}</button>
					</div>
				</div>
			</div>
		{!! Form::close() !!}
	</div>
@stop
