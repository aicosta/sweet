@extends('adminlte::page')

@section('title', 'Busca e lista de produtos')



@section('content')
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Lista de Produtos</h3>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Nome</th>
                  <th>Email</th>
                  <th>Data de Criação</th>
                  <th>Ultima Atualização</th>
                  <th>Status</th>
                </tr>
                @foreach($users as $user)
                <tr>
                  <td><a href="/users/edit/{{$user->id}}">{{$user->name}}</a></td>
                  <td><a href="/users/edit/{{$user->id}}">{{$user->email}}</a></td>
                  <td><a href="/users/edit/{{$user->id}}">{{date('d/m/Y H:i:s', strtotime($user->created_at))}}</a></td>
                  <td><a href="/users/edit/{{$user->id}}">{{date('d/m/Y H:i:s', strtotime($user->updated_at))}}</a></td>
                  <td>
                    <a href="/users/status/{{$user->id}}">
                    @if($user->status)
                      <span class="label label-success">Ativo</span>
                    @else
                      <span class="label label-danger">Inativo</span>
                    @endif
                    </a>
                  </td>
                </tr>
                @endforeach
             </table>
            </div>
            <div class="box-footer">
            <button type="button" class="btn btn-default">Cadastrar Usuário</button>
            </div>
        </div>

    </div>

@stop
