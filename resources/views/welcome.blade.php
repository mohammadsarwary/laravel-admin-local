<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Market Local Admin</h1>
        <p class="text-gray-600 mb-8">Laravel Admin Panel for Market Local Marketplace</p>
        <div class="space-x-4">
            <a href="/api/admin/login" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Admin Login
            </a>
            <a href="/api" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                API Documentation
            </a>
        </div>
    </div>
</body>
</html>
