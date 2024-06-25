<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\RequestHelper;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'cpf',
    ];

    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = RequestHelper::formatCpf($value);
    }

    public function getCpfAttribute($value)
    {
        return RequestHelper::maskCpf($value);
    }
}
