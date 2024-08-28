<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-primary { background-color: #1E617B; }
        .bg-secondary { background-color: #00A38C; }
        .text-primary { color: #1E617B; }
        .text-secondary { color: #00A38C; }
        .bg-accent { background-color: #65C5B9; }
        .bg-light { background-color: #D3EEEB; }
        .bg-white { background-color: #FFFFFF; }
    </style>
</head>
<body class="bg-light min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full">
        <!-- Logo de la farmacia -->
        <div class="mb-6 text-center">
            <img src="/images/image.png" alt="Logo de la farmacia" class="mx-auto h-20 w-20">
        </div>

        <h2 class="text-2xl font-semibold text-primary text-center mb-6">Iniciar Sesión</h2>

        <!-- Formulario de login -->
        <form method="POST" action="/login">
            @csrf
            <div class="mb-4">
                <label class="block text-primary text-sm font-semibold mb-2" for="email">Correo Electrónico</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                id="email" type="email" placeholder="Ingresa tu correo" name="email" :value="old('email')" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-6">
                <label class="block text-primary text-sm font-semibold mb-2" for="password">Contraseña</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" id="password" 
                type="password" name="password" required placeholder="********">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-secondary text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-opacity-50" type="submit">
                    Iniciar Sesión
                </button>
            </div>

            <div class="mt-6 text-center">
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-secondary hover:underline">¿Olvidaste tu contraseña?</a>
                @endif
            </div>
        </form>
    </div>
</body>
</html>
