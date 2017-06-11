@extends('adminlte::page')

@section('title','Criando Entrada ou saida do estoques')

@section('extra_css')
<link href="/css/easy-autocomplete.min.css" type="text/css" rel="stylesheet">
<link href="/css/easy-autocomplete.themes.min.css" type="text/css" rel="stylesheet">
@stop

@section('content')
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Entrada de produtos</h3>
      </div>
      <div  class="box-body table-responsive no-padding">
        <div class="col-md-12">
          @if (session('status'))
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Retorno</h4>
            {{ session('status') }}
          </div>
          @endif

        <br />
        </div>
        <form method="post" action="/stock/insert">
        {!! csrf_field() !!}
        <div class="col-md-12 table-responsive response" >
          <div class="col-md-12 no-padding">
            <div class="form-group">
              <label for="exampleInputEmail1">Buscar Produto</label>
              <input type="text" class="form-control" id="product_autocomplete" name="produto" placeholder="Busque o produto por nome ou sku">
            </div>
          </div>
          <div class="col-md-3 no-padding">
            <div class="form-group">
              <label for="exampleInputEmail1">Entradas</label>
              <input type="text" class="form-control" name="ins" value="0">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="exampleInputEmail1">Saídas</label>
             <input type="text" class="form-control" name="outs" value="0">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="exampleInputEmail1">Observações</label>
             <input type="text" class="form-control" name="observation" placeholder="observações extras">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="exampleInputEmail1">Origem</label>
             <input type="text" class="form-control" name="origin" placeholder="Origem">
            </div>
          </div>
        </div>
      </div>
      <div class="box-footer">
        <button type="submit" class="btn btn-block btn-success">Inserir</button>
      </div>
      </form>
    </div>
</div>

@stop

@section('extra_js')
  <script src="/js/jquery.easy-autocomplete.min.js"></script>
  <script>
    $(document).ready(function(){
    var options = {

      url: function(phrase) {

        return "/products/autocomplete/"+phrase;
      },

      getValue: function(element) {
        return element.name;
      },

      ajaxSettings: {
        dataType: "json",
        method: "GET",
        data: {
          dataType: "json"
        }
      },
      list: {
        match: {
          enabled: true
        }
      },

      preparePostData: function(data) {
        data.phrase = $("#product_autocomplete").val();

        return data;
      },
      theme: "plate-dark",

      requestDelay: 40
    };

    $("#product_autocomplete").easyAutocomplete(options);
    });
  </script>
@stop
