<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Market Local</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-500 rounded-lg mb-4">
                <span class="text-white font-bold text-2xl">M</span>
            </div>
            <h1 class="text-3xl font-bold text-white">MarketAdmin</h1>
            <p class="text-gray-400 mt-2">Admin Panel Login</p>
        </div>

        <!-- Login Card -->
        <div class="bg-gray-900 rounded-lg border border-gray-700 p-8 shadow-2xl">
            <form id="loginForm" class="space-y-6">
                @csrf
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        required
                        placeholder="admin@example.com"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        class="w-4 h-4 bg-gray-800 border border-gray-700 rounded focus:ring-red-500"
                    >
                    <label for="remember" class="ml-2 text-sm text-gray-400">Remember me</label>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="hidden bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm"></div>

                <!-- Loading State -->
                <div id="loadingMessage" class="hidden bg-blue-900 border border-blue-700 text-blue-300 px-4 py-3 rounded-lg text-sm flex items-center space-x-2">
                    <span class="material-icons animate-spin text-sm">refresh</span>
                    <span>Logging in...</span>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors flex items-center justify-center space-x-2"
                >
                    <span class="material-icons text-sm">login</span>
                    <span>Sign In</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-900 text-gray-500">Demo Credentials</span>
                </div>
            </div>

            <!-- Demo Info -->
            <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4 text-sm text-gray-300 space-y-2">
                <p><strong>Email:</strong> admin@bazarino.store</p>
                <p><strong>Password:</strong> password</p>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            Market Local Admin Panel © 2026
        </p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('errorMessage');
            const loadingMessage = document.getElementById('loadingMessage');
            
            // Get CSRF token from meta tag or form
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                             document.querySelector('input[name="_token"]')?.value;

            errorMessage.classList.add('hidden');
            loadingMessage.classList.remove('hidden');

            try {
                const response = await fetch('/api/admin/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Store token
                    localStorage.setItem('admin_token', data.data.token);
                    
                    // Redirect to dashboard
                    window.location.href = '/admin/dashboard';
                } else {
                    loadingMessage.classList.add('hidden');
                    errorMessage.classList.remove('hidden');
                    errorMessage.textContent = data.message || 'Login failed. Please try again.';
                }
            } catch (error) {
                loadingMessage.classList.add('hidden');
                errorMessage.classList.remove('hidden');
                errorMessage.textContent = 'An error occurred. Please try again.';
                console.error('Login error:', error);
            }
        });
    </script>
</body>
</html>
