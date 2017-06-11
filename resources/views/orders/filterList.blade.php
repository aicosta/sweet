@extends('adminlte::page')

@section('title', 'Pedidos por Status')
@section ('extra_css')
<link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="/plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="/plugins/timepicker/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="/plugins/select2/select2.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
@stop
@section('content')
    <div class="col-md-12">
        {!! csrf_field() !!}
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Filtros</h3>
        </div>
        <div  class="box-body table-responsive no-padding">

          <div class="col-md-3">
            <div class="form-group">
              <label>Período:</label>
              <div class="input-group">
                <button type="button" class="btn btn-default pull-right" id="daterange-btn">
                  <span>
                    <i class="fa fa-calendar"></i> Selecionar Período
                  </span>
                  <i class="fa fa-caret-down"></i>
                </button>
              </div>
            </div>
            <div class="form-group">

              <select name="origin" class="form-control selectOrigin" multiple="multiple" data-placeholder="Selecione a origem" style="width: 100%;">
                @foreach($origins as $origin)
                <option value="{{$origin->name}}">{{strtoupper($origin->name)}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">

              <select name="status" class="form-control selectStatus" multiple="multiple" data-placeholder="Selecione o status" style="width: 100%;">
                @foreach($statuses as $status)
                <option value="{{$status->id}}">{{strtoupper($status->name)}}</option>
                @endforeach

              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="export" style="display: none;">
              <button type="button" class="btn btn-block btn-default" onclick="orderExport();">Exportar Reátório</button>
            </div>
          </div>
        </div>
    </div>
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Pedidos</h3>
      </div>
      <div  class="box-body table-responsive no-padding">
        <div class="col-md-12 table-responsive response" >

        </div>
      </div>
    </div>
</div>

@stop



@section ('extra_js')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
 <script src="/plugins/daterangepicker/daterangepicker.js"></script>
 <script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
 <script src="/plugins/select2/select2.full.min.js"></script>
 <script>
    var date = false;
    var origin = false;
    var status = false;
    $(function () {
      $('#daterange-btn').daterangepicker(
        {
          opens: "right",
          locale: {
          "format": "DD/MM/YYYY",
          "separator": " - ",
          "applyLabel": "Aplicar",
          "cancelLabel": "Cancelar",
          "fromLabel": "De",
          "toLabel": "Até",
          "customRangeLabel": "Personalizado",
          "weekLabel": "W",
          "daysOfWeek": [
              "Dom",
              "Seg",
              "Ter",
              "Qua",
              "Qui",
              "Sex",
              "Sab"
          ],
          "monthNames": [
              "Janeiro",
              "Fevereiro",
              "Março",
              "Abril",
              "Maio",
              "Junho",
              "Julho",
              "Agosto",
              "Setembro",
              "Outubro",
              "Novembro",
              "Dezembro"
          ],
          "firstDay": 1
      },
          ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Este Ano': [moment().subtract(1, 'year').startOf('month'), moment().endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()

        },
        function (start, end) {
          $('#daterange-btn span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
          date = '["'+start.format('DD/MM/YYYY')+'","'+end.format('DD/MM/YYYY')+'"]';
          $('.export').fadeIn();
          sendRequest();
        }
      );
      $('.selectOrigin').select2({
        maximumSelectionLength:1,
        allowClear: true
      });
      $(".selectStatus").select2({
        maximumSelectionLength:1,
        allowClear: true
      });
      $('.selectOrigin').change(function(){
        origin = $('.selectOrigin').val();
        $('.export').fadeIn();
        sendRequest();
      });
      $('.selectStatus').change(function(){
        status = $('.selectStatus').val();
        $('.export').fadeIn();
        sendRequest();
      });
    });
    function orderExport(){
      var url = '/orders/export?s=0';
      if(date){
        url+='&date='+date;
      }
      if(origin){
        origin = origin.toString();
        origin = origin.split(",")
        var originData = JSON.stringify(origin);

        url+='&origin='+originData;
      }
      if(status != 'false'){
        status = status.toString();
        status = status.split(",");
         var statusData = JSON.stringify(status);
         url+='&status=['+statusData+']';
      }
      window.open(url, 'name');
    }
    function sendRequest(){
      var url = '/orders/filter?s=0';
      if(date){
        url+='&date='+date;
      }
      if(origin){
        origin = origin.toString();
        origin = origin.split(",")
        var originData = JSON.stringify(origin);

        url+='&origin='+originData;
      }
      if(status != 'false'){
        status = status.toString();
        status = status.split(",");
         var statusData = JSON.stringify(status);
         url+='&status=['+statusData+']';
      }
      var response = '';
      $.getJSON(url, { get_param: 'value' }, function( e ) {
        response += '<div class="callout callout-info">\
                      <p style="font-size:14px">\
                        '+e.total+' Pedido(s) Localizado<br /> \
                        '+e.lastPage+' página(s) \
                        \
                      </p>\
                    </div>\
                    <table id="invoiceDataTable" class="table table-bordered table-striped">\
                <thead>\
                  <tr>\
                    <th></th>\
                    <th>Data</th>\
                    <th>Pedido</th>\
                    <th>Origem</th>\
                    <th>Estado</th>\
                    <th>Sku</th>\
                    <th>Nome</th>\
                    <th>Qty</th>\
                    <th>Status</th>\
                    <th>NFE</th>\
                    <th>Data Nfe</th>\
                    <th>Rastreio</th>\
                    <th>Fornecedor</th>\
                  </tr>\
                </thead>\
                <tbody>';
          $.each(e.orders, function( index, value ) {
            response += '<tr>\
                          <td></td>\
                          <td>'+value.data+'</td>\
                          <td>'+value.pedido+'</td>\
                          <td>'+value.origem+'</td>\
                          <td>'+value.state+'</td>\
                          <td>'+value.sku+'</td>\
                          <td>'+value.nome+'</td>\
                          <td>'+value.quantidade+'</td>\
                          <td>'+value.status+'</td>\
                          <td>'+value.nota+'</td>\
                          <td>'+value.dataNota+'</td>\
                          <td>'+value.rastreio+'</td>\
                          <td>'+value.fornecedor+'</td>\
                        </tr>';
          });
          response += '<tbody>\
                        </table>';
        $('.response').html(response);
      });



    }
 </script>
  <!-- iCheck for checkboxes and radio inputs -->
@stop

