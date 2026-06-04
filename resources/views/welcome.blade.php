<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ config('app.name', 'Laravel') }}</h1>
                <p class="text-lg text-gray-600 mb-8">Your one-stop e-commerce platform</p>
                <div class="space-x-4">
                    <a href="{{ route('products.index') }}" class="bg-indigo-500 text-white px-6 py-3 rounded-lg hover:bg-indigo-600">Browse Products</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </body>
</html>
