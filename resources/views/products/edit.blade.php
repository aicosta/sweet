@extends('adminlte::page')

@section('title', 'Editando Produto')



@section('content')
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Novo Produto</h3>
            </div>
            @if(session('message'))
			<div class="col-md-12">
				<div class="callout callout-success">
		          <p>{{session('message')}}</p>
		        </div>
			</div>
			@endif
			@if(session('error'))
			<div class="col-md-12">
				<div class="callout callout-danger">
		          <p>{{session('error')}}</p>
		        </div>
			</div>
			@endif
			<form role="form" method="post">
			{!! csrf_field() !!}
				<div class="box-body">
					<div class="col-md-4">
						<div class="form-group">
							<label for="sku">SKU</label>
							<input type="text" class="form-control" name="sku" placeholder="SKU comercial do produto" disabled="disabled" value="{{$product->sku}}">
						</div>
					</div>
					<div class="col-md-8">
						<div class="form-group">
							<label for="exampleInputPassword1">Nome</label>
							<input type="text" class="form-control" name="name" placeholder="Nome comercial do produto" value="{{$product->name}}">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
		                	<label>Descrição Resumida</label>
		                	<textarea name="short_description" class="form-control" id="short_description" rows="3" placeholder="Uma breve informação sobre o produto">
		                		{{$product->short_description}}
		                	</textarea>
		                </div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
		                	<label>Descrição</label>
		                	<textarea name="description" id="description" class="form-control" rows="3" placeholder="Descrição principal do produto">
		                		{{$product->description}}
		                	</textarea>
		                </div>
					</div>
					<div class="col-sm-3 col-sm-3">
						<div class="form-group">
							<label>Fornecedor</label>
							<select name="provider" class="form-control">
								<option value="">Selecione um Fornecedor</option>
								@foreach($providers as $provider)
									<option value="{{$provider->id}}"
									@if($product->providers_id == $provider->id)
									selected
									@endif
									>
										{{$provider->name}}
									</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="brand">Marca</label>
							<input type="text" class="form-control" name="brand" placeholder="Marca do produto"
							value="{{$product->brand}}">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="lead_time">Lead Time</label>
							<input type="text" class="form-control" name="lead_time" placeholder="Tempo extra de entrega" value="{{$product->lead_time}}">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="stock[quantity]">Estoque do Fornecedor</label>
							<input type="text" class="form-control" name="stock[quantity]" placeholder="Estoque Informado pelo fornecedor" value="{{$product->stocks->quantity}}">
						</div>
					</div>
					<div class="col-md-12 nopadding">
						<div class="well">
	                        <h4>Dimensões</h4>
	                        <p>As dimensões informadas serão utilizadas para calcular o valor de envio do produto, então coloque as dimensões do produto com embalagem.<br />
							Insira as dimensões reais do produtos em seus atributos.</p>
	                        <div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[weight]">Peso</label>
									<input type="text" class="form-control" name="dimensions[weight]" placeholder="Peso do produto" value="{{$product->dimensions->weight}}">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[width]">Altura</label>
									<input type="text" class="form-control" name="dimensions[width]" placeholder="Altura da caixa" value="{{$product->dimensions->width}}">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[height]">Largura</label>
									<input type="text" class="form-control" name="dimensions[height]" placeholder="Largura da caixa" value="{{$product->dimensions->height}}">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[depth]">Profundidade</label>
									<input type="text" class="form-control" name="dimensions[depth]" placeholder="Profundidade da caixa" value="{{$product->dimensions->depth}}">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[cube]">Cubagem</label>
									<input type="text" class="form-control" name="dimensions[cube]" placeholder="Peso do produto" value="{{$product->dimensions->cube}}">
								</div>
							</div>
	                        <div style="clear:both;">
	                    </div>
                    </div>
					<div class="col-md-12 nopadding">
						<div class="well">
	                        <h4>Dados Fiscais</h4>
	                        <p>As informações abaixo são de extrema importancia para a emissão da nota do produto, por esse motivo, os dados são obrigatórios.</p>
	                        <div class="col-md-3">
								<div class="form-group">
									<label for="fiscals[sku]">SKU</label>
									<input type="text" class="form-control" name="fiscals[sku]" placeholder="SKU que será utilizado nas notas fiscais" value="{{$product->fiscals->sku}}">
								</div>
							</div>
							<div class="col-md-9">
								<div class="form-group">
									<label for="fiscals[name]">Nome</label>
									<input type="text" class="form-control" name="fiscals[name]" placeholder="Nome do produto que está na nota fiscal emitida pelo fornecedor" value="{{$product->fiscals->name}}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fiscals[ean]">EAN</label>
									<input type="text" class="form-control" name="fiscals[ean]" placeholder="Tempo extra de entrega" value="{{$product->fiscals->ean}}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fiscals[isbn]">ISBN</label>
									<input type="text" class="form-control" name="fiscals[isbn]" placeholder="Apenas para livros" value="{{$product->fiscals->isbn}}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fiscals[ncm]">NCM</label>
									<input type="text" class="form-control" name="fiscals[ncm]" placeholder="Classificação fiscal do produto" value="{{$product->fiscals->ncm}}">
								</div>
							</div>
							<div class="col-md-3">

								<div class="form-group">
									<label>Origem</label>
									<select name="provider" class="form-control">
										<option value="">Selecione a origem do produto</option>
										@foreach($origins as $origin)
											<option value="{{$origin->code}}"
											@if($product->fiscals->origin == $origin->code)
											selected
											@endif
											>
												{{$origin->code}} - {{$origin->name}}
											</option>
										@endforeach
									</select>
								</div>
							</div>
							<div style="clear:both;">
	                    </div>
	                    
                    </div>
                    <div class="col-md-12 nopadding">
                    	<div class="well">
                        	<h4>Imagems</h4>
                        	
                        	@foreach($product->images as $image)
                        		<p>
                        			<img src="{{$image->url}}" width="150px" />
                        		</p>
                        	@endforeach
                        	
                        	<div style="clear:both;">
                    	</div>
                    </div>
                    <div class="col-md-12 nopadding">
                    	<div class="well">
                        	<h4>Atributos</h4>
                        	<p>Devido a restrições em algums marketplaces, algums atributos podem não funcionar no momento do envio.</p>
                        	<div class="col-md-11">
                        		<a href="javascript:void(0);" onclick="addAttribute()">+</a>
                        	</div>

                        	<div class="col-md-6">
                        		<div class="form-group">
									<label for="attribute[name][0]">Nome</label>
									<input type="text" class="form-control" name="attribute[name][0]" placeholder="Nome do Atributo">
								</div>
                        	</div>
                        	<div class="col-md-5">
                        		<div class="form-group">
									<label for="attribute[value][0]">Nome</label>
									<input type="text" class="form-control" name="attribute[value][0]" placeholder="Nome do Atributo">
								</div>
                        	</div>
                        	<div class="col-md-1">
                        		<div class="form-group">
									<label>Remover</label>
									<button type="submit" class="btn btn-danger">-</button>
								</div>
                        	</div>
                        	<div style="clear:both;">
                    	</div>
                    </div>
                    

				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
				
			</form>

		</div>
	</div>



@stop

@section('js')
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
	<script>
	$(document).ready(function(){
	    CKEDITOR.replace('short_description');
	    CKEDITOR.replace('description');
	});
	</script>
@stop