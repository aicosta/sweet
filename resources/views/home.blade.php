@extends('adminlte::page')
@section('title', 'Dashboard')
@section('content')
<style type="text/css">
  a.info-box-text{color:#fff;}
  .info-box-text{font-size: 13px;}
</style>

<div class="col-md-12 no-padding">
  <div class="col-md-12">
  </div>

    <!-- INICIO COMPRA PENDENTE -->
  <div class="col-md-3">
    <div class="info-box bg-orange">
      <span class="info-box-icon"><i class="ion ion-ios-cart-outline"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Compra Pendente</span>
        <span class="info-box-number">{{$shop}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/buy-or-invoice">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM COMPRA PENDENTE -->

  <!-- INICIO AGUARDANDO CONFIRMAÇÃO DE COMPRA -->
  <div class="col-md-3">
    <div class="info-box bg-orange">
      <span class="info-box-icon"><i class="ion ion-ios-cart-outline"></i></span>
      <div class="info-box-content">
        <span class="info-box-text" style="font-size: 10px;">Aguardando<br />Confirmação de compra</span>
        <span class="info-box-number">0</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <!-- <a class="info-box-text" href="/orders/buy-or-invoice">ver pedidos</a> -->
          </span>
      </div>
    </div>
  </div>
  <!-- FIM AGUARDANDO CONFIRMAÇÃO DE COMPRA -->
  <!-- INICIO AGUARDANDO PRODUTO -->
  <div class="col-md-3">
    <div class="info-box bg-orange">
      <span class="info-box-icon"><i class="ion ion-ios-stopwatch-outline"></i></span>
      <div class="info-box-content">
        <span class="info-box-text" style="font-size: 10px;">Aguardando Produto</span>
        <span class="info-box-number">{{$waiting}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/14">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM AGUARDANDO PRODUTO -->
  <div class="col-md-12">

  </div>
  <!-- INICIO AGUARDANDO FATURA -->
  <div class="col-md-3">
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="ion ion-ios-flame-outline"></i></span>
      <div class="info-box-content">
        <span class="info-box-text" style="font-size: 10px;">Aguardando Fatura</span>
        <span class="info-box-number">{{$pending}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/1">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM AGUARDANDO FATURA -->
  <!-- INICIO DROP AGUARDANDO FATURA -->
  <div class="col-md-3">
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="ion ion-ios-flame-outline"></i></span>
      <div class="info-box-content">
        <span class="info-box-text" style="font-size: 10px;">Drop Aguardando Fatura</span>
        <span class="info-box-number">0</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="#">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM DROP AGUARDANDO FATURA -->
  <!-- INICIO INCONSISTENCIAS DE FATURA -->
  <div class="col-md-3">
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="ion ion-ios-flame"></i></span>
      <div class="info-box-content">
        <span class="info-box-text" font-size="10px">Inconsistencias de Fatura</span>
        <span class="info-box-number">0</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/2">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM INCONSISTENCIAS DE FATURA -->
  <!-- INICIO FATURADOS -->
  <div class="col-md-3">
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="ion ion-ios-flame"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Faturados</span>
        <span class="info-box-number">{{$invoiced}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/2">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM FATURADOS -->
  <div class="col-md-12">

  </div>
  <!-- INICIO EM TRANSITO -->
  <div class="col-md-3">
    <div class="info-box bg-fuchsia">
      <span class="info-box-icon"><i class="ion ion-android-plane"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Em Transito</span>
        <span class="info-box-number">{{$shipped}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/3">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM EM TRANSITO -->
  <!-- INICIO INCONSCISTENCIA DE TRANSPORTE -->
    <div class="col-md-3">
      <div class="info-box bg-fuchsia">
        <span class="info-box-icon"><i class="ion ion-minus-circled"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" style="font-size: 9px">INCONSCISTENCIAS DE<br /> TRANSPORTE</span>
          <span class="info-box-number">0</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
              <a class="info-box-text" href="#">ver pedidos</a>
            </span>
        </div>
      </div>
    </div>
    <!-- FIM INCONSCISTENCIA DE TRANSPORTE -->
    <!-- INICIO NÃO LOCALIZADOS -->
  <div class="col-md-3">
    <div class="info-box bg-fuchsia">
      <span class="info-box-icon"><i class="ion ion-android-locate"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Não Localizados</span>
        <span class="info-box-number">{{$problems}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/5">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM NÃO LOCALIZADOS -->
  <!-- INICIO EXTRAVIO / ROUBO -->
  <div class="col-md-3">
    <div class="info-box bg-fuchsia">
      <span class="info-box-icon"><i class="ion ion-speakerphone"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Extravio / Roubo</span>
        <span class="info-box-number">{{$thief}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/18">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM EXTRAVIO / ROUBO -->
  <!-- INICIO AGUARDANDO RETIRADA -->
  <div class="col-md-3">
    <div class="info-box bg-fuchsia">
      <span class="info-box-icon"><i class="ion ion-medkit"></i></span>
      <div class="info-box-content">
        <span class="info-box-text" style="font-size: 11px">Aguardando Retirada</span>
        <span class="info-box-number">{{$waiting2}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/16">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM AGUARDANDO RETIRADA -->
  <!-- INICIO CORTE -->
  <div class="col-md-3">
    <div class="info-box bg-fuchsia">
      <span class="info-box-icon"><i class="ion ion-scissors"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Corte</span>
        <span class="info-box-number">{{$cuts}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/15">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM CORTE -->
  <!-- INICIO PENDÊNCIAS DE SAC -->
  <div class="col-md-3">
    <div class="info-box bg-fuchsia">
      <span class="info-box-icon"><i class="ion ion-android-contacts"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Pendências de Sac</span>
        <span class="info-box-number">{{$sac}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/7">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM PENDÊNCIAS DE SAC -->
  <!-- INICIO A COMBINAR -->
  <div class="col-md-3">
    <div class="info-box bg-fuchsia">
      <span class="info-box-icon"><i class="ion ion-android-hand"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">A combinar</span>
        <span class="info-box-number">{{$ml}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/13">Apenas Mercado Livre</a>
          </span>
      </div>
    </div>
  </div>
  <div class="col-md-12">
  </div>
  <!-- FIM A COMBINAR -->
  <div class="col-md-12 no-padding">
    <!-- INICIO AGUARDANDO ALTERAÇÃO -->
    <div class="col-md-3">
      <div class="info-box bg-blue">
        <span class="info-box-icon"><i class="ion ion-settings"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" style="font-size: 12px;">Aguardando Alteração</span>
          <span class="info-box-number">{{$alteracao}}</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/20">Ver Pedidos</a>
            </span>
        </div>
      </div>
    </div>
    <!-- FIM AGUARDANDO ALTERAÇÃO -->
    <!-- INICIO AGUARDANDO ALTERAÇÃO -->
    <div class="col-md-3">
      <div class="info-box bg-blue">
        <span class="info-box-icon"><i class="ion ion-flag"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" style="font-size: 11px;">Ocorrências em aberto</span>
          <span class="info-box-number">0</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
            <a class="info-box-text" href="#">Ver Pedidos</a>
            </span>
        </div>
      </div>
    </div>
    <!-- FIM AGUARDANDO ALTERAÇÃO -->
    <!-- INICIO AGUARDANDO ALTERAÇÃO -->
    <div class="col-md-3">
      <div class="info-box bg-blue">
        <span class="info-box-icon"><i class="ion ion-ios-people"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" style="font-size: 11px;">Ouvidoria</span>
          <span class="info-box-number">{{$ouvidoria}}</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/21">Ver Pedidos</a>
            </span>
        </div>
      </div>
    </div>
    <!-- FIM AGUARDANDO ALTERAÇÃO -->
  </div>
    <!-- INICIO AGUARDANDO APROVAÇÃO -->
  <div class="col-md-3">
    <div class="info-box bg-teal">
      <span class="info-box-icon"><i class="ion ion-android-playstore"></i></span>
      <div class="info-box-content">
        <span class="info-box-text" style="font-size: 10px;">Aguardando Aprovação</span>
        <span class="info-box-number">0</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
          <a class="info-box-text" href="/orders/byStatus/10">Apenas Walmart</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM AGUARDANDO APROVAÇÃO -->

  <!-- INICIO CANCELADOS -->
  <div class="col-md-3">
    <div class="info-box bg-teal">
      <span class="info-box-icon"><i class="ion ion-android-close"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Cancelados</span>
        <span class="info-box-number">{{$canceled}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/8">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM CANCELADOS -->
  <!-- FINALIZADOS -->
  <div class="col-md-3">
    <div class="info-box bg-teal">
      <span class="info-box-icon"><i class="ion ion-android-done-all"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Finalizados</span>
        <span class="info-box-number">{{$finished}}</span>
          <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            <a class="info-box-text" href="/orders/byStatus/6">ver pedidos</a>
          </span>
      </div>
    </div>
  </div>
  <!-- FIM FINALIZADOS -->
  <div class="col-md-12">
  </div>
    <!-- INICIO LOGÍSTICA REVERSA -->
    <div class="col-md-3">
      <div class="info-box bg-black">
        <span class="info-box-icon"><i class="ion ion-cube"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" style="font-size: 9px">AGUARDANDO LOGÍSTICA<br />REVERSA</span>
          <span class="info-box-number">0</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
              <a class="info-box-text" href="#">ver pedidos</a>
            </span>
        </div>
      </div>
    </div>
    <!-- FIM FINALIZADOS -->
    <!-- Aguardando retorno -->
    <div class="col-md-3">
      <div class="info-box bg-black">
        <span class="info-box-icon"><i class="ion ion-ios-time-outline"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" style="font-size: 12px">AGUARDANDO RETORNO</span>
          <span class="info-box-number">0</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
              <a class="info-box-text" href="#">ver pedidos</a>
            </span>
        </div>
      </div>
    </div>
    <!-- FIM FINALIZADOS -->
    <!-- Aguardando DEVOLVIDO -->
    <div class="col-md-3">
      <div class="info-box bg-black">
        <span class="info-box-icon"><i class="ion ion-backspace-outline"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" style="font-size: 12px">DEVOLVIDO</span>
          <span class="info-box-number">0</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
              <a class="info-box-text" href="#">ver pedidos</a>
            </span>
        </div>
      </div>
    </div>
    <!-- FIM FINALIZADOS -->
  <div class="col-md-12 no-padding">
    <h3>Visão Geral dos marketplaces</h3>
    <!-- ML -->
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-yellow">
          <div class="widget-user-image">
            <img class="img-circle" src="/marketplaces/ml.png" alt="User Avatar">
          </div>
          <!-- /.widget-user-image -->
          <h3 class="widget-user-username" style="font-size: 22px">Mercado<br />Livre</h3>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-stacked">
            <li>
              <a href="#">Pedidos <span class="pull-right badge bg-blue">0%</span><span class="pull-right badge bg-blue">0</span></a>
            </li>
            <li><a href="#">Anúncios<span class="pull-right badge bg-aqua">0</span></a></li>
            <li><a href="#">Anúncios Ativos<span class="pull-right badge bg-green">0</span></a></li>
            <li><a href="#">Anúncios Pausados<span class="pull-right badge bg-yellow">0</span></a></li>
            <li><a href="#">Anúncios Finalizados<span class="pull-right badge bg-fuchsia">0</span></a></li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Fim ML -->
    <!-- B2W -->
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-aqua">
          <div class="widget-user-image">
            <img class="img-circle" src="/marketplaces/b2w.png" alt="User Avatar">
          </div>
          <!-- /.widget-user-image -->
          <h3 class="widget-user-username" style="font-size: 45px">B2W</h3>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-stacked">
            <li><a href="#">Pedidos <span class="pull-right badge bg-blue">0</span></a></li>
            <li><a href="#">Produtos Enviados<span class="pull-right badge bg-aqua">0</span></a></li>
            <li><a href="#">Produtos Não Enviados<span class="pull-right badge bg-fuchsia">0</span></a></li>
            <li><a href="#">Produtos Com Estoque<span class="pull-right badge bg-green">0</span></a></li>
            <li><a href="#">Produtos Sem Estoque<span class="pull-right badge bg-yellow">0</span></a></li>

          </ul>
        </div>
      </div>
    </div>
    <!-- FIM B2W -->
    <!-- CNOVA -->
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-fuchsia">
          <div class="widget-user-image">
            <img class="img-circle" src="/marketplaces/cnova.png" alt="User Avatar">
          </div>
          <!-- /.widget-user-image -->
          <h3 class="widget-user-username" style="font-size: 45px">CNOVA</h3>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-stacked">
            <li><a href="#">Pedidos <span class="pull-right badge bg-blue">0</span></a></li>
            <li><a href="#">Produtos Enviados<span class="pull-right badge bg-aqua">0</span></a></li>
            <li><a href="#">Produtos Não Enviados<span class="pull-right badge bg-fuchsia">0</span></a></li>
            <li><a href="#">Produtos Com Estoque<span class="pull-right badge bg-green">0</span></a></li>
            <li><a href="#">Produtos Sem Estoque<span class="pull-right badge bg-yellow">0</span></a></li>

          </ul>
        </div>
      </div>
    </div>
    <!-- FIM CNOVA -->
    <!-- MOBLY -->
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-blue">
          <div class="widget-user-image">
            <img class="img-circle" src="/marketplaces/mobly.jpg" alt="User Avatar">
          </div>
          <!-- /.widget-user-image -->
          <h3 class="widget-user-username" style="font-size: 45px">MOBLY</h3>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-stacked">
            <li><a href="#">Pedidos <span class="pull-right badge bg-blue">0</span></a></li>
            <li><a href="#">Produtos Enviados<span class="pull-right badge bg-aqua">0</span></a></li>
            <li><a href="#">Produtos Não Enviados<span class="pull-right badge bg-fuchsia">0</span></a></li>
            <li><a href="#">Produtos Com Estoque<span class="pull-right badge bg-green">0</span></a></li>
            <li><a href="#">Produtos Sem Estoque<span class="pull-right badge bg-yellow">0</span></a></li>

          </ul>
        </div>
      </div>
    </div>
    <!-- FIM MOBLY -->
    <!-- MOBLY -->
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-blue">
          <div class="widget-user-image">
            <img class="img-circle" src="/marketplaces/mobly.jpg" alt="User Avatar">
          </div>
          <!-- /.widget-user-image -->
          <h3 class="widget-user-username" style="font-size: 45px">MOBLY</h3>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-stacked">
            <li><a href="#">Pedidos <span class="pull-right badge bg-blue">0</span></a></li>
            <li><a href="#">Produtos Enviados<span class="pull-right badge bg-aqua">0</span></a></li>
            <li><a href="#">Produtos Não Enviados<span class="pull-right badge bg-fuchsia">0</span></a></li>
            <li><a href="#">Produtos Com Estoque<span class="pull-right badge bg-green">0</span></a></li>
            <li><a href="#">Produtos Sem Estoque<span class="pull-right badge bg-yellow">0</span></a></li>

          </ul>
        </div>
      </div>
    </div>
    <!-- FIM MOBLY -->
  </div>



  <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Gráfico geral de vendas</h3>

            <div class="box-tools pull-right">
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
              <div class="col-md-8">
                <p class="text-center">
                  <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                </p>

                <div class="chart">
                  <!-- Sales Chart Canvas -->
                  <canvas id="salesChart" style="height: 180px;"></canvas>
                </div>
                <!-- /.chart-responsive -->
              </div>
              <!-- /.col -->
              <div class="col-md-4">
                <p class="text-center">
                  <strong>Goal Completion</strong>
                </p>

                <div class="progress-group">
                  <span class="progress-text">Add Products to Cart</span>
                  <span class="progress-number"><b>160</b>/200</span>

                  <div class="progress sm">
                    <div class="progress-bar progress-bar-aqua" style="width: 80%"></div>
                  </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                  <span class="progress-text">Complete Purchase</span>
                  <span class="progress-number"><b>310</b>/400</span>

                  <div class="progress sm">
                    <div class="progress-bar progress-bar-red" style="width: 80%"></div>
                  </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                  <span class="progress-text">Visit Premium Page</span>
                  <span class="progress-number"><b>480</b>/800</span>

                  <div class="progress sm">
                    <div class="progress-bar progress-bar-green" style="width: 80%"></div>
                  </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                  <span class="progress-text">Send Inquiries</span>
                  <span class="progress-number"><b>250</b>/500</span>

                  <div class="progress sm">
                    <div class="progress-bar progress-bar-yellow" style="width: 80%"></div>
                  </div>
                </div>
                <!-- /.progress-group -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- ./box-body -->
          <div class="box-footer">
            <!-- /.row -->
          </div>
          <!-- /.box-footer -->
        </div>
        <div class="col-md-8">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Vendas por Região</h3>

              <div class="box-tools pull-right">

              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="row">
                <div class="col-md-9 col-sm-8">
                  <div class="pad">
                    <!-- Map will be created here -->
                    <div id="world-map-markers" style="height: 325px;"></div>
                  </div>
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-4">
                  <div class="pad box-pane-right bg-green" style="min-height: 280px">
                    <div class="description-block margin-bottom">
                      <div class="sparkbar pad" data-color="#fff">90,70,90,70,75,80,70</div>
                      <h5 class="description-header">8390</h5>
                      <span class="description-text">Visits</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block margin-bottom">
                      <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                      <h5 class="description-header">30%</h5>
                      <span class="description-text">Referrals</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block">
                      <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                      <h5 class="description-header">70%</h5>
                      <span class="description-text">Organic</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
          </div>
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
</div>

@endsection


@section('adminlte_js')
<script src="/plugins/chartjs/Chart.min.js"></script>
<!-- jvectormap -->
<script src="/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

<!-- Sparkline -->
<script src="plugins/sparkline/jquery.sparkline.min.js"></script>
<script type="text/javascript">
$(function () {
    var salesChartCanvas = $("#salesChart").get(0).getContext("2d");
    var salesChart = new Chart(salesChartCanvas);
    var dataGraph = {!!$salesGraph!!};
    var salesChartOptions = {
    showScale: true,
    scaleShowGridLines: false,
    scaleGridLineColor: "rgba(0,0,0,.05)",
    scaleGridLineWidth: 1,
    scaleShowHorizontalLines: true,
    scaleShowVerticalLines: true,
    bezierCurve: true,
    bezierCurveTension: 0.3,
    pointDot: false,
    pointDotRadius: 4,
    pointDotStrokeWidth: 1,
    pointHitDetectionRadius: 20,
    datasetStroke: true,
    datasetStrokeWidth: 2,
    datasetFill: true,
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%=datasets[i].label%></li><%}%></ul>",
    maintainAspectRatio: true,
    responsive: true
  };

    salesChart.Line(dataGraph, salesChartOptions);
});

$('#world-map-markers').vectorMap({
    map: 'world_mill_en',
    normalizeFunction: 'polynomial',
    hoverOpacity: 0.7,
    hoverColor: false,
    backgroundColor: 'transparent',
    regionStyle: {
      initial: {
        fill: 'rgba(210, 214, 222, 1)',
        "fill-opacity": 1,
        stroke: 'none',
        "stroke-width": 0,
        "stroke-opacity": 1
      },
      hover: {
        "fill-opacity": 0.7,
        cursor: 'pointer'
      },
      selected: {
        fill: 'yellow'
      },
      selectedHover: {}
    },
    markerStyle: {
      initial: {
        fill: '#00a65a',
        stroke: '#111'
      }
    },
    markers: [
      {latLng: [-22.00, -48.85], name: 'São Paulo'},
    ]
  });
/* SPARKLINE CHARTS
   * ----------------
   * Create a inline charts with spark line
   */

  //-----------------
  //- SPARKLINE BAR -
  //-----------------
  $('.sparkbar').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'bar',
      height: $this.data('height') ? $this.data('height') : '30',
      barColor: $this.data('color')
    });
  });

  //-----------------
  //- SPARKLINE PIE -
  //-----------------
  $('.sparkpie').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'pie',
      height: $this.data('height') ? $this.data('height') : '90',
      sliceColors: $this.data('color')
    });
  });

  //------------------
  //- SPARKLINE LINE -
  //------------------
  $('.sparkline').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'line',
      height: $this.data('height') ? $this.data('height') : '90',
      width: '100%',
      lineColor: $this.data('linecolor'),
      fillColor: $this.data('fillcolor'),
      spotColor: $this.data('spotcolor')
    });
  });
</script>
<script src="/vendor/adminlte/dist/js/app.min.js"></script>
@endsection
