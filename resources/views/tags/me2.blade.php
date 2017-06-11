@extends('adminlte::page')

@section('title', 'Lista de Produtos')

@section('content')
	<div class="col-md-12">

		{!! csrf_field() !!}
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Lista de Pedidos</h3>
				 <div class="box-tools">
	              </div>
            </div>
            <div class="box-body table-responsive no-padding">
            
              <table class="table table-hover">
                <tr>
                  <th>nfe</th>
                  <th>Pedido</th>
                  <th>Origem</th>
                  <th>Sku's</th>
                  <th>Nome's</th>
                  <th>Fornecedor</th>

                </tr>

                 @foreach($orders as $order)
                <tr>
					<td>{{$order->number}}</td>
          <td>{{$order->code}}</td>
					<td>{{$order->origin}}</td>
					<td>{{$order->sku}}</td>
					<td>{{$order->name}}</td>
					<td>{{$order->fornecedor}}</td>
                </tr>
                @endforeach
             </table>
           
            </div>
            
        </div>

	</div>

@stop