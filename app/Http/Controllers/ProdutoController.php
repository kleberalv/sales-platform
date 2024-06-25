<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RequestHelper;

class ProdutoController extends Controller
{
    private function validateProdutoInput($data)
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric',
            'estoque' => 'required|integer',
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
            $produtos = Produto::all();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json($produtos, Response::HTTP_OK);
            }

            return view('produtos.index', compact('produtos'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->validateProdutoInput($request->all());

            $produto = Produto::create($request->all());

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Produto criado com sucesso!',
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('produtos.index')
                ->with('success', 'Produto criado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar produto.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, Produto $produto)
    {
        try {
            if (RequestHelper::isApiRequest($request)) {
                return response()->json($produto, Response::HTTP_OK);
            }

            return view('produtos.show', compact('produto'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao encontrar produto.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Produto $produto)
    {
        try {
            $this->validateProdutoInput($request->all(), $produto->id);

            $produto->update($request->all());

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Produto atualizado com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('produtos.index')
                ->with('success', 'Produto atualizado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar produto.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, Produto $produto)
    {
        try {
            $produto->delete();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Produto deletado com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('produtos.index')
                ->with('success', 'Produto deletado com sucesso.');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar produto.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
