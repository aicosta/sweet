@extends('adminlte::page')

@section('title', 'Criando Produto')



@section('content')
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Novo Produto</h3>
            </div>
		
			<form role="form" method="post" action="/products/create">
			{!! csrf_field() !!}
				<div class="box-body">
					<div class="col-md-4">
						<div class="form-group">
							<label for="sku">SKU</label>
							<input type="text" class="form-control" name="sku" placeholder="SKU comercial do produto">
						</div>
					</div>
					<div class="col-md-8">
						<div class="form-group">
							<label for="exampleInputPassword1">Nome</label>
							<input type="password" class="form-control" name="name" placeholder="Nome comercial do produto">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
		                	<label>Descrição Resumida</label>
		                	<textarea name="short_description" class="form-control" id="short_description" rows="3" placeholder="Uma breve informação sobre o produto"></textarea>
		                </div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
		                	<label>Descrição</label>
		                	<textarea name="description" id="description" class="form-control" rows="3" placeholder="Descrição principal do produto"></textarea>
		                </div>
					</div>
					<div class="col-sm-3 col-sm-3">
						<div class="form-group">
							<label>Fornecedor</label>
							<select name="provider" class="form-control">
								<option value="">Selecione um Fornecedor</option>
								@foreach($providers as $provider)
									<option value="{{$provider->id}}">
										{{$provider->name}}
									</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="brand">Marca</label>
							<input type="text" class="form-control" name="brand" placeholder="Marca do produto">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="lead_time">Lead Time</label>
							<input type="text" class="form-control" name="lead_time" placeholder="Tempo extra de entrega">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="stock[quantity]">Estoque do Fornecedor</label>
							<input type="text" class="form-control" name="stock[quantity]" placeholder="Estoque Informado pelo fornecedor">
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
									<input type="text" class="form-control" name="dimensions[weight]" placeholder="Peso do produto">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[width]">Altura</label>
									<input type="text" class="form-control" name="dimensions[width]" placeholder="Altura da caixa">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[height]">Largura</label>
									<input type="text" class="form-control" name="dimensions[height]" placeholder="Largura da caixa">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[depth]">Profundidade</label>
									<input type="text" class="form-control" name="dimensions[depth]" placeholder="Profundidade da caixa">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="dimensions[cube]">Cubagem</label>
									<input type="text" class="form-control" name="dimensions[cube]" placeholder="Peso do produto">
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
									<input type="text" class="form-control" name="fiscals[sku]" placeholder="SKU que será utilizado nas notas fiscais">
								</div>
							</div>
							<div class="col-md-9">
								<div class="form-group">
									<label for="fiscals[name]">Nome</label>
									<input type="text" class="form-control" name="fiscals[name]" placeholder="Nome do produto que está na nota fiscal emitida pelo fornecedor">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fiscals[ean]">EAN</label>
									<input type="text" class="form-control" name="fiscals[ean]" placeholder="Tempo extra de entrega">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fiscals[isbn]">ISBN</label>
									<input type="text" class="form-control" name="fiscals[isbn]" placeholder="Apenas para livros">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fiscals[ncm]">NCM</label>
									<input type="text" class="form-control" name="fiscals[ncm]" placeholder="Classificação fiscal do produto">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Origem</label>
									<select name="provider" class="form-control">
										<option value="">Selecione a origem do produto</option>
										@foreach($origins as $origin)
											<option value="{{$origin->code}}">
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
                        	<p></p>
                        	<div style="clear:both;">
                    	</div>
                    </div>
                    <div class="col-md-12 nopadding">
                    	<div class="well">
                        	<h4>Atributos</h4>
                        	<p>Devido a restrições em algums marketplaces, algums atributos podem não funcionar no momento do envio.</p>
                        	<div class="col-md-11">
                        		
                        		<a href="javascript:void(0);" class="btn btn-success" onclick="addAttribute()">+</a>
                        	</div>

                        	<div class="col-sm-6">
                        		<div class="form-group">
									<label for="attribute[name][0]">Nome</label>
									<input type="text" class="form-control" name="attribute[name][0]" placeholder="Nome do Atributo" value="Altura" disabled="disabled">
								</div>
                        	</div>
                        	<div class="col-md-5">
                        		<div class="form-group">
									<label for="attribute[value][0]">Valor</label>
									<input type="text" class="form-control" name="attribute[value][0]" placeholder="Altura do produto sem embalagem">
								</div>
                        	</div>
                        	<div class="col-md-1">
                        		<div class="form-group">
									<label>Remover</label>
									<a href="javascript:void(0);" class="btn btn-danger" onclick="removeAttribute(1)">-</a>
								</div>
                        	</div>
                        	<div class="col-sm-6">
                        		<div class="form-group">
									<input type="text" class="form-control" name="attribute[name][1]" placeholder="Nome do Atributo" value="Largura" disabled="disabled">
								</div>
                        	</div>
                        	<div class="col-md-5">
                        		<div class="form-group">
									<input type="text" class="form-control" name="attribute[value][1]" placeholder="Largura do produto sem embalagem">
								</div>
                        	</div>
                        	<div class="col-md-1">
                        		<div class="form-group">

								</div>
                        	</div>
                        	<div class="col-sm-6">
                        		<div class="form-group">
									<input type="text" class="form-control" name="attribute[name][1]" placeholder="Nome do Atributo" value="Comprimento" disabled="disabled">
								</div>
                        	</div>
                        	<div class="col-md-5">
                        		<div class="form-group">
									<input type="text" class="form-control" name="attribute[value][1]" placeholder="Comprimento do produto sem embalagem">
								</div>
                        	</div>
                        	<div class="col-md-1">
                        		<div class="form-group">

								</div>
                        	</div>
                        	<div class="col-sm-6">
                        		<div class="form-group">
									<input type="text" class="form-control" name="attribute[name][1]" placeholder="Nome do Atributo" value="Cor" disabled="disabled">
								</div>
                        	</div>
                        	<div class="col-md-5">
                        		<div class="form-group">
									<input type="text" class="form-control" name="attribute[value][1]" placeholder="Cor do produto">
								</div>
                        	</div>
                        	<div class="col-md-1">
                        		<div class="form-group">

								</div>
                        	</div>
                        	<div class="attributes">
                        		
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