@extends('reportes.layout', ['type' => $type, 'title' => $title])

@section('content')
	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th width="15%" class="left">CUENTA</th>
				<th width="37%" class="left">NOMBRE</th>
				<th width="12%" class="center">INICIAL</th>
				<th width="12%" class="center">DEBITO</th>
				<th width="12%" class="center">CREDITO</th>
				<th width="12%" class="center">FINAL</th>
			</tr>
		</thead>
		<tbody>
			@if(count($saldos) > 0)
				{{--*/ $sdebito = $scredito = $tfinal = $tinicio = 0; /*--}}
				@foreach($saldos as $saldo)
					<tr>
						<td class="left">{{ $saldo->plancuentas_cuenta }}</td>
						<td class="left">{{ $saldo->plancuentas_nombre }}</td>
						<td class="right">{{ number_format($saldo->inicial,2,'.',',') }}</td>
						<td class="right">{{ number_format($saldo->debitomes,2,'.',',') }}</td>
						<td class="right">{{ number_format($saldo->creditomes,2,'.',',') }}</td>
						{{-- Calculo final --}}
						{{--*/
							if($saldo->plancuentas_naturaleza == 'D') {
								$final = $saldo->inicial + ($saldo->debitomes - $saldo->creditomes);
							}else if($saldo->plancuentas_naturaleza == 'C'){
								$final = $saldo->inicial + ($saldo->creditomes - $saldo->debitomes);
							}
						/*--}}
						<td class="right">{{ number_format($final,2,'.',',') }}</td>

						{{-- Calculo totales --}}
						{{--*/
							if($saldo->plancuentas_nivel == 1) {
								$sdebito = $saldo->debitomes + $sdebito;
								$scredito = $saldo->creditomes + $scredito;
								$tfinal += $final;
								$tinicio += $saldo->inicial;
							}
						/*--}}
					</tr>
				@endforeach
				<tr>
					<td colspan="2" class="right bold">TOTAL</td>
					<td class="right bold">{{ number_format($tinicio,2,'.',',') }}</td>
					<td class="right bold">{{ number_format($sdebito,2,'.',',') }}</td>
					<td class="right bold">{{ number_format($scredito,2,'.',',') }}</td>
					<td class="right bold">{{ number_format($tfinal,2,'.',',') }}</td>
				</tr>
			@endif
		</tbody>
	</table>
@stop
