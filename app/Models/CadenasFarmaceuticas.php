<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CadenasFarmaceuticas extends Model
{
  use HasFactory;

  protected $table = 'cadenas_farmaceuticas';
  protected $fillable = ['nombre'];
}
