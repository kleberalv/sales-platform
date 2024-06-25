<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RequestHelper;

class ClienteController extends Controller
{
    private function validateClienteInput($data)
    {
        $data['cpf'] = RequestHelper::formatCpf($data['cpf']);

        $rules = [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'cpf' => 'required|string|max:11|unique:clientes,cpf,' . ($clienteId ?? 'null'),
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
            $clientes = Cliente::all();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json($clientes, Response::HTTP_OK);
            }

            return view('clientes.index', compact('clientes'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $cpf = RequestHelper::formatCpf($request->cpf);

            $cliente = Cliente::withTrashed()->where('cpf', $cpf)->first();

            if ($cliente) {
                $cliente->restore();
                $cliente->update($request->all());

                if (RequestHelper::isApiRequest($request)) {
                    return response()->json([
                        'message' => 'Cliente reativado com sucesso!',
                    ], Response::HTTP_OK);
                }

                return redirect()->route('clientes.index')
                    ->with('success', 'Cliente reativado com sucesso.');
            }

            $this->validateClienteInput($request->all());
            $cliente = Cliente::create($request->all());

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Cliente criado com sucesso!',
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente criado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar cliente.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, Cliente $cliente)
    {
        try {
            if (RequestHelper::isApiRequest($request)) {
                return response()->json($cliente, Response::HTTP_OK);
            }

            return view('clientes.show', compact('cliente'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao encontrar cliente.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Cliente $cliente)
    {
        try {
            $this->validateClienteInput($request->all(), $cliente->id);

            $cliente->update($request->all());

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Cliente atualizado com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente atualizado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro na validação dos dados.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar cliente.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();

            if (RequestHelper::isApiRequest($request)) {
                return response()->json([
                    'message' => 'Cliente deletado com sucesso!',
                ], Response::HTTP_OK);
            }

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente deletado com sucesso.');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar cliente.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
