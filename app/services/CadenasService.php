<?php

namespace App\Services;

use App\Models\CadenasFarmaceuticas;

class CadenasService
{
  public function all()
  {
    return CadenasFarmaceuticas::select('id', 'nombre')->orderBy('nombre')->get();
  }
}
