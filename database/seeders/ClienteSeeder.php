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
            ['nome' => 'João Silva', 'email' => 'joao.silva@example.com', 'telefone' => '61999999901', 'cpf' => '83491256789'],
            ['nome' => 'Maria Oliveira', 'email' => 'maria.oliveira@example.com', 'telefone' => '61999999902', 'cpf' => '70518349265'],
            ['nome' => 'Carlos Souza', 'email' => 'carlos.souza@example.com', 'telefone' => '61999999903', 'cpf' => '64927315840'],
            ['nome' => 'Ana Pereira', 'email' => 'ana.pereira@example.com', 'telefone' => '61999999904', 'cpf' => '57382164950'],
            ['nome' => 'Fernanda Lima', 'email' => 'fernanda.lima@example.com', 'telefone' => '61999999905', 'cpf' => '12845670392'],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
