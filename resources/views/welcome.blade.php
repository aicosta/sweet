@extends('adminlte::page')
@section('title', 'Dashboard')
@section('content')
<div class="col-md-12">
  <div class="col-md-4">
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Pendentes</span>
        <span class="info-box-number">5,200</span>
          <div class="progress">
            <div class="progress-bar" style="width: 50%"></div>
          </div>
          <span class="progress-description">
            50% Increase in 30 Days
          </span>
      </div>
    </div>
  </div>
</div>
@endsection
