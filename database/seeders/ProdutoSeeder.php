<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    public function run()
    {
        $produtos = [
            ['nome' => 'Madeira MDF', 'preco' => 120.00, 'estoque' => 50],
            ['nome' => 'Prego 1 polegada', 'preco' => 5.00, 'estoque' => 1000],
            ['nome' => 'Lixa 150', 'preco' => 2.50, 'estoque' => 500],
            ['nome' => 'Cola de Madeira', 'preco' => 20.00, 'estoque' => 200],
            ['nome' => 'Parafuso 2 polegadas', 'preco' => 0.10, 'estoque' => 2000],
            ['nome' => 'Tinta Verniz', 'preco' => 45.00, 'estoque' => 100],
            ['nome' => 'Pincel', 'preco' => 10.00, 'estoque' => 300],
            ['nome' => 'Martelo', 'preco' => 25.00, 'estoque' => 100],
            ['nome' => 'Serra Circular', 'preco' => 200.00, 'estoque' => 50],
            ['nome' => 'Fita Métrica', 'preco' => 15.00, 'estoque' => 150],
        ];

        foreach ($produtos as $produto) {
            Produto::create($produto);
        }
    }
}
