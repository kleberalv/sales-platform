@include('include.header')

<div class="container-fluid" style="min-height: 80vh;">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-10">
            <div class="card bg-white mt-4">
                <div class="card-header">
                    <h1 class="text-center my-1">Consulta de Vendas</h1>
                    <br>
                    <div class="d-flex justify-content-between align-items-center">
                        <form id="search" action="{{ route('vendas.index') }}" method="GET">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" name="search" class="form-control" placeholder="Pesquisar" aria-label="Buscar" aria-describedby="button-addon2">
                                <button class="btn btn-secondary" type="submit">Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if ($vendas->isEmpty())
                    <p class="d-flex justify-content-center">Nenhuma venda encontrada</p>
                    <br>
                    @else
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data da Venda</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendas as $venda)
                            <tr>
                                <td>{{ $venda->id }}</td>
                                <td>{{ $venda->cliente->nome }}</td>
                                <td>{{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y') }}</td>
                                <td>R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-primary visualizar-venda" data-venda="{{ json_encode($venda) }}">
                                        <i class="fa fa-eye"></i> Visualizar
                                    </button>
                                    <button class="btn btn-warning editar-venda" data-venda="{{ json_encode($venda) }}">
                                        <i class="fa fa-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger excluir" data-venda="{{ json_encode($venda) }}">
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
                            @if ($vendas instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            @if ($vendas->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $vendas->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                            @endif

                            @foreach ($vendas->getUrlRange(1, $vendas->lastPage()) as $page => $url)
                            @if ($page == $vendas->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                            @endforeach

                            @if ($vendas->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $vendas->nextPageUrl() }}" rel="next">&raquo;</a></li>
                            @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="visualizarVendaModal" tabindex="-1" aria-labelledby="visualizarVendaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="visualizarVendaModalLabel">Detalhes da Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detalhesVenda">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editarVendaModal" tabindex="-1" aria-labelledby="editarVendaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarVendaModalLabel">Editar Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="salvarEdicao" action="{{ route('vendas.update', ['venda' => ':id']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="editar-cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="editar-cliente_id" name="cliente_id" required>
                            @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editar-data_venda" class="form-label">Data da Venda</label>
                        <input type="date" class="form-control" id="editar-data_venda" name="data_venda" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Itens</label>
                        <div id="editar-itens-container">
                        </div>
                        <button type="button" class="btn btn-secondary" id="adicionar-item-editar">Adicionar Item</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmarExclusaoModal" tabindex="-1" aria-labelledby="confirmarExclusaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarExclusaoModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <p>Tem certeza que deseja excluir a venda de ID <strong id="vendaIdExcluir"></strong> do cliente <strong id="clienteNomeExcluir"></strong>?</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6>Produtos na venda:</h6>
                        <ul id="produtosExcluir" class="list-group">

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

        function adicionarItem(container, produtos) {
            var itemHtml = `
            <div class="row mb-3 item-venda">
                <div class="col-md-3">
                    <label class="form-label">Produto</label>
                    <select class="form-select produto-select" name="itens[][produto_id]" required>
                        <option value="">Selecione um produto</option>
                        ${produtos.map(produto => `<option value="${produto.id}" data-preco="${produto.preco}">${produto.nome}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantidade</label>
                    <input type="number" class="form-control quantidade-input" name="itens[][quantidade]" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal-input" readonly>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger btn-remover-item mt-4">Remover</button>
                </div>
            </div>`;
            container.append(itemHtml);
        }

        function atualizarSubtotal(item) {
            var preco = parseFloat(item.find('.produto-select option:selected').data('preco')) || 0;
            var quantidade = parseInt(item.find('.quantidade-input').val()) || 0;
            var subtotal = preco * quantidade;
            item.find('.subtotal-input').val(`R$ ${subtotal.toFixed(2).replace('.', ',')}`);
        }

        $('#adicionar-item-editar').click(function() {
            var container = $('#editar-itens-container');
            adicionarItem(container, @json($produtos));
        });

        $(document).on('change', '.produto-select, .quantidade-input', function() {
            var item = $(this).closest('.item-venda');
            atualizarSubtotal(item);
        });

        $(document).on('click', '.btn-remover-item', function() {
            $(this).closest('.item-venda').remove();
        });

        $('.editar-venda').click(function() {
            var venda = $(this).data('venda');
            var form = $('#salvarEdicao');
            var url = form.attr('action').replace(':id', venda.id);
            form.attr('action', url);
            $('#editar-cliente_id').val(venda.cliente.id);
            $('#editar-data_venda').val(venda.data_venda);
            var container = $('#editar-itens-container');
            container.empty();
            venda.itens.forEach(function(item) {
                adicionarItem(container, @json($produtos));
                var lastItem = container.find('.item-venda').last();
                lastItem.find('.produto-select').val(item.produto_id);
                lastItem.find('.quantidade-input').val(item.quantidade);
                atualizarSubtotal(lastItem);
            });
            $('#editarVendaModal').modal('show');
        });

        $('#salvarEdicao').submit(function(event) {
            var form = $(this);
            var clienteId = $('#editar-cliente_id').val();
            var dataVenda = $('#editar-data_venda').val();
            var itens = [];

            $('#editar-itens-container .item-venda').each(function() {
                var produtoId = $(this).find('.produto-select').val();
                var quantidade = $(this).find('.quantidade-input').val();
                itens.push({
                    produto_id: produtoId,
                    quantidade: quantidade
                });
            });

            var data = {
                cliente_id: clienteId,
                data_venda: dataVenda,
                itens: itens
            };

            form.find('input[name="itens[][produto_id]"]').remove();
            form.find('input[name="itens[][quantidade]"]').remove();

            itens.forEach(function(item, index) {
                form.append('<input type="hidden" name="itens[' + index + '][produto_id]" value="' + item.produto_id + '">');
                form.append('<input type="hidden" name="itens[' + index + '][quantidade]" value="' + item.quantidade + '">');
            });

            form.off('submit').submit();
        });

        function formatarMoeda(valor) {
            return `R$ ${parseFloat(valor).toFixed(2).replace('.', ',')}`;
        }

        $('.visualizar-venda').click(function() {
            var venda = $(this).data('venda');
            var detalhesVendaHtml = `
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><strong>ID da Venda:</strong> ${venda.id}</h6>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Cliente:</strong> ${venda.cliente.nome}</h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><strong>Data da Venda:</strong> ${new Date(venda.data_venda).toLocaleDateString('pt-BR')}</h6>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Total:</strong> ${formatarMoeda(venda.total)}</h6>
                    </div>
                </div>
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>`;

            venda.itens.forEach(item => {
                detalhesVendaHtml += `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.produto.nome}</td>
                    <td>${item.quantidade}</td>
                    <td>${formatarMoeda(item.preco_unitario)}</td>
                    <td>${formatarMoeda(item.subtotal)}</td>
                </tr>`;
            });

            detalhesVendaHtml += `
                    </tbody>
                </table>
            </div>`;

            $('#detalhesVenda').html(detalhesVendaHtml);
            $('#visualizarVendaModal').modal('show');
        });

        $('.excluir').click(function() {
            var venda = $(this).data('venda');
            var vendaId = venda.id;
            var clienteNome = venda.cliente.nome;
            var produtos = venda.itens;

            $('#vendaIdExcluir').text(vendaId);
            $('#clienteNomeExcluir').text(clienteNome);

            var produtosHtml = '';
            produtos.forEach(function(item) {
                produtosHtml += `<li class="list-group-item">${item.produto.nome} - Quantidade: ${item.quantidade}</li>`;
            });
            $('#produtosExcluir').html(produtosHtml);

            var formAction = "{{ route('vendas.destroy', ':id') }}";
            formAction = formAction.replace(':id', vendaId);
            $('#excluir').attr('action', formAction);

            $('#confirmarExclusaoModal').modal('show');
        });


    });
</script>