@extends('adminlte::page')

@section('title', 'Visualização de produtos')

@section('content')
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Opções de busca</h3>
				 <div class="box-tools"></div>
            </div>
            <form action="/orders" method="get">
        		<div class="box-body table-responsive no-padding">
		        	<div class="col-md-2">
		        		<div class="form-group">
		                  <label>Tipo de Busca</label>
		                  <select class="form-control" name="type">
		                    <option value="1">Nº do pedido</option>
		                    <option value="2">CPF</option>
		                    <option value="3">Nota Fiscal</option>
		                    <option value="4">Nome do cliente</option>
		                    <option value="5">Produto</option>
		                    <option value="6">Rastreio</option>
		                  </select>
		                </div>
		            </div>
		            <div class="col-md-10">
		        		<div class="form-group">
		                  <label for="exampleInputEmail1">Termo de Busca</label>
		                  <input type="text" name="search" class="form-control pull-right" placeholder="Buscar" value="">
		                </div>
		            </div>

		        </div>
		        <div class="box-footer">
	                <button type="submit" class="btn btn-primary">Procurar</button>
	              </div>
            </form>
        </div>
    </div>
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Lista de Pedidos</h3>
				 <div class="box-tools"></div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
              	<thead>
	                <tr>
	                  <th>Codigo Externo</th>
	                  <th>Data</th>
	                  <th>Nome</th>
	                  <th>CPF</th>
	                  <th>Origem</th>
	                  <th>Valor</th>
	                  <th>Frete</th>
	                  <th>NFE</th>
	                  <th>Data Fatura</th>
	                  <th>Cod.Rastreio</th>
	                  <th>Status</th>
	                </tr>
	            </thead>
	            <tbody>

                 @foreach($orders as $order)
                <tr>
					<td><a href="/orders/view/{{$order->id}}">{{$order->code}}</a></td>
					<td><a href="/orders/view/{{$order->id}}">{{date('d/m/Y', strtotime($order->created_at))}}</a></td>
					<td><a href="/orders/view/{{$order->id}}">{{strtoupper($order->name)}}</a></td>
					<td><a href="/orders/view/{{$order->id}}">{{strtoupper($order->document)}}</a></td>
					<td><a href="/orders/view/{{$order->id}}">{{$order->origin}}</a></td>
					<td><a href="/orders/view/{{$order->id}}">R$ {{number_format($order->total,2,',','')}}</a></td>
					<td><a href="/orders/view/{{$order->id}}">R$ {{number_format($order->freight,2,',','')}}</a></td>
					<td><a href="/orders/view/{{$order->id}}">
						@if(isset($order->number))
							{{$order->number}}
						@endif
					</a></td>
					<td><a href="/orders/view/{{$order->id}}">
						@if(isset($order->dtFatura))
							{{date('d/m/Y', strtotime($order->dtFatura))}}
						@endif
					</a></td>
					<td><a href="/orders/view/{{$order->id}}">

						 @if(isset($order->shipping_code))
	                      {{$order->shipping_code}}
	                    @endif
					</a></td>
					<td><a href="/orders/view/{{$order->id}}">{{$order->status}}</a></td>
                </tr>
                @endforeach
                </tbody>
             </table>
             <div class="col-md-12">

				<ul class="pagination">

				   <li class="paginate_button previous
				   @if($orders->currentPage() == 1)
				   disabled
				   @endif
				   " id="example2_previous"><a href="{{$orders->previousPageUrl()}}" aria-controls="example2" data-dt-idx="0" tabindex="0">Previous</a></li>



				   <li class="paginate_button next
				   @if($orders->currentPage() == $orders->lastPage())
				   disabled
				   @endif
				   " id="example2_next">
				   	<a href="{{$orders->nextPageUrl()}}" aria-controls="example2" data-dt-idx="7" tabindex="0">Next</a>
				   </li>
				</ul>
			</div>
            </div>

        </div>

	</div>

@stop
