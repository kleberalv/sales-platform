@include('include.header')

@auth
{{-- Usuário já autenticado, redirecione para a área de admin --}}
<script>
    window.location = "{{ route('admin') }}";
</script>
@endauth

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card col-md-6 p-4" style="background-color: #003366; color: white; border-radius: 15px;">
        <div class="card-body text-center">
            <img src="{{ asset('images/logo-branco.fw-430x96.png') }}" alt="Logo" class="mb-4" style="width: 80%; height: auto;">
            <form id="login" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required style="background-color: #f8f9fa; color: #000;">
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required style="background-color: #f8f9fa; color: #000;">
                </div>
                <div class="mb-3 form-check text-start">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" style="background-color: #f8f9fa; color: #000;">
                    <label class="form-check-label" for="remember">Lembrar-me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="background-color: white; color: #003366; border: none;">Login</button>
            </form>
        </div>
    </div>
</div>

@include('include.footer')

<style>
    .btn-primary:hover {
        background-color: #6faed9 !important;
        color: white !important;
    }

    .form-check-input:checked {
        background-color: #6faed9 !important;
        border-color: #6faed9 !important;
    }
</style>
