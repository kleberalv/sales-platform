<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Cliente;
use App\Models\ItensVenda;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RequestHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;

class VendaController extends Controller
{
    private function validateVendaInput($data)
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

    public function relatorio(Request $request)
    {
        $query = Venda::with(['cliente', 'itens.produto']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('cliente', function ($query) use ($search) {
                $query->where('nome', 'like', "%{$search}%");
            });
        }

        if ($request->has('all')) {
            $vendas = $query->get();
            return response()->json($vendas);
        }

        $vendas = $query->paginate(10);

        return view('relatorio', compact('vendas'));
    }

    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $vendasQuery = Venda::with(['cliente', 'itens.produto']);

            if ($search) {
                $vendasQuery->whereHas('cliente', function ($query) use ($search) {
                    $query->where('nome', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cpf', 'like', "%{$search}%");
                });
            }

            $vendas = RequestHelper::isApiRequest($request) ? $vendasQuery->get() : $vendasQuery->paginate(10);
            $produtos = Produto::all();
            $clientes = Cliente::all();

            if (RequestHelper::isApiRequest($request)) {
                return ResponseHelper::respondWithApi(null, $vendas);
            }

            return view('vendas', compact('vendas', 'produtos', 'clientes'));
        } catch (\Exception $e) {
            $message = 'Erro ao buscar vendas.';
            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
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
                    $message = 'Estoque insuficiente para o produto: ' . $produto->nome;
                    return RequestHelper::isApiRequest($request) ?
                        ResponseHelper::respondWithApi($message, null, Response::HTTP_BAD_REQUEST) :
                        ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
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
            $message = 'Venda e itens de venda criados com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_CREATED) :
                ResponseHelper::respondWithWeb('vendas.index', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $message = 'Erro na validação dos dados.';
            $errors = collect($e->errors())->flatten()->all();

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY) :
                ResponseHelper::respondWithWeb('vendas.index', $message . ' ' . implode(', ', $errors), 'error');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Erro ao criar venda.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
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
                return ResponseHelper::respondWithApi(null, $vendaData, Response::HTTP_OK);
            }

            return view('vendas.show', compact('venda'));
        } catch (\Exception $e) {
            $message = 'Erro ao encontrar venda.';
            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
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
            $venda->delete();

            $total = 0;
            $newVenda = Venda::create([
                'cliente_id' => $request->cliente_id,
                'data_venda' => $request->data_venda,
                'total' => $total,
            ]);

            foreach ($request->itens as $item) {
                $item['venda_id'] = $newVenda->id;
                $produto = Produto::find($item['produto_id']);

                if ($produto->estoque < $item['quantidade']) {
                    DB::rollBack();
                    $message = 'Estoque insuficiente para o produto: ' . $produto->nome;
                    return RequestHelper::isApiRequest($request) ?
                        ResponseHelper::respondWithApi($message, null, Response::HTTP_BAD_REQUEST) :
                        ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
                }

                $produto->estoque -= $item['quantidade'];
                $produto->save();

                $item['preco_unitario'] = $produto->preco;
                $item['subtotal'] = $item['quantidade'] * $item['preco_unitario'];
                $total += $item['subtotal'];

                ItensVenda::create($item);
            }

            $newVenda->total = $total;
            $newVenda->save();

            DB::commit();
            $message = 'Venda e itens de venda atualizados com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_OK) :
                ResponseHelper::respondWithWeb('vendas.index', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $message = 'Erro na validação dos dados.';
            $errors = collect($e->errors())->flatten()->all();

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY) :
                ResponseHelper::respondWithWeb('vendas.index', $message . ' ' . implode(', ', $errors), 'error');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Erro ao atualizar venda.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
        }
    }

    public function destroy(Request $request, Venda $venda)
    {
        DB::beginTransaction();

        try {
            foreach ($venda->itens as $item) {
                $produto = Produto::find($item->produto_id);
                $produto->estoque += $item->quantidade;
                $produto->save();
                $item->delete();
            }

            $venda->delete();
            DB::commit();
            $message = 'Venda e itens de venda deletados com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message) :
                ResponseHelper::respondWithWeb('vendas.index', $message);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $message = 'Erro ao deletar venda: Venda não encontrada.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_NOT_FOUND) :
                ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Erro ao deletar venda.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('vendas.index', $message, 'error');
        }
    }
}
