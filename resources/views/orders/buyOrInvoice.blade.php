@extends('adminlte::page')

@section('title', 'Comprar ou Faturar?')

@section('content')
  <div class="col-md-12">


    {!! csrf_field() !!}
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Compras ou faturas</h3>
      </div>

            <div  class="box-body table-responsive no-padding">
            <div class="col-md-12">
            <div class="form-group">
              <label>Fornecedor</label>
              <select class="form-control buyorsell-provider">
                <option value="0">Todos</option>
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
              <a class="btn btn-app" onclick="sendToBuy()">

                <i class="fa fa-shopping-cart"></i> Aguardando Produto
              </a>
              <a class="btn btn-app" onclick="sendToInvoice()">

                <i class="fa fa-inbox"></i> Aguardando Faturamento
              </a>
              <table id="invoiceDataTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th><input type="checkbox" class="checkAll"></th>
                    <th>Data</th>
                    <th>Pedido</th>
                    <th>Origem</th>
                    <th>Estado</th>
                    <th>p.ID</th>
                    <th>Sku's</th>
                    <th>Nome's</th>
                    <th>Qty</th>
                    <th>Custo</th>
                    <th>E. In</th>
                    <th>E. Ex</th>
                    <th>Fornecedor</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($orders as $order)
                  <tr>
                    <td><input type="checkbox" name="order[]" value="{{$order['oid']}}"></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{date('d/m/Y H:i:s', strtotime($order['date']))}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['code']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['origin']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['estado']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['pid']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['psku']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['pname']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['quantity']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{number_format($order['cost'],2,'.','')}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['estoqueInterno']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['estoqueExterno']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['fornecedor']}}<a/></td>
                    <td><a href="/orders/view/{{$order['oid']}}">{{$order['ostatus']}}<a/></td>
                  </tr>
                  @endforeach
                </tbody>
             </table>

            </div>

        </div>

  </div>

@stop
