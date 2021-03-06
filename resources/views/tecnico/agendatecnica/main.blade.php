@extends('layout.layout')

@section('title') Agenda tecnica @stop

@section('content')
   	<section class="content-header">
        <h1>
            Agenda tecnica <small>Administración de agenda tecnica</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('app.home') }}</a></li>
          	<li>Agenda tecnica</li>
        </ol>
    </section>

    <section class="content" id="soportetecnico-main">
        <div class="box box-primary">
            <div class="box-body">
            	{!! Form::open(['id' => 'form-koi-search-tercero-component', 'class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form']) !!}
	            	<div class="row">
	                    <label for="search_tercero" class="col-sm-1 control-label">Tercero</label>
	                    <div class="col-sm-2">
	                        <div class="input-group input-group-sm">
	                            <span class="input-group-btn">
	                                <button type="button" class="btn btn-default btn-flat btn-koi-search-tercero-component-table" data-field="search_tercero">
	                                    <i class="fa fa-user"></i>
	                                </button>
	                            </span>
	                            <input id="search_tercero" placeholder="Tercero" class="form-control tercero-koi-component input-sm" name="search_tercero" type="text" maxlength="15" data-name="search_tercero_nombre" required>
	                        </div>
	                    </div>
	                    <div class="col-sm-4">
	                        <input id="search_tercero_nombre" name="search_tercero_nombre" placeholder="Tercero beneficiario" class="form-control input-sm" type="text" maxlength="15" readonly required>
	                    </div>

	               		<label for="search_technical" class="col-sm-1 control-label">Tecnico</label>
	                    <div class="col-sm-4 text-left">
	                        <select name="search_technical" id="search_technical" class="form-control select2-default-clear change-technical" required>
	                            @foreach( App\Models\Base\Tercero::getTechnicals() as $key => $value)
	                                <option value="{{ $key }}">{{ $value }}</option>
	                            @endforeach
	                        </select>
	                    </div>
	                </div><br>
	                <div class="row">
	                	<div class="col-sm-2 col-sm-offset-4 col-xs-4">
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
            <div class="col-sm-4">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">Ordenes</h4>
                    </div>
                    <div class="box-body">
                        <div id="external-events">
                            <div class="external-event bg-green ui-draggable ui-draggable-handle">Abierta</div>
                            <div class="external-event bg-red ui-draggable ui-draggable-handle">Cerrada</div>
                        </div>
                    </div>
                </div>
            </div>

		   	<div class="col-sm-8">
		        <div class="box box-solid" id="spinner-calendar">
		        	<div class="box-body">
		                <div id="calendar">
		                    {{-- Render --}}
		                </div>
		            </div>
		        </div>
		   	</div>
        </div>
    </section>

    <!-- Modal add tcontacto -->
	<div class="modal fade" id="modal-event-component" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header small-box {{ config('koi.template.bg') }}">
					<button type="button" class="close icon-close-koi" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="inner-title-modal modal-title"></h4>
				</div>
				{!! Form::open(['id' => 'form-event-component', 'data-toggle' => 'validator']) !!}
					<div class="modal-body">
						<div class="content-modal"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>

	<script type="text/template" id="add-info-event-tpl">
		<div class="row">
            <div class="col-sm-4">
				<label class="control-label">Estado</label>
                <div>
                    <% if( parseInt(orden_abierta) ){ %>
                        <span class="label label-success">ABIERTA</span>
                    <% }else{ %>
                        <span class="label label-danger">CERRADA</span>
                    <% } %>
                </div>
	        </div>
			<div class="col-sm-4">
				<label class="control-label">F.Servicio</label>
				<div><%- fecha_servicio %></div>
	        </div>
	        <div class="col-sm-4">
				<label class="control-label">H.Servicio</label>
				<div><%- hora_servicio %></div>
	        </div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<label class="control-label">Tercero</label>
				<div><%- tercero_nombre %></div>
	        </div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<label class="control-label">Tecnico</label>
				<div><%- tecnico_nombre %></div>
	        </div>
		</div>
	</script>
@stop
