@include('include.header')

<div class="container-fluid" style="min-height: 80vh;">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-10">
            <div class="card bg-white mt-4">
                <div class="card-header">
                    <h1 class="text-center my-1">Relatório de Vendas</h1>
                    <br>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <form id="search" action="{{ route('vendas.relatorio') }}" method="GET" class="d-flex">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" name="search" class="form-control" placeholder="Pesquisar" aria-label="Buscar" aria-describedby="button-addon2" value="{{ request()->input('search') }}">
                                <button class="btn btn-secondary" type="submit">Buscar</button>
                            </div>
                        </form>
                        <div>
                            <button class="btn btn-success me-2" id="imprimirTelaAtual">
                                <i class="fa fa-print"></i> Imprimir Atual
                            </button>
                            <button class="btn btn-primary" id="imprimirTudo">
                                <i class="fa fa-print"></i> Imprimir Tudo
                            </button>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    @if ($vendas->isEmpty())
                    <p class="d-flex justify-content-center">Nenhuma venda encontrada</p>
                    <br>
                    @else
                    <table class="table table-hover" id="relatorioTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data da Venda</th>
                                <th>Total</th>
                                <th>Itens</th>
                                <th class="no-print">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendas as $venda)
                            <tr id="venda-{{ $venda->id }}">
                                <td>{{ $venda->id }}</td>
                                <td>{{ $venda->cliente->nome }}</td>
                                <td>{{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y') }}</td>
                                <td>R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                                <td>
                                    <ul>
                                        @foreach($venda->itens as $item)
                                        <li>{{ $item->produto->nome }} - Quantidade: {{ $item->quantidade }} - Preço Unitário: R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="no-print">
                                    <button class="btn btn-secondary btn-sm" onclick="printSingleRow({{ $venda->id }})">
                                        <i class="fa fa-print"></i> Imprimir
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

@include('include.footer')

<script>
    function fetchAllData() {
        $('#overlay').show();
        $('#loader').show();
        return $.ajax({
            url: "{{ route('vendas.relatorio') }}",
            method: "GET",
            data: {
                all: true,
                search: "{{ request()->input('search') }}"
            },
            dataType: "json"
        }).always(function() {
            $('#overlay').hide();
            $('#loader').hide();
        });
    }

    function printRelatorio(vendas) {
        const rows = vendas.sort((a, b) => a.id - b.id).map(venda => `
            <tr>
                <td>${venda.id}</td>
                <td>${venda.cliente.nome}</td>
                <td>${new Date(venda.data_venda).toLocaleDateString('pt-BR')}</td>
                <td>R$ ${parseFloat(venda.total).toFixed(2).replace('.', ',')}</td>
                <td>
                    <ul>
                        ${venda.itens.map(item => `
                            <li>${item.produto.nome} - Quantidade: ${item.quantidade} - Preço Unitário: R$ ${parseFloat(item.preco_unitario).toFixed(2).replace('.', ',')}</li>
                        `).join('')}
                    </ul>
                </td>
            </tr>
        `).join('');

        const printContent = `
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Data da Venda</th>
                        <th>Total</th>
                        <th>Itens</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        `;

        const printWindow = window.open('', '', `height=${screen.availHeight},width=${screen.availWidth}`);
        printWindow.document.write(`
            <html>
                <head>
                    <title>Relatório de Vendas</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            padding: 10px;
                            border: 1px solid #ddd;
                        }
                        th {
                            background-color: #003366;
                            color: white;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();

        $('#overlay').hide();
        $('#loader').hide();
    }

    function printCurrentPage() {
        const printContent = document.getElementById('relatorioTable').outerHTML;
        const printWindow = window.open('', '', `height=${screen.availHeight},width=${screen.availWidth}`);
        printWindow.document.write(`
            <html>
                <head>
                    <title>Relatório de Vendas</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            padding: 10px;
                            border: 1px solid #ddd;
                        }
                        th {
                            background-color: #003366;
                            color: white;
                        }
                        .no-print {
                            display: none;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();

        $('#overlay').hide();
        $('#loader').hide();
    }

    function printSingleRow(id) {
        const row = document.getElementById(`venda-${id}`).outerHTML;
        const printContent = `
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Data da Venda</th>
                        <th>Total</th>
                        <th>Itens</th>
                    </tr>
                </thead>
                <tbody>
                    ${row}
                </tbody>
            </table>
        `;
        const printWindow = window.open('', '', `height=${screen.availHeight},width=${screen.availWidth}`);
        printWindow.document.write(`
            <html>
                <head>
                    <title>Relatório de Vendas</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            padding: 10px;
                            border: 1px solid #ddd;
                        }
                        th {
                            background-color: #003366;
                            color: white;
                        }
                        .no-print {
                            display: none;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();

        $('#overlay').hide();
        $('#loader').hide();
    }

    $(document).ready(function() {
        $('#imprimirTelaAtual').click(function() {
            $('#overlay').show();
            $('#loader').show();
            printCurrentPage();
        });

        $('#imprimirTudo').click(function() {
            $('#overlay').show();
            $('#loader').show();
            fetchAllData().done(function(data) {
                printRelatorio(data);
            }).fail(function() {
                alert('Erro ao buscar dados para impressão.');
                $('#overlay').hide();
                $('#loader').hide();
            });
        });
    });
</script>