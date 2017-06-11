@extends('adminlte::page')

@section('title', 'Ações em massa')

@section('content_header')
    <h1>Ações em massa</h1>
@stop

@section('content')
@if(session('message'))
<div class="col-md-12">
    <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <p>{{session('message')}}</p>
    </div>
</div>
@endif
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
            <h3 class="box-title">Carregar Pedidos!</h3>
        </div>
        <form action="/mobly/orders" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
            <div class="box-body">

                <div class="form-group">
                  <label for="mobly">Selecionar arquivo</label>
                  <input type="file" id="mobly" name="mobly" />
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>
</div>

@stop
