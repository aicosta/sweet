@extends('adminlte::page')

@section('title', 'Lista de Produtos')

@section('content')
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Lista de Pedidos</h3>
				 <div class="box-tools">
	                <div class="input-group input-group-sm" style="width: 150px;">
	                  <div class="input-group-btn">
	                  </div>
	                </div>
	                </form>
	              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>NFE</th>
                  <th>Codigo</th>
                  <th>Origem</th>
                  <th>Fornecedor</th>
                  <th>SKU</th>
                  <th>Qty</th>
                </tr>
                 @foreach($orders as $order)
                <tr>
					<td>
					@if(isset($order->invoice->number))
						{{$order->invoice->number}}
					@else
						Não Faturado
					@endif
					</td>
					<td>
						{{$order->code}}
					</td>
					<td>
						{{$order->origin}}
					</td>
					<td>
						fornecedor
					</td>
					<td>
					@if(isset($order->items))
						@foreach($order->items as $item)
							{{$item->sku}}<br />
						@endforeach
					@endif
					</td>
					<td>
					@if(isset($order->items))
					@foreach($order->items as $item)
						{{$item->quantity}}<br />
					
					@endforeach
					@endif
					</td>
                </tr>
                @endforeach
             </table>
             <div class="col-md-12">

				
			</div>
            </div>
            
        </div>

	</div>

@stop