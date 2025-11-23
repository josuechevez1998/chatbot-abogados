<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClienteDocumento extends Model
{
    use HasFactory;

    protected $table = 'clientes_documentos';

    protected $fillable = [
        'cliente_id',
        'name',
        'type',
        'path',
    ];

    /**
     * Relación: cada documento pertenece a un cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
