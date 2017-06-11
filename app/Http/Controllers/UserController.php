<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\User;
use Sweet\Role;
use Sweet\Permission;
use Auth;
use Entrust;

class UserController extends Controller
{
    public function list(){
        if(Entrust::hasRole('admin')){
            $users = User::orderBy('name')->get();

            return view('users.list')->with(compact('users'));
        }else{
            abort(403, 'Unauthorized action.');
        }

    }

    public function edit($id){
        if(Entrust::hasRole('admin') || Auth::user()->id == $id){
            $user = User::find($id);
            $roleId = (isset($user->roles[0]->id))?$user->roles[0]->id:false;
            $roles = Role::get();
            return view('users.edit')->with(compact('user', 'roles','roleId'));
        }else{
            abort(403, 'Unauthorized action.');
        }
    }

    public function status($id){
        $user = User::find($id);

        if($user){
            if($user->status == 0){
                $user->status = 1;
            }else{
                $user->status = 0;
            }
            $user->save();
            return back()->with('message','Usuário Atualizado');
        }else{
            return back()->with('error','Não foi possivel alterar o usuário');
        }
    }
}
