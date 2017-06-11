@extends('adminlte::page')

@section('title', 'Relatório de estoque interno')

@section('extra_css')
<link href="/css/easy-autocomplete.min.css" type="text/css" rel="stylesheet">
<link href="/css/easy-autocomplete.themes.min.css" type="text/css" rel="stylesheet">
@stop

@section('content')
    <div class="col-md-12">
        {!! csrf_field() !!}
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Filtros</h3>
        </div>
        <div  class="box-body table-responsive">
          <div class="col-md-2 no-padding">
            <div class="form-group">
              <label for="exampleInputEmail1">Buscar Por</label>
              <select class="form-control" name="tipo">
                  <option value="sku">SKU</option>
                  <option value="fornecedor">Fornecedor</option>
                  <option value="data">Data</option>
                  <option value="nfe">Nota Fiscal</option>
                </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="exampleInputEmail1">Termo</label>
              <input type="text" class="form-control" name="termo" placeholder="Busque o produto por nome ou sku">
            </div>
          </div>
          <div class="col-md-1">
            <label for="exampleInputEmail1">...</label>
            <button type="button" class="btn btn-block btn-success" onclick="stockSeach()">IR</button>
          </div>
          <div class="col-md-2">

          </div>
          <div class="col-md-3">
             <div class="form-group">
              <label for="exampleInputEmail1">Exportar Relatório</label>
              <a href="/stock/export" class="btn btn-block btn-primary">Exportar</a>
            </div>
          </div>
        </div>
    </div>
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Produtos</h3>
      </div>
      <div  class="box-body table-responsive no-padding">
        <div class="col-md-12 table-responsive response" >
        @if(!$stocks)
          <h3>Nenhum Localizado</h3>
        @else
          <table id="stockDataTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Data</th>
                <th>SKU</th>
                <th>Nome</th>
                <th>Fornecedor</th>
                <th>Entradas</th>
                <th>Saidas</th>
                <th>Origem</th>
                <th>Saldo</th>
              </tr>
            </thead>

          </table>
          @endif
        </div>
      </div>
      <div class="box-footer">
      </div>
    </div>
</div>

@stop

@section('extra_js')
  <script>
  var table;
  function stockSeach(){
    var tipo = $('select[name=tipo]').val();
    var termo = $('input[name=termo]').val();

    var url = '/stock/report/'+tipo+'/'+termo;
    table = $('#stockDataTable').DataTable( {
        "ajax": url,
        /*"columnDefs": [
        {
            targets: 0,
            render: function ( data, type, row, meta ) {
                if(type === 'display'){
                    data = '<a href="basic.php?game=' + encodeURIComponent(data) + '">' + data + '</a>';
                }

                return data;
            }
        }
    ]*/
    } );
  }
  </script>
@stop
