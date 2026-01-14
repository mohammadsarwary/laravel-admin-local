<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester - Market Local</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
        .method-get { background-color: #22c55e; }
        .method-post { background-color: #3b82f6; }
        .method-put { background-color: #f59e0b; }
        .method-delete { background-color: #ef4444; }
        .method-patch { background-color: #8b5cf6; }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body class="min-h-screen text-gray-100">
    <div x-data="apiTester()" x-init="init()" class="flex flex-col h-screen">
        <!-- Header -->
        <header class="bg-gray-900 border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">api</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">API Tester</h1>
                        <p class="text-sm text-gray-400">Test all Market Local API endpoints</p>
                    </div>
                </div>
                
                <!-- Auth Token -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-400">Token:</span>
                        <input type="password" 
                               x-model="authToken" 
                               placeholder="Paste Bearer token here"
                               class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 w-80 focus:ring-2 focus:ring-red-500">
                        <button @click="toggleTokenVisibility()" class="text-gray-400 hover:text-white">
                            <span class="material-icons text-sm" x-text="tokenVisible ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    <button @click="clearToken()" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded-lg transition-colors">
                        Clear
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar - Endpoints List -->
            <aside class="w-80 bg-gray-900 border-r border-gray-700 overflow-y-auto">
                <div class="p-4">
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Search endpoints..."
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500">
                </div>
                
                <nav class="space-y-1 px-2">
                    <template x-for="category in filteredCategories" :key="category.name">
                        <div>
                            <button @click="toggleCategory(category.name)" 
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 rounded-lg transition-colors">
                                <span x-text="category.name"></span>
                                <span class="material-icons text-sm" x-text="expandedCategories.includes(category.name) ? 'expand_less' : 'expand_more'"></span>
                            </button>
                            
                            <div x-show="expandedCategories.includes(category.name)" class="mt-1 space-y-1">
                                <template x-for="endpoint in category.endpoints" :key="endpoint.method + endpoint.path">
                                    <button @click="selectEndpoint(endpoint)"
                                            :class="selectedEndpoint === endpoint ? 'bg-red-500/20 border-red-500' : 'hover:bg-gray-800 border-transparent'"
                                            class="w-full flex items-center space-x-2 px-3 py-2 text-left text-sm rounded-lg border transition-colors">
                                        <span :class="'method-' + endpoint.method.toLowerCase()" 
                                              class="px-2 py-0.5 text-xs font-bold rounded text-white"
                                              x-text="endpoint.method"></span>
                                        <span class="text-gray-300 truncate" x-text="endpoint.path"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </nav>
            </aside>

            <!-- Main Panel -->
            <main class="flex-1 flex flex-col overflow-hidden">
                <!-- Request Panel -->
                <div class="flex-1 flex flex-col overflow-y-auto p-6 space-y-6">
                    <!-- Endpoint Info -->
                    <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <select x-model="selectedMethod" 
                                    class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white font-bold focus:ring-2 focus:ring-red-500">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                                <option value="PATCH">PATCH</option>
                            </select>
                            <input type="text" 
                                   x-model="selectedPath"
                                   placeholder="/api/endpoint"
                                   class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500">
                            <button @click="sendRequest()" 
                                    :disabled="loading"
                                    class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 flex items-center space-x-2">
                                <span class="material-icons text-sm" :class="{ 'animate-spin': loading }" x-text="loading ? 'refresh' : 'send'"></span>
                                <span x-text="loading ? 'Sending...' : 'Send'"></span>
                            </button>
                        </div>
                        
                        <div x-show="selectedEndpoint" class="text-sm text-gray-400">
                            <p><strong>Description:</strong> <span x-text="selectedEndpoint?.description"></span></p>
                            <p class="mt-1"><strong>Authentication:</strong> <span :class="selectedEndpoint?.auth ? 'text-yellow-400' : 'text-green-400'" x-text="selectedEndpoint?.auth ? 'Required' : 'Not Required'"></span></p>
                        </div>
                    </div>

                    <!-- Request Body -->
                    <div x-show="['POST', 'PUT', 'PATCH'].includes(selectedMethod)" class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Request Body (JSON)</h3>
                            <button @click="formatRequestBody()" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded transition-colors">
                                Format JSON
                            </button>
                        </div>
                        <textarea x-model="requestBody"
                                  rows="10"
                                  placeholder='{"key": "value"}'
                                  class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 font-mono text-sm focus:ring-2 focus:ring-red-500"></textarea>
                    </div>

                    <!-- Query Parameters -->
                    <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Query Parameters</h3>
                            <button @click="addQueryParam()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition-colors flex items-center space-x-1">
                                <span class="material-icons text-sm">add</span>
                                <span>Add Parameter</span>
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(param, index) in queryParams" :key="index">
                                <div class="flex items-center space-x-2">
                                    <input type="text" 
                                           x-model="param.key"
                                           placeholder="Key"
                                           class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 text-sm focus:ring-2 focus:ring-red-500">
                                    <input type="text" 
                                           x-model="param.value"
                                           placeholder="Value"
                                           class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 text-sm focus:ring-2 focus:ring-red-500">
                                    <button @click="removeQueryParam(index)" class="p-2 text-red-400 hover:text-red-300">
                                        <span class="material-icons text-sm">delete</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Response Panel -->
                    <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Response</h3>
                            <div x-show="responseStatus" class="flex items-center space-x-2">
                                <span :class="responseStatus >= 200 && responseStatus < 300 ? 'text-green-400' : 'text-red-400'" 
                                      class="font-bold"
                                      x-text="responseStatus"></span>
                                <span class="text-gray-400" x-text="responseTime + 'ms'"></span>
                            </div>
                        </div>
                        
                        <div x-show="loading" class="flex items-center justify-center py-8">
                            <div class="flex items-center space-x-2 text-gray-400">
                                <span class="material-icons animate-spin">refresh</span>
                                <span>Loading...</span>
                            </div>
                        </div>
                        
                        <div x-show="!loading && response" class="space-y-4">
                            <!-- Response Body -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-400">Response Body</span>
                                    <button @click="copyResponse()" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded transition-colors flex items-center space-x-1">
                                        <span class="material-icons text-sm">content_copy</span>
                                        <span>Copy</span>
                                    </button>
                                </div>
                                <pre class="bg-gray-800 border border-gray-700 rounded-lg p-4 text-sm text-green-400 overflow-x-auto font-mono" x-text="formattedResponse"></pre>
                            </div>
                            
                            <!-- Response Headers -->
                            <div x-show="responseHeaders">
                                <span class="text-sm text-gray-400">Response Headers</span>
                                <div class="mt-2 bg-gray-800 border border-gray-700 rounded-lg p-4 text-sm">
                                    <template x-for="(value, key) in responseHeaders" :key="key">
                                        <div class="flex justify-between py-1 border-b border-gray-700">
                                            <span class="text-blue-400" x-text="key"></span>
                                            <span class="text-gray-300" x-text="value"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <div x-show="!loading && !response" class="py-8 text-center text-gray-500">
                            <span class="material-icons text-4xl mb-2">api</span>
                            <p>Send a request to see the response</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function apiTester() {
            return {
                categories: [
                    {
                        name: 'Public - Authentication',
                        endpoints: [
                            { method: 'POST', path: '/api/auth/register', description: 'User registration', auth: false },
                            { method: 'POST', path: '/api/auth/login', description: 'User login', auth: false },
                        ]
                    },
                    {
                        name: 'Public - Data',
                        endpoints: [
                            { method: 'GET', path: '/api/categories', description: 'List all active categories', auth: false },
                            { method: 'GET', path: '/api/ads', description: 'List all ads (public)', auth: false },
                            { method: 'GET', path: '/api/ads/conditions', description: 'Get available ad conditions', auth: false },
                            { method: 'GET', path: '/api/ads/{ad}', description: 'Get single ad details', auth: false },
                            { method: 'GET', path: '/api/users/{user}', description: 'Get user profile', auth: false },
                            { method: 'GET', path: '/api/users/{user}/ads', description: 'Get user\'s ads', auth: false },
                        ]
                    },
                    {
                        name: 'Protected - Authentication',
                        endpoints: [
                            { method: 'POST', path: '/api/auth/refresh', description: 'Refresh authentication token', auth: true },
                            { method: 'POST', path: '/api/auth/logout', description: 'User logout', auth: true },
                            { method: 'GET', path: '/api/auth/me', description: 'Get current authenticated user', auth: true },
                        ]
                    },
                    {
                        name: 'Protected - User Profile',
                        endpoints: [
                            { method: 'PUT', path: '/api/users/profile', description: 'Update user profile', auth: true },
                            { method: 'POST', path: '/api/users/avatar', description: 'Update user avatar', auth: true },
                            { method: 'POST', path: '/api/users/change-password', description: 'Change password', auth: true },
                            { method: 'DELETE', path: '/api/users/account', description: 'Delete user account', auth: true },
                            { method: 'GET', path: '/api/users/favorites', description: 'Get user\'s favorite ads', auth: true },
                        ]
                    },
                    {
                        name: 'Protected - Ads',
                        endpoints: [
                            { method: 'POST', path: '/api/ads', description: 'Create new ad', auth: true },
                            { method: 'PUT', path: '/api/ads/{ad}', description: 'Update ad', auth: true },
                            { method: 'DELETE', path: '/api/ads/{ad}', description: 'Delete ad', auth: true },
                            { method: 'POST', path: '/api/ads/{ad}/sold', description: 'Mark ad as sold', auth: true },
                            { method: 'POST', path: '/api/ads/{ad}/images', description: 'Upload ad images', auth: true },
                            { method: 'POST', path: '/api/ads/{ad}/favorite', description: 'Toggle ad favorite status', auth: true },
                        ]
                    },
                    {
                        name: 'Admin - Authentication',
                        endpoints: [
                            { method: 'POST', path: '/api/admin/login', description: 'Admin login', auth: false },
                            { method: 'GET', path: '/api/admin/verify', description: 'Verify admin authentication', auth: true },
                        ]
                    },
                    {
                        name: 'Admin - Dashboard',
                        endpoints: [
                            { method: 'GET', path: '/api/admin/stats', description: 'Get dashboard statistics', auth: true },
                            { method: 'GET', path: '/api/admin/activity', description: 'Get recent activity log', auth: true },
                        ]
                    },
                    {
                        name: 'Admin - Users',
                        endpoints: [
                            { method: 'GET', path: '/api/admin/users', description: 'List all users with pagination', auth: true },
                            { method: 'GET', path: '/api/admin/users/export', description: 'Export users to CSV', auth: true },
                            { method: 'POST', path: '/api/admin/users/create', description: 'Create new user', auth: true },
                            { method: 'POST', path: '/api/admin/users/bulk-action', description: 'Bulk actions (activate, deactivate, delete)', auth: true },
                            { method: 'GET', path: '/api/admin/users/{user}', description: 'Get user details', auth: true },
                            { method: 'GET', path: '/api/admin/users/{user}/activity', description: 'Get user activity log', auth: true },
                            { method: 'PUT', path: '/api/admin/users/{user}/suspend', description: 'Suspend user', auth: true },
                            { method: 'PUT', path: '/api/admin/users/{user}/activate', description: 'Activate user', auth: true },
                            { method: 'PUT', path: '/api/admin/users/{user}/ban', description: 'Ban user', auth: true },
                            { method: 'PUT', path: '/api/admin/users/{user}/verify', description: 'Verify user', auth: true },
                            { method: 'DELETE', path: '/api/admin/users/{user}', description: 'Delete user', auth: true },
                        ]
                    },
                    {
                        name: 'Admin - Ads',
                        endpoints: [
                            { method: 'GET', path: '/api/admin/ads', description: 'List all ads with pagination', auth: true },
                            { method: 'GET', path: '/api/admin/ads/export', description: 'Export ads to CSV', auth: true },
                            { method: 'POST', path: '/api/admin/ads/bulk-action', description: 'Bulk actions (approve, reject, feature, promote, delete)', auth: true },
                            { method: 'GET', path: '/api/admin/ads/{ad}', description: 'Get ad details', auth: true },
                            { method: 'PUT', path: '/api/admin/ads/{ad}/approve', description: 'Approve ad', auth: true },
                            { method: 'PUT', path: '/api/admin/ads/{ad}/reject', description: 'Reject ad', auth: true },
                            { method: 'PUT', path: '/api/admin/ads/{ad}/feature', description: 'Feature ad', auth: true },
                            { method: 'PUT', path: '/api/admin/ads/{ad}/promote', description: 'Promote ad', auth: true },
                            { method: 'DELETE', path: '/api/admin/ads/{ad}', description: 'Delete ad', auth: true },
                        ]
                    },
                    {
                        name: 'Admin - Reports',
                        endpoints: [
                            { method: 'GET', path: '/api/admin/reports', description: 'List all reports', auth: true },
                            { method: 'GET', path: '/api/admin/reports/stats', description: 'Get report statistics', auth: true },
                            { method: 'GET', path: '/api/admin/reports/{report}', description: 'Get report details', auth: true },
                            { method: 'PUT', path: '/api/admin/reports/{report}/resolve', description: 'Resolve report', auth: true },
                            { method: 'PUT', path: '/api/admin/reports/{report}/dismiss', description: 'Dismiss report', auth: true },
                            { method: 'POST', path: '/api/admin/reports/{report}/action', description: 'Take action on report', auth: true },
                        ]
                    },
                    {
                        name: 'Admin - Categories',
                        endpoints: [
                            { method: 'GET', path: '/api/admin/categories', description: 'List all categories with pagination', auth: true },
                            { method: 'GET', path: '/api/admin/categories/export', description: 'Export categories to CSV', auth: true },
                            { method: 'POST', path: '/api/admin/categories', description: 'Create new category', auth: true },
                            { method: 'POST', path: '/api/admin/categories/bulk-action', description: 'Bulk actions (activate, deactivate, delete)', auth: true },
                            { method: 'GET', path: '/api/admin/categories/{category}', description: 'Get category details', auth: true },
                            { method: 'PUT', path: '/api/admin/categories/{category}', description: 'Update category', auth: true },
                            { method: 'PUT', path: '/api/admin/categories/{category}/toggle-status', description: 'Toggle category active status', auth: true },
                            { method: 'DELETE', path: '/api/admin/categories/{category}', description: 'Delete category', auth: true },
                        ]
                    },
                    {
                        name: 'Admin - Analytics',
                        endpoints: [
                            { method: 'GET', path: '/api/admin/analytics/users', description: 'User analytics data', auth: true },
                            { method: 'GET', path: '/api/admin/analytics/ads', description: 'Ads analytics data', auth: true },
                            { method: 'GET', path: '/api/admin/analytics/categories', description: 'Categories analytics data', auth: true },
                            { method: 'GET', path: '/api/admin/analytics/locations', description: 'Location analytics data', auth: true },
                        ]
                    }
                ],
                
                expandedCategories: [],
                selectedEndpoint: null,
                selectedMethod: 'GET',
                selectedPath: '',
                searchQuery: '',
                authToken: '',
                tokenVisible: false,
                requestBody: '',
                queryParams: [],
                loading: false,
                response: null,
                responseStatus: null,
                responseTime: null,
                responseHeaders: null,
                formattedResponse: '',
                
                init() {
                    // Load saved token from localStorage
                    const savedToken = localStorage.getItem('api_tester_token');
                    if (savedToken) {
                        this.authToken = savedToken;
                    }
                    
                    // Expand first category by default
                    if (this.categories.length > 0) {
                        this.expandedCategories.push(this.categories[0].name);
                    }
                },
                
                get filteredCategories() {
                    if (!this.searchQuery) return this.categories;
                    
                    return this.categories.map(category => ({
                        name: category.name,
                        endpoints: category.endpoints.filter(endpoint => 
                            endpoint.path.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            endpoint.description.toLowerCase().includes(this.searchQuery.toLowerCase())
                        )
                    })).filter(category => category.endpoints.length > 0);
                },
                
                toggleCategory(categoryName) {
                    const index = this.expandedCategories.indexOf(categoryName);
                    if (index > -1) {
                        this.expandedCategories.splice(index, 1);
                    } else {
                        this.expandedCategories.push(categoryName);
                    }
                },
                
                selectEndpoint(endpoint) {
                    this.selectedEndpoint = endpoint;
                    this.selectedMethod = endpoint.method;
                    this.selectedPath = endpoint.path;
                    
                    // Set default request body based on endpoint
                    if (endpoint.method === 'POST' && endpoint.path.includes('login')) {
                        this.requestBody = JSON.stringify({
                            email: 'admin@bazarino.store',
                            password: 'password'
                        }, null, 2);
                    } else if (endpoint.method === 'POST' && endpoint.path.includes('register')) {
                        this.requestBody = JSON.stringify({
                            name: 'Test User',
                            email: 'test@example.com',
                            password: 'password123',
                            password_confirmation: 'password123'
                        }, null, 2);
                    } else {
                        this.requestBody = '';
                    }
                },
                
                toggleTokenVisibility() {
                    this.tokenVisible = !this.tokenVisible;
                },
                
                clearToken() {
                    this.authToken = '';
                    localStorage.removeItem('api_tester_token');
                },
                
                addQueryParam() {
                    this.queryParams.push({ key: '', value: '' });
                },
                
                removeQueryParam(index) {
                    this.queryParams.splice(index, 1);
                },
                
                formatRequestBody() {
                    try {
                        const parsed = JSON.parse(this.requestBody);
                        this.requestBody = JSON.stringify(parsed, null, 2);
                    } catch (e) {
                        alert('Invalid JSON format');
                    }
                },
                
                async sendRequest() {
                    if (!this.selectedPath) {
                        alert('Please select an endpoint');
                        return;
                    }
                    
                    this.loading = true;
                    this.response = null;
                    this.responseStatus = null;
                    this.responseTime = null;
                    this.responseHeaders = null;
                    
                    // Save token
                    if (this.authToken) {
                        localStorage.setItem('api_tester_token', this.authToken);
                    }
                    
                    // Build query string
                    const queryString = this.queryParams
                        .filter(p => p.key && p.value)
                        .map(p => `${encodeURIComponent(p.key)}=${encodeURIComponent(p.value)}`)
                        .join('&');
                    
                    const url = (this.selectedPath.startsWith('http') ? '' : '') + this.selectedPath + (queryString ? '?' + queryString : '');
                    
                    const headers = {
                        'Accept': 'application/json',
                    };
                    
                    if (this.authToken) {
                        headers['Authorization'] = `Bearer ${this.authToken}`;
                    }
                    
                    const options = {
                        method: this.selectedMethod,
                        headers: headers,
                    };
                    
                    if (['POST', 'PUT', 'PATCH'].includes(this.selectedMethod) && this.requestBody) {
                        try {
                            JSON.parse(this.requestBody);
                            headers['Content-Type'] = 'application/json';
                            options.body = this.requestBody;
                        } catch (e) {
                            alert('Invalid JSON in request body');
                            this.loading = false;
                            return;
                        }
                    }
                    
                    const startTime = performance.now();
                    
                    try {
                        const response = await fetch(url, options);
                        const endTime = performance.now();
                        
                        this.responseStatus = response.status;
                        this.responseTime = Math.round(endTime - startTime);
                        
                        // Get headers
                        const headersObj = {};
                        response.headers.forEach((value, key) => {
                            headersObj[key] = value;
                        });
                        this.responseHeaders = headersObj;
                        
                        // Get response body
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            this.response = await response.json();
                            this.formattedResponse = JSON.stringify(this.response, null, 2);
                        } else {
                            this.response = await response.text();
                            this.formattedResponse = this.response;
                        }
                        
                        // Store token from login response
                        if (this.selectedPath.includes('login') && this.response?.data?.token) {
                            this.authToken = this.response.data.token;
                            localStorage.setItem('api_tester_token', this.authToken);
                        }
                    } catch (error) {
                        this.response = { error: error.message };
                        this.formattedResponse = JSON.stringify({ error: error.message }, null, 2);
                    } finally {
                        this.loading = false;
                    }
                },
                
                copyResponse() {
                    navigator.clipboard.writeText(this.formattedResponse);
                    alert('Response copied to clipboard!');
                }
            }
        }
    </script>
</body>
</html>
