@include('include.header')

<div class="container-fluid" style="min-height: 80vh;">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-8">
            <div class="card bg-white mt-4">
                <div class="card-header">
                    <h1 class="text-center my-1">Lista de Produtos</h1>
                    <br>
                    <div class="d-flex justify-content-between align-items-center">
                        <form id="search" action="{{ route('produtos.index') }}" method="GET">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" name="search" class="form-control" placeholder="Pesquisar" aria-label="Buscar" aria-describedby="button-addon2">
                                <button class="btn btn-secondary" type="submit">Buscar</button>
                            </div>
                        </form>
                        <div class="ml-auto">
                            <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#adicionarProdutoModal">
                                Adicionar Produto
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($produtos->isEmpty())
                    <p class="d-flex justify-content-center">Nenhum produto encontrado</p>
                    <br>
                    @else
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produtos as $produto)
                            <tr>
                                <td>{{ $produto->id }}</td>
                                <td>{{ $produto->nome }}</td>
                                <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                <td>{{ $produto->estoque }}</td>
                                <td>
                                    <button class="btn btn-primary editar-produto" data-bs-toggle="modal" data-bs-target="#editarProdutoModal" data-produto-id="{{ $produto->id }}" data-nome="{{ $produto->nome }}" data-preco="{{ $produto->preco }}" data-estoque="{{ $produto->estoque }}">
                                        <i class="fa fa-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger excluir-produto" data-produto-id="{{ $produto->id }}" data-nome="{{ $produto->nome }}" data-preco="{{ $produto->preco }}" data-estoque="{{ $produto->estoque }}">
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
                            @if ($produtos->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $produtos->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                            @endif

                            @foreach ($produtos->getUrlRange(1, $produtos->lastPage()) as $page => $url)
                            @if ($page == $produtos->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                            @endforeach

                            @if ($produtos->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $produtos->nextPageUrl() }}" rel="next">&raquo;</a></li>
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

<div class="modal fade" id="adicionarProdutoModal" tabindex="-1" aria-labelledby="adicionarProdutoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarProdutoModalLabel">Adicionar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="salvarAdicao" action="{{ route('produtos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço</label>
                        <input type="text" class="form-control" id="preco" name="preco" pattern="^\d*(\.\d{0,2})?$" required>
                    </div>
                    <div class="mb-3">
                        <label for="estoque" class="form-label">Estoque</label>
                        <input type="number" class="form-control" id="estoque" name="estoque" min="0" required>
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

@if(isset($produto))
<div class="modal fade" id="editarProdutoModal" tabindex="-1" aria-labelledby="editarProdutoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarProdutoModalLabel">Editar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="salvarEdicao" action="{{ route('produtos.update', ['produto' => $produto->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editar-produto_id" name="produto_id">
                    <div class="mb-3">
                        <label for="editar-nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="editar-nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar-preco" class="form-label">Preço</label>
                        <input type="text" class="form-control" id="editar-preco" name="preco" pattern="^\d*(\.\d{0,2})?$" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar-estoque" class="form-label">Estoque</label>
                        <input type="number" class="form-control" id="editar-estoque" name="estoque" min="0" required>
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
                <p>Tem certeza que deseja excluir o produto <strong id="produtoNomeExcluir"></strong>?</p>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6>Detalhes do Produto:</h6>
                        <ul id="detalhesProdutoExcluir" class="list-group">
                            <!-- Detalhes do produto serão preenchidos via JavaScript -->
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
    $(document).ready(function() {
        $('.editar-produto').click(function() {
            var produtoId = $(this).data('produto-id');
            var nome = $(this).data('nome');
            var preco = $(this).data('preco');
            var estoque = $(this).data('estoque');

            $('#editar-produto_id').val(produtoId);
            $('#editar-nome').val(nome);
            $('#editar-preco').val(preco);
            $('#editar-estoque').val(estoque);

            var action = '{{ route("produtos.update", ":id") }}';
            action = action.replace(':id', produtoId);
            $('#salvarEdicao').attr('action', action);
            $('#editarProdutoModal').modal('show');
        });

        $('#preco, #editar-preco').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });

        $('.excluir-produto').click(function() {
            var produtoId = $(this).data('produto-id');
            var nome = $(this).data('nome');
            var preco = parseFloat($(this).data('preco')); // Garantir que preco seja um número
            var estoque = $(this).data('estoque');

            $('#produtoNomeExcluir').text(nome);

            var detalhesHtml = `
                <li class="list-group-item"><strong>Nome:</strong> ${nome}</li>
                <li class="list-group-item"><strong>Preço:</strong> R$ ${preco.toFixed(2).replace('.', ',')}</li>
                <li class="list-group-item"><strong>Estoque:</strong> ${estoque}</li>`;
            $('#detalhesProdutoExcluir').html(detalhesHtml);

            var formAction = "{{ route('produtos.destroy', ':id') }}";
            formAction = formAction.replace(':id', produtoId);
            $('#excluir').attr('action', formAction);

            $('#confirmarExclusaoModal').modal('show');
        });

        $('#preco, #editar-preco').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
    });
</script>