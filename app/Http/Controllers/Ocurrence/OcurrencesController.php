<?php

namespace Sweet\Http\Controllers\Ocurrence;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\Http\Controllers\Controller;
use Sweet\sector;
use Sweet\Ocurrence\Ocurrence;
use Sweet\Ocurrence\OcurrenceType;
use Sweet\Ocurrence\Type;



class OcurrencesController extends Controller
{
    public function sector(){
        $sectors = sector::get();
        dd($sectors);
    }
    public function sectorCreate(){
        return view('ocurrence.sector.create');
    }

}
