@extends('adminlte::page')

@section('title', 'Ações massivas em produtos')

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
<div class="col-md-4">

	<div class="box box-success">
		<div class="box-header with-border">
			<h3 class="box-title">Carregar Produtos!</h3>
		</div>
		<form action="/products/import/products" method="post" enctype="multipart/form-data">
		{!! csrf_field() !!}
			<div class="box-body">
				<p>Para baixar o modelo <a href="/workers/produtos.csv">clique aqui</a></p>
				<div class="form-group">
	              <label for="products">Selecionar arquivo</label>
	              <input type="file" id="products" name="products" />
	            </div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary">Enviar</button>
			</div>
		</form>
	</div>
</div>
<div class="col-md-4">
	<div class="box box-warning">
		<div class="box-header with-border">
			<h3 class="box-title">Atualizar Estoques</h3>
		</div>
		<form action="/products/import/stocks" method="post" enctype="multipart/form-data">
		{!! csrf_field() !!}
			<div class="box-body">
				<p>Para baixar o modelo <a href="/workers/estoques.xlsx">clique aqui</a></p>
				<div class="form-group">
	              <label for="stocks">Selecionar arquivo</label>
	              <input type="file" id="stocks" name="stocks" />
	            </div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary">Enviar</button>
			</div>
		</form>
	</div>
</div>
<div class="col-md-4">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Atualizar Preços</h3>
		</div>
		<form action="/products/import/prices" method="post" enctype="multipart/form-data">
		{!! csrf_field() !!}
			<div class="box-body">
				<p>Para baixar o modelo <a href="/workers/prices.xlsx">clique aqui</a></p>
				<div class="form-group">
	              <label for="prices">Selecionar arquivo</label>
	              <input type="file" id="prices" name="prices" />
	            </div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary">Enviar</button>
			</div>
		</form>
	</div>
</div>
@stop
