<p>Olá {{$user->name}},</p>
<p>A sua carga de email foi processada!</p>

<table border="1" cellpadding="0" cellspacing="0">
	<tr>
		<td>SKU</td>
		<td>Estoque Anterior</td>
		<td>Estoque Atual</td>
		<td>Mensagem</td>
	</tr>
@foreach($log as $l)
<tr>
	<td>{{$l['sku']}}</td>
	<td>
		@if(isset($l['estoqueAntigo']))
			{{$l['estoqueAntigo']}}
		@endif
	</td>
	<td>
		@if(isset($l['estoqueAtual']))
			{{$l['estoqueAtual']}}
		@endif
	</td>
	<td>
		@if(isset($l['success']))
			{{$l['success']}}
		@elseif(isset($l['error']))
			{{$l['error']}}
		@endif
	</td>
</tr>
@endforeach
</table>