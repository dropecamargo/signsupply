@extends('layout.layout')

@section('title') Notificaciones @stop

@section('content')
    <section class="content-header">
		<h1>
			Tus notificaciones
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('app.home') }}</a></li>
			<li>Notificaciones</li>
		</ol>
    </section>

   	<section class="content" id="notification-main">
        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['id' => 'form-koi-search-tercero-component', 'class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form']) !!}
	            	<div class="row">
	                    <label for="search_fecha" class="col-sm-1 control-label">Fecha</label>
	                    <div class="col-sm-2">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="search_fecha" placeholder="Fecha" class="form-control input-sm datepicker" name="search_fecha" type="text">
                            </div>
	                    </div>

	               		<label for="search_typenotification" class="col-sm-2 control-label">Tipo notificación</label>
	                    <div class="col-sm-3 text-left">
	                        <select name="search_typenotification" id="search_typenotification" class="form-control select2-default-clear">
	                            @foreach( App\Models\Base\TipoNotificacion::getTypes() as $key => $value)
	                                <option value="{{ $key }}">{{ $value }}</option>
	                            @endforeach
	                        </select>
	                    </div>

                        <label for="search_estado" class="col-sm-1 control-label">Estado</label>
	                    <div class="col-sm-3 text-left">
	                        <select name="search_estado" id="search_estado" class="form-control">
                                <option value selected>Todas</option>
                                <option value="F">Pendientes</option>
                                <option value="T">Vistas</option>
	                        </select>
	                    </div>
	                </div><br>
	                <div class="row">
	                	<div class="col-sm-offset-4 col-sm-2 col-xs-4">
                            <button type="button" class="btn btn-default btn-block btn-sm btn-clear">Limpiar</button>
                        </div>
	                	<div class="col-sm-2 col-xs-4">
                            <button type="button" class="btn btn-primary btn-block btn-sm btn-search">Buscar</button>
                        </div>
	                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="box box-solid">
                    <div class="box-body" id="spinner-notification">
                        <ul class="notifications-list" id="notifications-list">
                            @foreach( $notifications as $notification )
                                @if( !$notification->notificacion_visto )
                                    <li class="item view-notification notification-true" data-notification="{{ $notification->id }}">
                                @else
                                    <li class="item view-notification" data-notification="{{ $notification->id }}">
                                @endif
                                    <div class="row">
                                        <div class="col-sm-6 text-left">
                                            <p class="text-green">
                                                <i class="fa fa-phone"></i>
                                                {{ $notification->notificacion_titulo }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <small><p class="text-green">{{ $notification->nfecha }}</p></small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <span class="text-description">{{ $notification->notificacion_descripcion }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach()
                        </ul>
                        {!! $notifications->render() !!}
                    </div>
                </div>
            </div>
        </div>
	</section>
@stop
