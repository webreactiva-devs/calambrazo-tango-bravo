<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KahootGame extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nombre_concurso', 'fecha_celebracion', 'numero_participantes'];

    protected static function booted()
    {
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }
}
