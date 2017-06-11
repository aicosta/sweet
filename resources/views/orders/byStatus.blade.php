@extends('adminlte::page')

@section('title', 'Pedidos por Status')

@section('content')
	<div class="col-md-12">
		{!! csrf_field() !!}
    <!--

-->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Filtros</h3>
      </div>
      <div  class="box-body table-responsive no-padding">
        <div class="col-md-3">
          <div class="form-group">
            <label>Status</label>
            <select class="form-control orderStatus">
              <option value="">Todos</option>
              @foreach($statuses as $status)
              <option value="{{$status->id}}">{{$status->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

    </div>
		<div class="box box-default">
			<div class="box-header with-border">

        <h3 class="box-title">Lista de Pedidos</h3>
            <div  class="box-body table-responsive no-padding">

              <table id="invoiceDataTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th></th>
                    <th>Data</th>
                    <th>Pedido</th>
                    <th>Origem</th>
                    <th>Estado</th>
                    <th>Sku's</th>
                    <th>Nome's</th>
                    <th>Qty</th>
                    <th>E. In</th>
                    <th>E. Ex</th>
                    <th>Status</th>
                    <th>NFE</th>
                    <th>Data Nfe</th>
                    <th>Rastreio</th>
                    <th>Fornecedor</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach($orders as $order)
                  <tr>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      <input type="checkbox" name="order[]" value="{{$order['pedido']}}">
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!date('d/m/Y', strtotime($order['data']))!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['pedido']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['origem']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['state']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['sku']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['nome']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['quantidade']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['estoqueInterno']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['estoqueExterno']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['status']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['nota']!!}
                      </a>
                    </td>
                     <td>
                    <a href="/orders/view/{{$order['id']}}">
                      @if($order['dataNota'] != '')
                      {!!date('d/m/Y', strtotime($order['dataNota']))!!}
                      @endif
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['rastreio']!!}
                      </a>
                    </td>
                    <td>
                    <a href="/orders/view/{{$order['id']}}">
                      {!!$order['fornecedor']!!}
                      </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
             </table>

            </div>

        </div>

	</div>

@stop
