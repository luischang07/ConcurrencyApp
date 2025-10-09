<?php

namespace App\Repositories;

use App\Models\CadenasFarmaceuticas;
use Illuminate\Support\Collection;

class CadenasRepository
{
  public function getAll(): Collection
  {
    return CadenasFarmaceuticas::select('id', 'nombre')->orderBy('nombre')->get();
  }
}
