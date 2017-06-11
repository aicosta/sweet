@extends('adminlte::page')

@section('title')
  Visualizando pedido # {{$order->code}} # {{$order->origin}}
@stop
@section('content')
<div class="modal modal-success">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Perfeito!</h4>
              </div>
              <div class="modal-body">
                <p class="modalMessage">One fine body…</p>
              </div>
              <div class="modal-footer">
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
<section class="invoice">
      <input type="hidden" class="orderID" value="{{$order->id}}" />
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <i class="fa fa-globe"></i> {{$order->origin}}.
            @if ($order->estimated_date != '0000-00-00 00:00:00')
            <small class="pull-right">
               <span class="label label-warning">Previsão de entrega: {{date('d/m/Y', strtotime($order->estimated_date))}}</span></small>
            @else
            <small class="pull-right">
               <span class="label label-info">Previsão de entrega não definida</span></small>
            @endif
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          <address>
            <strong>Nome: </strong>{{$order->customer->name}}<br>
            <strong>CPF: </strong> {{$order->customer->document}}<br>
            <strong>RG: </strong> {{$order->customer->document2}}<br>
            <strong>CEP:</strong> {{$order->customer->zip_code}}<br>
            <strong>Endereço:</strong> {{$order->customer->address}}<br>
            <strong>Número:</strong> {{$order->customer->number}}<br>
            <strong>Complemento:</strong> {{$order->customer->complement}}<br>
            <strong>Referência:</strong> {{$order->customer->reference}}<br>
            <strong>Bairro:</strong> {{$order->customer->quarter}}<br>
            <strong>Cidade:</strong> {{$order->customer->city}}<br>
            <strong>Estado:</strong> {{$order->customer->state}}<br>
            <strong>Telefone:</strong> {{$order->customer->phone}}<br>
            <strong>Telefone:</strong> {{$order->customer->phone2}}<br>
            <strong>Email:</strong> {{$order->customer->email}}
          </address>
        </div>
        <!-- /.col -->

        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <b>Pedido #{{$order->code}}</b><br>
          <b>Data:</b> {{date('d/m/Y h:i:s', strtotime($order->created_at))}}<br>
          <b>Data Prometida:</b> {{date('d/m/Y h:i:s', strtotime($order->max_date))}}<br>
          <b>Envio:</b> {{$order->envio}}<br>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
        <h4>Status do Pedido</h4>
          <select name="status" class="changeStatus">
            @foreach($statuses as $status)
              <option value="{{$status->id}}"
              @if($order->order_statuses_id == $status->id)
              selected
              @endif
              >
              {{$status->name}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
      @if($order->invoice)
        <div class="col-xs-12 table-responsive">
          <h3>Faturamento</h3>
          <table class="table table-striped">
            <thead>
            <tr>
              <th>NFE</th>
              <th>Serie</th>
              <th>Chave</th>
              <th>Link</th>
              <th>Data</th>
            </tr>
            </thead>
            <tbody>
              <td>{{$order->invoice->number}}</td>
              <td>{{$order->invoice->serie}}</td>
              <td>{{$order->invoice->key}}</td>
              <td>{{$order->invoice->url}}</td>
              <td>{{date('d/m/Y H:i:s', strtotime($order->invoice->created_at))}}</td>
            </tbody>
          </table>
        </div>
        @else
        <div class="col-xs-12 table-responsive">
          <div class="callout callout-warning">
            <h4>Nenhuma Fatura Localizada</h4>

            <p>Até o momento, não localizamos nenhuma fatura emitida para este pedido
            <br /> <a href="#">clique aqui</a> para incluir manualmente uma Fatura</p>
          </div>

        </div>
        @endif
        @if($order->shipping)
        <div class="col-xs-12 table-responsive">
          <h3>Expedição</h3>
          <table class="table table-striped">
            <thead>
            <tr>
              <th>Rastreio</th>
              <th>PLP</th>
              <th>DataExpedição</th>
              <th>Status</th>
            </tr>
            </thead>
            <tbody>
              <td>
                <div class="form-group">
                  <input type="hidden" name="ship_id" value="{{$order->shipping->id}}" />
                  <input type="text" class="form-control" name="ship_code" value="{{$order->shipping->shipping_code}}"/>
                </div>

              </td>
              <td></td>
              <td>{{date('d/m/Y H:i:s', strtotime($order->shipping->created_at))}}</td>
              <td>{{$order->shipping->status}}</td>
            </tbody>
          </table>
        <hr />
        </div>
        @else
        <div class="col-xs-12 table-responsive">
          <div class="callout callout-info dataRastreio">
            <h4>Nenhuma Etiqueta Localizada</h4>

            <p>Até o momento, não localizamos nenhuma etiqueta emitida para este pedido
            <br /> <a href="javascript:void(0)" onclick="showPlp()">clique aqui</a> para incluir manualmente uma Etiqueta</p>
          </div>

            <div class="plphide" style="display:none;">
              <div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title">Incluir Rastreio</h3>
                </div>
                <div class="box-body">
                  <div class="row">
                    <div class="col-xs-2">
                      <input type="text" class="form-control" name="rastreio-pedido" value="{{$order->id}}" disabled>
                    </div>
                    <div class="col-xs-3">
                      <input type="text" class="form-control" name="rastreio-rastreio" placeholder="Codigo de Rastreio">
                    </div>
                   <div class="col-xs-3">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right" name="rastreio-data" id="datepicker">
                    </div>
                    </div>
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
              <div class="box-footer">
                 <button type="button" class="btn btn-success" onclick="insereRastreio()">Gravar Rastreio</button>
              </div>
            </div>
        </div>
        @endif

        <div class="col-xs-12 table-responsive">
        <div class="box box-solid box-info">
          <div class="box-header">
            <h3 class="box-title">Observações</h3>
          </div>
          <div class="box-body">

            <div class="form-group">
              <label>Comentários</label>
              <textarea class="form-control dataComment" rows="3" placeholder=""></textarea>
              <p class="help-block">Inclua os seus comentários no campo acima. Todos os comentários inseridos no pedido serão logo Abaixo deste formulário.</p>
              <button type="button" class="btn btn-block btn-success" onclick="saveComment({{$order->id}}, {{$user}}, false)">Guardar Comentário</button>
            </div>
          </div>
        </div>
        </div>
        @if(count($comments) > 0)
        <div class="col-xs-12 table-responsive">

            <ul class="timeline">
              <!-- timeline time label -->
              <li class="time-label">
                    <span class="bg-green">
                      HISTÓRICO DE OBSERVAÇÕES
                    </span>
              </li>
              <!-- /.timeline-label -->
              <!-- timeline item -->
               @foreach($comments as $comment)
              <li>
                <i class="fa fa-envelope bg-yellow"></i>

                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> {{date('d/m/Y H:i:s', strtotime($comment->created_at))}}</span>

                  <h3 class="timeline-header"><a href="#">{{$comment->user->name}}</a> Envio o comentário</h3>

                  <div class="timeline-body">
                    {!!nl2br($comment->content)!!}
                  </div>
                  <div class="timeline-footer">
                  </div>
                </div>
              </li>
              @endforeach
            </ul>

        </div>
        @endif

        <!-- /.col -->
      </div>

      <!-- /.ITEMS -->
      <div class="row">
          <div class="col-xs-12 table-responsive">
              <div class="box box-solid box-default">
                  <div class="box-header with-border">
                      <h3 class="box-title">Itens do Pedido</h3>
                  </div>
                  <div class="box-body">
                      <div class="col-xs-12 table-responsive">
                          <table class="table table-striped">
                              <thead>
                                  <tr>
                                      <th>Qty</th>
                                      <th>Produto</th>
                                      <th>Nome</th>
                                      <th>SubTotal</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach($order->items as $item)
                                  <tr>
                                      <td>{{$item->quantity}}</td>
                                      <td>{{$item->sku}}</td>
                                      <td>{{$item->name}}</td>
                                      <td>R$ {{number_format($item->price,2,',','')}}</td>
                                  </tr>
                                  @endforeach
                              </tbody>
                          </table>
                          <!-- accepted payments column -->
                          <div class="col-xs-9"></div>
                          <!-- /.col -->
                          <div class="col-xs-3">
                              <div class="table-responsive">
                                  <table class="table">
                                      <tbody>
                                          <tr>
                                              <th style="width:50%">Subtotal:</th>
                                              <td>R$ {{number_format($order->total,2,',','')}}</td>
                                          </tr>
                                          <tr>
                                              <th>Frete</th>
                                              <td>R$ {{number_format($order->freight,2,',','')}}</td>
                                          </tr>
                                          <tr>
                                              <th>Total:</th>
                                              <td>R$ {{number_format($order->total+$order->freight,2,',','')}}</td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <!-- /.ITEMS -->

      <div class="row">
        <div class="col-xs-12 table-responsive">
      <div class="box box-solid box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Registro de Atividades</h3>
          </div>
          <div class="box-body">
            <table class="table table-striped">
            <thead>
            <tr>
              <th>Data</th>
              <th>Status Anterior</th>
              <th>Novo Status</th>
              <th>Alterado por</th>
            </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{date('d/m/Y H:i:s', strtotime($order->created_at))}}</td>
                <td></td>
                <td><span class="label label-success">Pedido Incluido</span></td>
                <td>Sweet</td>
              </tr>
              @foreach($logs as $log)
                <tr>
                  <td>{{date('d/m/Y H:i:s', strtotime($log->created_at))}}</td>
                  <td><span class="label label-warning">{{$log->old_status}}</span></td>
                  <td><span class="label label-info">{{$log->new_status}}</span></td>
                  <td>{{$log->user->name}}</td>
                </tr>
              @endforeach
            </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>

      </div>
      </div>
      <!-- /.row -->

      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12">
          <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Imprimir</a>
        </div>
      </div>
    </section>
@stop


@section('adminlte_js')
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="/vendor/adminlte/dist/js/app.min.js"></script>
@endsection
