<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RequestHelper;
use App\Helpers\ResponseHelper;

class ClienteController extends Controller
{
    private function validateClienteInput($data, $clienteId = null)
    {
        $data['cpf'] = RequestHelper::formatCpf($data['cpf']);

        $rules = [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'cpf' => 'required|string|min:11|max:11|unique:clientes,cpf,' . $clienteId,
        ];

        $messages = [
            '*' => [
                'required' => 'O campo :attribute é obrigatório',
                'min' => 'O campo :attribute deve conter no mínimo :min caracteres',
                'max' => 'O campo :attribute deve conter no máximo :max caracteres',
            ],
            'cpf.unique' => 'Este CPF já está cadastrado para outro cliente.',
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
            $query = Cliente::when($search, function ($query, $search) {
                $searchFormatted = RequestHelper::formatCpf($search);
                return $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere(function ($query) use ($searchFormatted) {
                        if (!empty($searchFormatted)) {
                            $query->where('cpf', 'like', "%{$searchFormatted}%");
                        }
                    })
                    ->orWhere('telefone', 'like', "%{$search}%");
            });

            if (RequestHelper::isApiRequest($request)) {
                $clientes = $query->get();
                return ResponseHelper::respondWithApi(null, $clientes);
            } else {
                $clientes = $query->paginate(10);
                return view('clientes', compact('clientes'));
            }
        } catch (\Exception $e) {
            $message = 'Erro ao buscar clientes.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('clientes.index', $message, 'error');
        }
    }

    public function store(Request $request)
    {
        try {
            $cpf = RequestHelper::formatCpf($request->cpf);

            $cliente = Cliente::withTrashed()->where('cpf', $cpf)->first();

            if ($cliente) {
                if ($cliente->trashed()) {
                    $cliente->restore();
                    $cliente->update($request->all());
                    $message = 'Cliente reativado com sucesso!';

                    return RequestHelper::isApiRequest($request) ?
                        ResponseHelper::respondWithApi($message, null, Response::HTTP_CREATED) :
                        ResponseHelper::respondWithWeb('clientes.index', $message);
                } else {
                    $message = 'Já existe um cliente cadastrado com este CPF.';

                    return RequestHelper::isApiRequest($request) ?
                        ResponseHelper::respondWithApi($message, null, Response::HTTP_CONFLICT) :
                        ResponseHelper::respondWithWeb('clientes.index', $message, 'error');
                }
            }

            $this->validateClienteInput($request->all());
            $cliente = Cliente::create($request->all());
            $message = 'Cliente criado com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_CREATED) :
                ResponseHelper::respondWithWeb('clientes.index', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = 'Erro na validação dos dados.';
            $errors = collect($e->errors())->flatten()->all();

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY) :
                ResponseHelper::respondWithWeb('clientes.index', $message . ' ' . implode(', ', $errors), 'error');
        } catch (\Exception $e) {
            $message = 'Erro ao criar cliente.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('clientes.index', $message, 'error');
        }
    }

    public function update(Request $request, Cliente $cliente)
    {
        try {
            $this->validateClienteInput($request->all(), $cliente->id);
            $teste1 = $request->all();
            $teste2 = $cliente;
            $cliente->update($request->all());
            $message = 'Cliente atualizado com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_OK) :
                ResponseHelper::respondWithWeb('clientes.index', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = 'Erro na validação dos dados.';
            $errors = collect($e->errors())->flatten()->all();

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY) :
                ResponseHelper::respondWithWeb('clientes.index', $message . ' ' . implode(', ', $errors), 'error');
        } catch (\Exception $e) {
            $message = 'Erro ao atualizar cliente.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('clientes.index', $message, 'error');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();
            $message = 'Cliente deletado com sucesso!';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message) :
                ResponseHelper::respondWithWeb('clientes.index', $message);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $message = 'Erro ao deletar cliente: Cliente não encontrado.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, null, Response::HTTP_NOT_FOUND) :
                ResponseHelper::respondWithWeb('clientes.index', $message, 'error');
        } catch (\Exception $e) {
            $message = 'Erro ao deletar cliente.';

            return RequestHelper::isApiRequest($request) ?
                ResponseHelper::respondWithApi($message, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR) :
                ResponseHelper::respondWithWeb('clientes.index', $message, 'error');
        }
    }
}
