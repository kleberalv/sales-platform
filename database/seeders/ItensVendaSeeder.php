<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItensVenda;
use App\Models\Venda;
use App\Models\Produto;

class ItensVendaSeeder extends Seeder
{
    public function run()
    {
        $vendas = Venda::all();
        $produtos = Produto::all()->pluck('id')->toArray();

        foreach ($vendas as $venda) {
            $total = 0;

            for ($i = 1; $i <= rand(1, 5); $i++) {
                $produtoId = $produtos[array_rand($produtos)];
                $quantidade = rand(1, 10);
                $precoUnitario = Produto::find($produtoId)->preco;
                $subtotal = $quantidade * $precoUnitario;

                ItensVenda::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $produtoId,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;
            }

            $venda->total = $total;
            $venda->save();
        }
    }
}
