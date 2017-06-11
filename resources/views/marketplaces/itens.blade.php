@extends('adminlte::page')

@section('title', 'Items Por Marketplace')
@section('content_header')
    <h1>Ações em massa</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Selecione o marketplace</h3>
        </div>
        {!! csrf_field() !!}
            <div class="box-body">
                <div class="col-md-3">
                    <div class="form-group">
                      <select class="form-control marketplace" onchange="buscarProdutos();">
                        <option>Selecione o marketplace</option>
                        <option value="ml">Mercado Livre</option>
                      </select>
                    </div>
                </div>
            </div>
            <div class="box-footer"></div>
    </div>
</div>
<div class="col-md-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Resultados da pesquisa</h3>
        </div>
        <div class="box-body">
            <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>SKU EXTERNO</th>
                </tr>
            </thead>
            </table>
        </div>
    </div>

</div>
@stop

@section('extra_js')
<script type="text/javascript">
function buscarProdutos(){
    var marketplace = $(".marketplace").val();
    $('#example').DataTable( {
        "language": {
            "lengthMenu": "Mostrar _MENU_ por página",
            "zeroRecords": "Carregando registros...",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "Carregando registros...",
            "infoFiltered": "(filtered from _MAX_ total records)"
        },
        "lengthMenu": [[50, 75, 100], [50, 75, 100]],
        "ajax": '/ml/ajax/itens'
    });

}
</script>
@stop
