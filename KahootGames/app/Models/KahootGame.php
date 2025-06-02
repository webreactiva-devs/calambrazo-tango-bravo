<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KahootGame extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nombre_concurso', 'fecha_celebracion', 'numero_participantes'];

    protected static function booted()
    {
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }
}
