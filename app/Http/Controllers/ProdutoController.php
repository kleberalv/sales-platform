<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RequestHelper;
use App\Helpers\ResponseHelper;

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
            $search = $request->input('search');
            $query = Produto::when($search, function ($query, $search) {
                return $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('preco', 'like', "%{$search}%")
                    ->orWhere('estoque', 'like', "%{$search}%");
            });

            if (RequestHelper::isApiRequest($request)) {
                $produtos = $query->get();
                return ResponseHelper::respondWithApi(null, $produtos);
            } else {
                $produtos = $query->paginate(10);
                return view('produtos', compact('produtos'));
            }
        } catch (\Exception $e) {
            $message = 'Erro ao buscar produtos.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('produtos.index', $message, 'error');
        }
    }

    public function store(Request $request)
    {
        try {
            $this->validateProdutoInput($request->all());

            $produto = Produto::create($request->all());
            $message = 'Produto criado com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_CREATED) :
                ResponseHelper::respondWithWeb('produtos.index', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = 'Erro na validação dos dados.';
            $errors = collect($e->errors())->flatten()->all();

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY) :
                ResponseHelper::respondWithWeb('produtos.index', $message . ' ' . implode(', ', $errors), 'error');
        } catch (\Exception $e) {
            $message = 'Erro ao criar produto.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('produtos.index', $message, 'error');
        }
    }

    public function show(Request $request, Produto $produto)
    {
        try {
            if (RequestHelper::isApiRequest($request)) {
                return ResponseHelper::respondWithApi(null, $produto, Response::HTTP_OK);
            }

            return view('produtos.show', compact('produto'));
        } catch (\Exception $e) {
            $message = 'Erro ao encontrar produto.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('produtos.index', $message, 'error');
        }
    }

    public function update(Request $request, Produto $produto)
    {
        try {
            $this->validateProdutoInput($request->all(), $produto->id);

            $produto->update($request->all());
            $message = 'Produto atualizado com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_OK) :
                ResponseHelper::respondWithWeb('produtos.index', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = 'Erro na validação dos dados.';
            $errors = collect($e->errors())->flatten()->all();

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY) :
                ResponseHelper::respondWithWeb('produtos.index', $message . ' ' . implode(', ', $errors), 'error');
        } catch (\Exception $e) {
            $message = 'Erro ao atualizar produto.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('produtos.index', $message, 'error');
        }
    }

    public function destroy(Request $request, Produto $produto)
    {
        try {
            $produto->delete();
            $message = 'Produto deletado com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message) :
                ResponseHelper::respondWithWeb('produtos.index', $message);
        } catch (\Exception $e) {
            $message = 'Erro ao deletar produto.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('produtos.index', $message, 'error');
        }
    }
}
