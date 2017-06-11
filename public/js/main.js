$(document).ready(function(){
   $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      }
    });
  $(".checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));

    $('#datepicker').datepicker({
      autoclose: true
    });
});

  $('.invoice-provider').change(function(){
    window.location= '/invoice/'+$(this).val();
  })
  $('.orderStatus').change(function(){
    window.location= '/orders/byStatus/'+$(this).val();
  })
  $('.buyorsell-provider').change(function(){
    if($(this).val() ==0){
      window.location= '/orders/buy-or-invoice';
    }else{
      window.location= '/orders/buy-or-invoice/'+$(this).val();
    }

  })


$("#invoiceDataTable").DataTable({
  "paging": false
});

  $('.changeStatus').change(function(){
    if(confirm('Tem certeza que deseja alterar o status?')){
      var code = $('.orderID').val();
      var status = $(this).val();
      $.ajax(
        {
          method:'GET',
          url: "/orders/changeStatus/"+code+"/"+status,
          beforeSend:function(){
            $('.load-page').fadeIn();
          },
          success: function(e){
            $('.load-page').fadeOut();
            alert('Status do pedido Alterado');
          }
      });


    }else{
      return false;
    }
  });


  $("input[name=ship_code]").blur(function(){
    var ship_id = $('input[name=ship_id]').val();
    var ship_code = $('input[name=ship_code]').val();
    $.ajax(
        {
          method:'POST',
          url: "/tags/postcode",
          data: {ship_id:ship_id,  ship_code:ship_code},
          beforeSend:function(){
            $('.load-page').fadeIn('fast');
          },
          success: function(e){
              $('.load-page').fadeOut('fast');
              $('.modalMessage').html('Rastreio do pedido alterado para '+ship_code);
              $('.show-success-modal').fadeIn('fast').delay(5000).fadeOut('slow');
          }
      });
  });
  $('.close').click(function(){
    $('.modal').fadeOut('fast');
  });
  $('.stockInsert').submit(function(){
    var produto = $('input[name=produto]').val();
    var ins = $('input[name=ins]').val();
    var outs = $('input[name=outs]').val();
    var observation = $('input[name=observation]').val();
    var origin = $('input[name=origin]').val();

    $.ajax(
      {
        method:'POST',
        url: "/stock/insert",
        data: {produto:produto,ins:ins,outs:outs,observation:observation,origin:origin},
        success: function(e){
          alert(e);
          if(e == 1){
            alert('Estoque Inserido para o produto '+produto);
            location.reload();
          }else{
            alert('Ocorreu um problema na gravação do comentário.');
          }
        }
    });
    return false;
  });

  $(".showReports").click(function(){
    $(".productReports").slideToggle();
  })
});


function addAttribute(){
  var attr = '<div class="attr-1">\
              <div class="col-sm-6">\
                <div class="form-group">\
                  <input type="text" class="form-control" name="attribute[name][1]" placeholder="Nome do Atributo" value="">\
                </div>\
                          </div>\
                          <div class="col-md-5">\
                            <div class="form-group">\
                  <input type="text" class="form-control" name="attribute[value][1]" placeholder="Valor do atributo">\
                </div>\
                          </div>\
                          <div class="col-md-1">\
                            <div class="form-group">\
                            <a href="javascript:void(0);" class="btn btn-danger" onclick="removeAttribute(1)">-</a>\
                            </div>\
                          </div>\
                          </div>';
  $('.attributes').append(attr);
}

function removeAttribute(id){
  $('.attr-1').remove();
}

function sendToBuy(){
  $('#invoiceDataTable input:checked').each(function() {
      $.ajax(
        {
          method:'GET',
          url: "/orders/changeStatus/"+$(this).attr('value')+"/14",
          success: function(e){

          }
      });
      alert('pedidos alterados');
  });

}
function sendToInvoice(){
  $('#invoiceDataTable input:checked').each(function() {
      $.ajax(
        {
          method:'GET',
          url: "/orders/changeStatus/"+$(this).attr('value')+"/1",
          success: function(e){

          }
      });
      alert('pedidos alterados');
  });
}

function showPlp(){
  $('.dataRastreio').slideUp('fast');
  $('.plphide').slideDown('fast');
  return false;
}
function insereRastreio(){
    var pedido = $('input[name=rastreio-pedido]').val();
    var rastreio = $('input[name=rastreio-rastreio]').val();
    var data = $('input[name=rastreio-data]').val();

    if(rastreio != ''){
      $.ajax(
        {
          method:'POST',
          url: "/orders/insertShipping",
          data: {pedido:pedido,  rastreio:rastreio,  data:data },
          success: function(e){
            if(e == 1){
              alert('Rastreio incluido no pedido');
            }else if(e == 2){
              alert('Houve um erro na inclusão do rastreio');
            }else{
              alert('Houve um problema na localização do pedido');
            }
            location.reload();
          }
      });
    }

}

function lancarEntradas(){
  var nff = $('input[name=nff]').val();
  var sku = new Array( );
  var quantity = new Array( );
  $('.sku').each(function() {
      sku.push($(this).val());
  });
  $('.quantity').each(function() {
      quantity.push($(this).val());
  });

  $.ajax(
      {
        method:'POST',
        url: "/nff/validAndInsert",
        data: {sku:sku,  quantity:quantity, nff:nff},
        success: function(e){
          $('p.message').html(e);
          $('.messagebox').slideDown('fast');
          $("html, body").animate({ scrollTop: 0 }, "slow");
        }
    });

}


function saveComment(orderId, userId, father = false){
  var comment = $(".dataComment").val();
  if(comment == ''){
    return false;
  }
  $.ajax(
      {
        method:'POST',
        url: "/orders/comment",
        data: {comment:comment,  orderId:orderId, userId:userId, father:father},
        success: function(e){
          if(e == 1){
            location.reload();
          }else{
            alert('Ocorreu um problema na gravação do comentário.');
          }
        }
    });
}
