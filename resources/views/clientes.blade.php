@include('include.header')

<div class="container-fluid" style="min-height: 80vh;">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-8">
            <div class="card bg-white mt-4">
                <div class="card-header">
                    <h1 class="text-center my-1">Lista de Clientes</h1>
                    <br>
                    <div class="d-flex justify-content-between align-items-center">
                        <form id="search" action="{{ route('clientes.index') }}" method="GET">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" name="search" class="form-control" placeholder="Pesquisar" aria-label="Buscar" aria-describedby="button-addon2">
                                <button class="btn btn-secondary" type="submit">Buscar</button>
                            </div>
                        </form>
                        <div class="ml-auto">
                            <button class="btn btn-success float-end" data-bs-toggle="modal" data-bs-target="#adicionarClienteModal">
                                <i class="fa fa-plus"></i> Adicionar Cliente
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($clientes->isEmpty())
                    <p class="d-flex justify-content-center">Nenhum cliente encontrado</p>
                    <br>
                    @else
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->id }}</td>
                                <td>{{ $cliente->nome }}</td>
                                <td>{{ $cliente->cpf }}</td>
                                <td>{{ $cliente->email }}</td>
                                <td>{{ $cliente->telefone }}</td>
                                <td>
                                    <button class="btn btn-primary editar-cliente" data-bs-toggle="modal" data-bs-target="#editarClienteModal" data-cliente-id="{{ $cliente->id }}" data-nome="{{ $cliente->nome }}" data-cpf="{{ $cliente->cpf }}" data-email="{{ $cliente->email }}" data-telefone="{{ $cliente->telefone }}">
                                        <i class="fa fa-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger excluir-cliente" data-cliente-id="{{ $cliente->id }}" data-nome="{{ $cliente->nome }}" data-cpf="{{ $cliente->cpf }}" data-email="{{ $cliente->email }}" data-telefone="{{ $cliente->telefone }}">
                                        <i class="fa fa-trash"></i> Excluir
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    <div class="d-flex justify-content-center">
                        <ul class="pagination">
                            @if ($clientes->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $clientes->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                            @endif

                            @foreach ($clientes->getUrlRange(1, $clientes->lastPage()) as $page => $url)
                            @if ($page == $clientes->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                            @endforeach

                            @if ($clientes->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $clientes->nextPageUrl() }}" rel="next">&raquo;</a></li>
                            @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adicionarClienteModal" tabindex="-1" aria-labelledby="adicionarClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarClienteModalLabel">Adicionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="salvarAdicao" action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" maxlength="14" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($cliente))
<div class="modal fade" id="editarClienteModal" tabindex="-1" aria-labelledby="editarClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarClienteModalLabel">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="salvarEdicao" action="{{ route('clientes.update', ['cliente' => $cliente->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editar-cliente_id" name="cliente_id" value="{{ $cliente->id }}">
                    <div class="mb-3">
                        <label for="editar-nome" class="form-label">Nome completo</label>
                        <input type="text" class="form-control" id="editar-nome" name="nome" value="{{ $cliente->nome }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar-email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="editar-email" name="email" value="{{ $cliente->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar-telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="editar-telefone" name="telefone" value="{{ $cliente->telefone }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar-cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="editar-cpf" name="cpf" maxlength="14" value="{{ $cliente->cpf }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="confirmarExclusaoModal" tabindex="-1" aria-labelledby="confirmarExclusaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarExclusaoModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o cliente <strong id="clienteNomeExcluir"></strong>?</p>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6>Detalhes do Cliente:</h6>
                        <ul id="detalhesClienteExcluir" class="list-group">
                            <!-- Detalhes do cliente serão preenchidos via JavaScript -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="excluir" action="" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function formatCpf(cpf) {
        cpf = cpf.replace(/\D/g, "");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        return cpf;
    }

    $(document).ready(function() {
        $('#cpf, #editar-cpf').on('input', function() {
            this.value = formatCpf(this.value);
        });

        $('.editar-cliente').click(function() {
            var clienteId = $(this).data('cliente-id');
            var nome = $(this).data('nome');
            var email = $(this).data('email');
            var telefone = $(this).data('telefone');
            var cpf = $(this).data('cpf');

            $('#editar-cliente_id').val(clienteId);
            $('#editar-nome').val(nome);
            $('#editar-email').val(email);
            $('#editar-telefone').val(telefone);
            $('#editar-cpf').val(formatCpf(cpf));

            var action = '{{ route("clientes.update", ":id") }}';
            action = action.replace(':id', clienteId);
            $('#salvarEdicao').attr('action', action);
            $('#editarClienteModal').modal('show');
        });

        $('.excluir-cliente').click(function() {
            var clienteId = $(this).data('cliente-id');
            var nome = $(this).data('nome');
            var cpf = $(this).data('cpf');
            var email = $(this).data('email');
            var telefone = $(this).data('telefone');

            $('#clienteNomeExcluir').text(nome);

            var detalhesHtml = `
                <li class="list-group-item"><strong>Nome:</strong> ${nome}</li>
                <li class="list-group-item"><strong>CPF:</strong> ${cpf}</li>
                <li class="list-group-item"><strong>Email:</strong> ${email}</li>
                <li class="list-group-item"><strong>Telefone:</strong> ${telefone}</li>`;
            $('#detalhesClienteExcluir').html(detalhesHtml);

            var formAction = "{{ route('clientes.destroy', ':id') }}";
            formAction = formAction.replace(':id', clienteId);
            $('#excluir').attr('action', formAction);

            $('#confirmarExclusaoModal').modal('show');
        });
    });
</script>

@include('include.footer')