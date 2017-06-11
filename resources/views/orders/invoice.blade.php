@extends('adminlte::page')

@section('title', 'Faturamento')

@section('content')
	<div class="col-md-12">

    <div class="box box-success">
      <div class="box-header with-border">
      </div>
      <div  class="box-body table-responsive">
        <div class="form-group">
          <label>Fornecedor</label>
          <select class="form-control invoice-provider">
            <option value="">Todos</option>
            @foreach($providers as $provider)
            <option value="{{$provider->id}}"
              @if($id == $provider->id)
              selected
              @endif
            >{{$provider->name}}</option>
            @endforeach
          </select>
        </div>
      </div>

    </div>
		<form action="/invoice" method="POST">

		{!! csrf_field() !!}
		<div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Pedidos</h3>
        <div class="box-tools">
          <input type="submit" value="Faturar" />
        </div>
      </div>
            <div  class="box-body table-responsive">
              <div class="callout callout-info">
                  <h4>Seleção de Transportadoras</h4>
                  <p>Caso necessário, altere o campo abaixo.</p>
                  <div class="form-group">
                    <label>Seleção de Transportadoras</label>
                    <select class="form-control" name="shipping">
                      @foreach($companies as $companie)
                      <option value="{{$companie->id}}">{{$companie->name}} - {{$companie->state}}</option>
                      @endforeach
                    </select>
                  </div>
              </div>
              <table id="invoiceDataTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th><input type="checkbox" class="checkAll"></th>
                    <th>Data</th>
                    <th>Pedido</th>
                    <th>Origem</th>
                    <th>Sku's</th>
                    <th>Nome's</th>
                    <th>Qty</th>
                    <th>Fornecedor</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach($orders as $order)
                <tr>
                	<td><input type="checkbox" name="order[]" value="{{$order->code}}"></td>
					<td>{{date('d/m/Y', strtotime($order->date))}}</td>
          <td>{{$order->code}}</td>
					<td>{{$order->origin}}</td>
					<td>{{$order->psku}}</td>
					<td>{{$order->pname}}</td>
          <td>{{$order->quantity}}</td>
					<td>{{$order->fornecedor}}</td>
          <td>{{$order->ostatus}}</td>
                </tr>
                @endforeach
                </tbody>
             </table>
             </form>

            </div>

        </div>

	</div>

@stop
