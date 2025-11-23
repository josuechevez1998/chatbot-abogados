<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'dui',
        'nombres',
        'apellidos',
    ];

    /**
     * Relación: un cliente tiene muchos documentos
     */
    public function documentos()
    {
        return $this->hasMany(ClienteDocumento::class, 'cliente_id');
    }
}
