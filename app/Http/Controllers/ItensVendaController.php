<?php

namespace App\Http\Controllers;

use App\Models\ItensVenda;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RequestHelper;

class ItemVendaController extends Controller
{
    private function validateItemVendaInput($data, $itemVendaId = null)
    {
        $rules = [
            'venda_id' => 'required|exists:vendas,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
            'preco_unitario' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
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
            $itensVenda = ItensVenda::all();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json($itensVenda, Response::HTTP_OK);
            }

            return view('itens_venda.index', compact('itensVenda'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->validateItemVendaInput($request->all());

            $itemVenda = ItensVenda::create($request->all());

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Item de venda criado com sucesso!',
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('itens_venda.index')
                ->with('success', 'Item de venda criado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar item de venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, ItensVenda $itemVenda)
    {
        try {
            if (RequestHelper::isApiRequest($request)) {
                return response()->json($itemVenda, Response::HTTP_OK);
            }

            return view('itens_venda.show', compact('itemVenda'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao encontrar item de venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->validateItemVendaInput($request->all(), $id);

            $itemVenda = ItensVenda::findOrFail($id);
            $itemVenda->update($request->all());

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Item de venda atualizado com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('itens_venda.index')
                ->with('success', 'Item de venda atualizado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar item de venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $itemVenda = ItensVenda::findOrFail($id);
            $itemVenda->delete();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Item de venda deletado com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('itens_venda.index')
                ->with('success', 'Item de venda deletado com sucesso.');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar item de venda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
