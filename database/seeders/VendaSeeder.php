<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venda;
use App\Models\Cliente;
use Carbon\Carbon;

class VendaSeeder extends Seeder
{
    public function run()
    {
        $clientes = Cliente::all()->pluck('id')->toArray();

        for ($i = 1; $i <= 15; $i++) {
            $venda = Venda::create([
                'cliente_id' => $clientes[array_rand($clientes)],
                'data_venda' => Carbon::now()->subDays(rand(1, 30)),
                'total' => 0
            ]);

            $total = $venda->itens->sum('subtotal');
            $venda->total = $total;
            $venda->save();
        }
    }
}
