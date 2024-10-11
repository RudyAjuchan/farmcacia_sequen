<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
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
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full my-10">
        <!-- Logo de la farmacia -->
        <div class="mb-6 text-center">
            <img src="/images/image.png" alt="Logo de la farmacia" class="mx-auto h-20 w-20">
        </div>

        <h2 class="text-2xl font-semibold text-primary text-center mb-6">Registro de Usuario</h2>

        <!-- Formulario de registro -->
        <form method="POST" action="/register">
            @csrf

            <div class="mb-4">
                <label class="block text-primary text-sm font-semibold mb-2" for="nombre">Nombre</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                id="name" type="text" placeholder="Ingresa tu nombre" name="name" :value="old('name')" required autofocus>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-4">
                <label class="block text-primary text-sm font-semibold mb-2" for="usuario">Email</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                id="email" type="email" placeholder="Ingresa tu usuario" name="email" :value="old('email')" required>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-4">
                <label class="block text-primary text-sm font-semibold mb-2" for="direccion">Dirección (opcional)</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                id="direccion" type="text" placeholder="Ingresa tu dirección" name="direccion" :value="old('direccion')">
            </div>

            <div class="mb-4">
                <label class="block text-primary text-sm font-semibold mb-2" for="nit">NIT (opcional)</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                id="nit" type="text" placeholder="Ingresa tu NIT" name="nit" :value="old('nit')">
            </div>

            <div class="mb-4">
                <label class="block text-primary text-sm font-semibold mb-2" for="telefono">Teléfono</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                id="telefono" type="tel" placeholder="Ingresa tu teléfono" name="telefono" :value="old('telefono')" required>
                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
            </div>

            <div>
                <label class="block text-primary text-sm font-semibold mb-2" for="password">Contraseña</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" id="password" 
                type="password" name="password" required placeholder="********">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="mb-6">
                <label class="block text-primary text-sm font-semibold mb-2" for="password">Confirmar Contraseña</label>
                <input class="appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" id="password_confirmation" 
                type="password" name="password_confirmation" required placeholder="********">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-secondary text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-opacity-50" type="submit">
                    Registrar
                </button>
            </div>

            <div class="mt-6 text-center">
                <a href="/log_in" class="text-sm text-secondary hover:underline">¿Ya tienes una cuenta? Inicia sesión aquí</a>
            </div>
        </form>
    </div>
</body>
</html>
