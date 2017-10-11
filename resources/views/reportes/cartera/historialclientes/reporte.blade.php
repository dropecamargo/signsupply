@extends('reportes.layout', ['type' => $type, 'title' => $title])

@section('content')
	<table class="rtable" border="1" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th width="15%">DOCUMENTO</th>
				<th width="15%">NÚMERO</th>
				<th width="50%">SUCURSAL</th>
				<th width="10%">AFEC DOCUMENTO</th>
				<th width="15%">AFEC NÚMERO</th>
				<th width="15%">AFEC CTE</th>
				<th width="15%">FECHA E</th>
				<th width="15%">FECHA DOC</th>
				<th width="15%">FECHA PAGO</th>
				<th width="15%">DEBITO</th>
				<th width="15%">CREDITO</th>
			</tr>
		</thead>
		<tbody>
			{{--@foreach($historyClient as $item)
				<tr>
					<td>{{ $item->documentos_nombre }}</td>
					<td>{{ $item->ajustec1_numero }}</td>
					<td>{{ $item->activofijo_descripcion }}</td>
					<td>{{ number_format($item->activofijo_costo) }}</td>
					<td>{{ number_format($item->activofijo_depreciacion) }}</td>
				</tr>
			@endforeach--}}
		</tbody>
	</table>
@stop