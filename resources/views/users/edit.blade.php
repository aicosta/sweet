@extends('adminlte::page')

@section('title', 'Busca e lista de produtos')



@section('content')
<div class="row">
  <div class="col-md-12">
    
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Editando Usuário {{$user->name}}</h3>
      </div>
      <div class="box-body box-profile">
        <form>
          <div class="col-md-2">
            <img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">
          </div>
          <div class="col-md-10">
            <div class="col-md-12">
              <div class="form-group">
                <label>Nome</label>
                <input type="text" class="form-control" name="name" value="{{$user->name}}"/>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="{{$user->email}}"/>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Regras e permissões</h3>
              </div>
              <div class="box-body box-profile">
              <div class="form-group">
                @foreach($roles as $role)
                  <div class="radio">
                    <label>
                      <input type="radio" name="role" value="{{$role->id}}"
                        @if($roleId == $role->id)
                          checked="checked"
                        @endif
                        />
                      {{$role->display_name}}
                    </label>
                  </div>
                @endforeach
                </div>

              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@stop
