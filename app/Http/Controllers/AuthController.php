<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;

class AuthController extends Controller
{
  	public static function checkPermission($role = array()){
  		return true;
  		//return (\Auth::user()->hasRole($role))?true:abort('403');
  	}

  	public static function checkPermissionApi($role = array()){
  		return (\Auth::user()->hasRole($role))?true:false;
  	}
}
