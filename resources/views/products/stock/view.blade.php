@extends('adminlte::page')

@section('title')
  Lista de estoques do produto #{{$product->sku}} # {{$product->name}}
@stop
@section('content')
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Estoque Interno</h3>
      </div>
      <div  class="box-body table-responsive no-padding">
        <div class="col-md-12">
        <br />
        </div>
        <div class="col-md-12 table-responsive response" >
          <div class="well">
              <h4>#{{$product->sku}} - {{$product->name}}</h4>
              <p>
              <strong>Estoque Interno:</strong> {{$stockData['internal']}}<br />
              <strong>Estoque do Fornecedor:</strong> {{$stockData['external']}}<br />
              <strong>Estoques Somados:</strong> {{$stockData['total']}}
              </p>
          </div>
          @if(count($stocks) == 0 || !$stocks)
            <div class="callout callout-danger lead">
              <h4>Nenhuma Entrada Localizada!</h4>
              <p>Até o momento, não foram emitidas notas de entrada/saida para este produto</p>
            </div>
          @else
          <h3>Histórico de Entradas e Saídas</h3>
          <table id="invoiceDataTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Data</th>
                <th>Entradas</th>
                <th>Saidas</th>
                <th>Saldo no dia</th>
                <th>Observações</th>
                <th>Origem</th>
              </tr>
            </thead>
            <tbody>
              @foreach($stocks as $stock)
                <tr>
                  <td>{{$stock['created_at']}}</td>
                  <td>{{$stock['ins']}}</td>
                  <td>{{$stock['outs']}}</td>
                  <td>{{$stock['balance']}}</td>
                  <td>{{$stock['observation']}}</td>
                  <td>{{$stock['origin']}}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
          @endif
        </div>
        <div class="col-md-12 table-responsive response">
        @if($logs)
        <h3>Histórico de Atualizações externas</h3>
          <table id="invoiceDataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                  <th>Atualizado Em</th>
                  <th>Atualizado Por</th>
                  <th>Atualizado Para</th>
                  <th>Origem</th>
                </tr>
              </thead>
              <tbody>
                @foreach($logs as $log)
                <tr>
                  <td>{{$log->created_at}}</td>
                  <td>{{$log->user->name}}</td>
                  <td>{{$log->qty}}</td>
                  <td>{{$log->origin}}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
        @else
          <div class="callout callout-danger lead">
              <h4>Nenhuma Histórico Localizado!</h4>
              <p>Até o momento, não foram Realizadas Alterações neste produto</p>
            </div>
        @endif
        </div>
      </div>
    </div>
</div>

@stop

