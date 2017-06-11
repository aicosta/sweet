<?php

namespace Sweet\Http\Controllers\marketplaces;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\Http\Controllers\Controller;

class MarketplacesController extends Controller
{
    public function items(){
        return view('marketplaces.itens');
    }
}
