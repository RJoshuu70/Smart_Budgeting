<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Smart Budgeting</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $cssFile = $manifest['resources/css/app.css']['file'] ?? 'assets/app.css';
        $jsFile = $manifest['resources/js/app.js']['file'] ?? 'assets/app.js';
    @endphp
    <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
    <script type="module" src="{{ asset('build/' . $jsFile) }}" defer></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-4 my-8">

        <div class="text-center mb-8">
            <div class="text-6xl text-indigo-600 mb-3">
                <i class="bi bi-piggy-bank-fill"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Smart Budgeting</h1>
            <p class="text-gray-500 text-sm mt-1">Buat akun dan mulai kelola keuanganmu</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Buat Akun Baru</h2>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="Nama kamu"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('name') border-red-400 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('email') border-red-400 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password"
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                        placeholder="Ulangi password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700 transition-colors">
                    Daftar Sekarang
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</body>
</html>