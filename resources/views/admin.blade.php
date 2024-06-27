@include('include.header')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header" style="background-color: #003366; color: white;">
                    <h3 class="mb-0">Área Administrativa</h3>
                </div>
                <div class="card-body">
                    <p class="lead">Bem-vindo à área administrativa! Utilize o menu abaixo para navegar pelas opções disponíveis.</p>
                    <div class="list-group">
                        <a id="vendas" href="{{ route('vendas.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-shopping-cart"></i> Gerenciar Vendas
                        </a>
                        <a id="clientes" href="{{ route('clientes.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-users"></i> Gerenciar Clientes
                        </a>
                        <a id="produtos" href="{{ route('produtos.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-archive"></i> Gerenciar Produtos
                        </a>
                        <a id="relatorio" href="{{ route('vendas.relatorio') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-file"></i> Relatório de Vendas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include.footer')