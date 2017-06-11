@extends('adminlte::page')

@section('title', 'Busca e lista de produtos')



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
<form action="/products" method="get">
    <div class="col-md-12 productReports" style="display: none;">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Relatórios</h3>
        </div>
        <div class="box-body">
            <a href="/products/fullReport" class="btn btn-default">Tabela de Preços / Estoques</a>
        </div>
    </div>
    </div>
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Lista de Produtos</h3>
                 <div class="box-tools">
                 <button type="button" class="btn btn-default showReports">Relatórios</button>
                 </div>
				 <div class="box-tools" style="margin:  1px 92px 0 0">

	                <div class="input-group input-group-sm" style="width: 150px;">
	                  <input type="text" name="search" class="form-control pull-right" placeholder="Buscar" value="{{$search}}">

	                  <div class="input-group-btn">
	                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
	                  </div>

	                </div>

	              </div>
            </div>
</form>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Imagem</th>
                  <th>SKU</th>
                  <th>Nome</th>
                  <th>Valor Base</th>
                  <th>Fornecedor</th>
                  <th>Estoque</th>
                  <th>Status</th>
                </tr>
                @foreach($products as $product)
                <tr>
                	<td><img src="{{$product->images[0]->url}}" width="50px"/></td>
					<td><a href="/products/edit/{{$product->id}}">{{$product->sku}}</a></td>
					<td>{{$product->name}}</td>
					<td>
						<a href="#">
							1 - R$ {{@number_format($product->prices['price'],2,',','')}}<br />
              2 - R$ {{@number_format($product->prices['price2'],2,',','')}}<br />
              3 - R$ {{@number_format($product->prices['price3'],2,',','')}}
						</a>
					</td>
					<td>{{$product->providers->name}}</td>
					<td>
                        <a href="https://fornecedores.fullhub.com.br/stock/view/{{$product->id}}">
                        {{$product->stocks->quantity}}
                        </a>
                    </td>
					<td>
						@if($product->status)
							<span class="label label-success">Ativo</span>
						@else
							<span class="label label-danger">Inativo</span>
						@endif
					</td>
                </tr>
                @endforeach
             </table>
             <div class="col-md-12">

				<ul class="pagination">

				   <li class="paginate_button previous
				   @if($products->currentPage() == 1)
				   disabled
				   @endif
				   " id="example2_previous"><a href="{{$products->previousPageUrl()}}" aria-controls="example2" data-dt-idx="0" tabindex="0">Previous</a></li>



				   <li class="paginate_button next
				   @if($products->currentPage() == $products->lastPage())
				   disabled
				   @endif
				   " id="example2_next">
				   	<a href="{{$products->nextPageUrl()}}" aria-controls="example2" data-dt-idx="7" tabindex="0">Next</a>
				   </li>
				</ul>
			</div>
            </div>

        </div>

	</div>

@stop
