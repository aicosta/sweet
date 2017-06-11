<?php

namespace Sweet\Sigep;

use Illuminate\Database\Eloquent\Model;

class Plp extends Model
{
    public function itens()
    {
    	return $this->hasMany(\Sweet\Sigep\PlpItens::class);
    }
}
