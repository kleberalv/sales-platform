<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        $clientes = [
            ['nome' => 'João Silva', 'email' => 'joao.silva@example.com', 'telefone' => '61999999901', 'cpf' => '12345678901'],
            ['nome' => 'Maria Oliveira', 'email' => 'maria.oliveira@example.com', 'telefone' => '61999999902', 'cpf' => '12345678902'],
            ['nome' => 'Carlos Souza', 'email' => 'carlos.souza@example.com', 'telefone' => '61999999903', 'cpf' => '12345678903'],
            ['nome' => 'Ana Pereira', 'email' => 'ana.pereira@example.com', 'telefone' => '61999999904', 'cpf' => '12345678904'],
            ['nome' => 'Fernanda Lima', 'email' => 'fernanda.lima@example.com', 'telefone' => '61999999905', 'cpf' => '12345678905'],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
