@extends('adminlte::page')

@section('title', 'Entrada de notas Fiscais')

@section('content_header')
    <h1>Entrada de notas Fiscais</h1>
@stop

@section('content')

<div class="col-md-12 messagebox" style="display: none;">
    <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <p class="message"></p>
    </div>
</div>
@if(session('error'))
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <p>{{session('error')}}</p>
    </div>
</div>
@endif
<div class="col-md-12">

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Procurar NFF</h3>
        </div>
        <form action="/nff/in" method="post">
        {!! csrf_field() !!}
            <div class="box-body">
                <div class="col-md-3 nopadding">
                    <div class="form-group">
                        <label for="nff">Número</label>
                        <input type="text" class="form-control" id="nff" name="nff" value = "{{$in['nff'] ?? ''}}" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="serie">serie</label>
                      <input type="text" class="form-control" id="nff" name="serie" value ="{{$in['serie'] ?? ''}}"/>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>
    @if(isset($in))
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Procurar NFF</h3>
        </div>
        <div class="box-body">
            <div class="col-md-3 nopadding">
                <table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>EAN</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($in['items'] as $item)
                <tr>
                    <td>
                        <div class="form-group">
                            <input type="text" style="width:150px;" class="sku form-control" id="sku[]" name="sku" value = "{{$item['sku'] ?? ''}}" />
                        </div>
                    </td>



                    <td>
                        <input type="text" style="width:180px;" class="form-control" id="ean[]" name="ean" value = "{{$item['ean'] ?? ''}}" />
                    </td>
                    <td>
                        <input type="text" style="width:400px;" class="form-control" id="name[]" name="name" value = "{{$item['name'] ?? ''}}" />
                    </td>
                    <td>
                        <input type="text" class="form-control quantity" id="quantity" name="quantity[]" value = "{{$item['quantity'] ?? ''}}" />
                    </td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div>

        </div>
        <div class="box-footer">
                <button type="submit" class="btn btn-primary" onclick="lancarEntradas()">Lançar Entradas</button>
            </div>
    </div>
    @endif
</div>
@stop
