@extends('adminlte::page')

@section('title', 'Lista de Produtos')

@section('content')
    @if (session('msg'))
        <div class="col-md-12">
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Alert!</h4>
            {!! session('msg') !!}
          </div>

        </div>
    @endif
	<div class="col-md-12">
		<form action="/tags" method="POST">

		{!! csrf_field() !!}
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Lista de Pedidos</h3>
				 <div class="box-tools">
				 	<input type="submit" value="Emitir Etiquetas" />
	              </div>
	              <div class="form-group">
                  <label>Fornecedor</label>
                  <select class="form-control invoice-provider">
                    <option value="">Todos</option>
                    @foreach($providers as $provider)
                    <option value="{{$provider->id}}">{{$provider->name}}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">

              <table id="invoiceDataTable" class="table table-hover">
                <thead>
                  <tr>
                    <th></th>
                    <th>Pedido</th>
                    <th>NFE</th>
                    <th>Origem</th>
                    <th>Sku's</th>
                    <th>Nome's</th>
                    <th>Fornecedor</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach($orders as $order)
                <tr>
                	<td><input type="checkbox" name="order[]" value="{{$order->code}}"></td>
        					<td>{{$order->code}}</td>
        					<td>{{$order->number}}</td>
                  <td>{{$order->origin}}</td>
        					<td>{{$order->psku}}</td>
        					<td>{{$order->pname}}</td>
                  <td>{{$order->fornecedor}}</td>
                </tr>
                @endforeach
                </tbody>
             </table>
             </form>

            </div>

        </div>

	</div>

@stop
