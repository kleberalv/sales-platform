<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\ItensVenda;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RequestHelper;
use Illuminate\Support\Facades\DB;

class VendaController extends Controller
{
    private function validateVendaInput($data, $vendaId = null)
    {
        $rules = [
            'cliente_id' => 'required|exists:clientes,id',
            'data_venda' => 'required|date',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ];

        $messages = [
            '*' => [
                'required' => 'O campo :attribute é obrigatório',
                'min' => 'O campo :attribute deve conter no mínimo :min caracteres',
                'max' => 'O campo :attribute deve conter no máximo :max caracteres',
            ],
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    public function index(Request $request)
    {
        try {
            $vendas = Venda::with(['itens', 'itens.produto'])->get();

            $vendas = $vendas->map(function ($venda) {
                return [
                    'id' => $venda->id,
                    'cliente_id' => $venda->cliente_id,
                    'data_venda' => $venda->data_venda,
                    'total' => $venda->total,
                    'itens' => $venda->itens->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'produto_id' => $item->produto_id,
                            'produto_nome' => $item->produto->nome,
                            'quantidade' => $item->quantidade,
                            'preco_unitario' => $item->preco_unitario,
                            'subtotal' => $item->subtotal,
                        ];
                    }),
                ];
            });

            if (RequestHelper::isApiRequest($request)) {
                return response()->json($vendas, Response::HTTP_OK);
            }

            return view('vendas.index', compact('vendas'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->validateVendaInput($request->all());

            $total = 0;

            $venda = Venda::create([
                'cliente_id' => $request->cliente_id,
                'data_venda' => $request->data_venda,
                'total' => $total,
            ]);

            foreach ($request->itens as $item) {
                $item['venda_id'] = $venda->id;
                $produto = Produto::find($item['produto_id']);

                if ($produto->estoque < $item['quantidade']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Estoque insuficiente para o produto: ' . $produto->nome,
                    ], Response::HTTP_BAD_REQUEST);
                }

                $produto->estoque -= $item['quantidade'];
                $produto->save();

                $item['preco_unitario'] = $produto->preco;
                $item['subtotal'] = $item['quantidade'] * $item['preco_unitario'];
                $total += $item['subtotal'];

                ItensVenda::create($item);
            }

            $venda->total = $total;
            $venda->save();

            DB::commit();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Venda e itens de venda criados com sucesso!',
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('vendas.index')
                ->with('success', 'Venda e itens de venda criados com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, Venda $venda)
    {
        try {
            $venda = $venda->load('itens.produto');

            $vendaData = [
                'id' => $venda->id,
                'cliente_id' => $venda->cliente_id,
                'data_venda' => $venda->data_venda,
                'total' => $venda->total,
                'itens' => $venda->itens->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'produto_id' => $item->produto_id,
                        'produto_nome' => $item->produto->nome,
                        'quantidade' => $item->quantidade,
                        'preco_unitario' => $item->preco_unitario,
                        'subtotal' => $item->subtotal,
                    ];
                }),
            ];

            if (RequestHelper::isApiRequest($request)) {
                return response()->json($vendaData, Response::HTTP_OK);
            }

            return view('vendas.show', compact('venda'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao encontrar venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Venda $venda)
    {
        DB::beginTransaction();

        try {
            $this->validateVendaInput($request->all(), $venda->id);

            foreach ($venda->itens as $item) {
                $produto = Produto::find($item->produto_id);
                $produto->estoque += $item->quantidade;
                $produto->save();
                $item->delete();
            }

            $venda->update([
                'cliente_id' => $request->cliente_id,
                'data_venda' => $request->data_venda,
            ]);

            $total = 0;

            foreach ($request->itens as $item) {
                $item['venda_id'] = $venda->id;
                $produto = Produto::find($item['produto_id']);

                if ($produto->estoque < $item['quantidade']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Estoque insuficiente para o produto: ' . $produto->nome,
                    ], Response::HTTP_BAD_REQUEST);
                }

                $produto->estoque -= $item['quantidade'];
                $produto->save();

                $item['preco_unitario'] = $produto->preco;
                $item['subtotal'] = $item['quantidade'] * $item['preco_unitario'];
                $total += $item['subtotal'];

                ItensVenda::create($item);
            }

            $venda->total = $total;
            $venda->save();

            DB::commit();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Venda e itens de venda atualizados com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('vendas.index')
                ->with('success', 'Venda e itens de venda atualizados com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao atualizar venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, Venda $venda)
    {
        try {
            foreach ($venda->itens as $item) {
                $produto = Produto::find($item->produto_id);
                $produto->estoque += $item->quantidade;
                $produto->save();
            }

            $venda->delete();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Venda deletada com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('vendas.index')
                ->with('success', 'Venda deletada com sucesso.');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
