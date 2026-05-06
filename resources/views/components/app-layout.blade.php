<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Student Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen">
        <header class="glass sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l9-5-9-5-9 5 9 5zm0 0v6m0-6L3 9m18 0l-9 5" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900 tracking-tight">University Admission Portal</h1>
                    </div>
                </div>
            </div>
        </header>

        <main class="py-12">
            {{ $slot }}
        </main>

        <footer class="mt-auto py-10 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} University Health Sciences. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
