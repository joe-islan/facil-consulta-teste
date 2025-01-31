<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cidade extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cidades';

    protected $fillable = ['nome', 'estado'];

    public function medicos()
    {
        return $this->hasMany(Medico::class);
    }
}
