@extends('tesoreria.conceptosajustep.main')

@section('breadcrumb')
    <li><a href="{{ route('conceptosajustep.index')}}">Concepto ajuste de proveedor</a></li>
    <li><a href="{{ route('conceptosajustep.show', ['conceptoajustep' => $conceptoajustep->id]) }}">{{ $conceptoajustep->id }}</a></li>
	<li class="active">Editar</li>
@stop

@section('module')
	<div class="box box-primary" id="conceptoajustep-create">
		{!! Form::open(['id' => 'form-conceptoajustep', 'data-toggle' => 'validator']) !!}
			<div class="box-body" id="render-form-conceptoajustep">
				{{-- Render form conceptoajustep --}}
			</div>

			<div class="box-footer clearfix">
                <div class="row">
                    <div class="col-sm-2 col-sm-offset-4 col-xs-6 text-left">
						<a href="{{ route('conceptosajustep.index') }}" class="btn btn-default btn-sm btn-block">{{ trans('app.cancel') }}</a>
                    </div>
                    <div class="col-sm-2 col-xs-6 text-right">
						<button type="submit" class="btn btn-primary btn-sm btn-block">{{ trans('app.save') }}</button>
                    </div>
                </div>
            </div>
		{!! Form::close() !!}
	</div>
@stop
