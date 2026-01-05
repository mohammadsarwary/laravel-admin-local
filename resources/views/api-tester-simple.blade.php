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
        .category-section {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .category-section.expanded {
            max-height: 2000px;
            transition: max-height 0.3s ease-in;
        }
    </style>
</head>
<body class="min-h-screen text-gray-100">
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
                           id="authToken"
                           placeholder="Paste Bearer token here"
                           class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 w-80 focus:ring-2 focus:ring-red-500">
                    <button onclick="toggleTokenVisibility()" class="text-gray-400 hover:text-white">
                        <span class="material-icons text-sm" id="tokenVisibilityIcon">visibility</span>
                    </button>
                </div>
                <button onclick="clearToken()" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded-lg transition-colors">
                    Clear
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden" style="height: calc(100vh - 80px);">
        <!-- Sidebar - Endpoints List -->
        <aside class="w-80 bg-gray-900 border-r border-gray-700 overflow-y-auto">
            <div class="p-4">
                <input type="text" 
                       id="searchQuery"
                       placeholder="Search endpoints..."
                       onkeyup="filterEndpoints()"
                       class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500">
            </div>
            
            <nav class="space-y-1 px-2" id="categoriesNav">
                <!-- Categories will be populated by JavaScript -->
            </nav>
        </aside>

        <!-- Main Panel -->
        <main class="flex-1 flex flex-col overflow-y-auto p-6 space-y-6">
            <!-- Request Panel -->
            <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <select id="selectedMethod" 
                            class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white font-bold focus:ring-2 focus:ring-red-500">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                        <option value="PATCH">PATCH</option>
                    </select>
                    <input type="text" 
                           id="selectedPath"
                           placeholder="/api/endpoint"
                           class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500">
                    <button onclick="sendRequest()" 
                            id="sendButton"
                            class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 flex items-center space-x-2">
                        <span class="material-icons text-sm" id="sendIcon">send</span>
                        <span id="sendText">Send</span>
                    </button>
                </div>
                
                <div id="endpointInfo" class="text-sm text-gray-400" style="display: none;">
                    <p><strong>Description:</strong> <span id="endpointDescription"></span></p>
                    <p class="mt-1"><strong>Authentication:</strong> <span id="endpointAuth"></span></p>
                </div>
            </div>

            <!-- Request Body -->
            <div id="requestBodySection" class="bg-gray-900 rounded-lg border border-gray-700 p-6" style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Request Body (JSON)</h3>
                    <button onclick="formatRequestBody()" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded transition-colors">
                        Format JSON
                    </button>
                </div>
                <textarea id="requestBody"
                          rows="10"
                          placeholder='{"key": "value"}'
                          class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 font-mono text-sm focus:ring-2 focus:ring-red-500"></textarea>
            </div>

            <!-- Query Parameters -->
            <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Query Parameters</h3>
                    <button onclick="addQueryParam()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition-colors flex items-center space-x-1">
                        <span class="material-icons text-sm">add</span>
                        <span>Add Parameter</span>
                    </button>
                </div>
                <div id="queryParams" class="space-y-2">
                    <!-- Query params will be populated by JavaScript -->
                </div>
            </div>

            <!-- Response Panel -->
            <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Response</h3>
                    <div id="responseStatus" class="flex items-center space-x-2" style="display: none;">
                        <span id="statusCode" class="font-bold"></span>
                        <span id="responseTime" class="text-gray-400"></span>
                    </div>
                </div>
                
                <div id="loadingState" class="flex items-center justify-center py-8" style="display: none;">
                    <div class="flex items-center space-x-2 text-gray-400">
                        <span class="material-icons animate-spin">refresh</span>
                        <span>Loading...</span>
                    </div>
                </div>
                
                <div id="responseContent" class="space-y-4" style="display: none;">
                    <!-- Response Body -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-400">Response Body</span>
                            <button onclick="copyResponse()" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded transition-colors flex items-center space-x-1">
                                <span class="material-icons text-sm">content_copy</span>
                                <span>Copy</span>
                            </button>
                        </div>
                        <pre id="responseBody" class="bg-gray-800 border border-gray-700 rounded-lg p-4 text-sm text-green-400 overflow-x-auto font-mono"></pre>
                    </div>
                    
                    <!-- Response Headers -->
                    <div id="responseHeadersSection" style="display: none;">
                        <span class="text-sm text-gray-400">Response Headers</span>
                        <div id="responseHeaders" class="mt-2 bg-gray-800 border border-gray-700 rounded-lg p-4 text-sm">
                            <!-- Headers will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
                
                <div id="emptyState" class="py-8 text-center text-gray-500">
                    <span class="material-icons text-4xl mb-2">api</span>
                    <p>Send a request to see the response</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        // API Endpoints Data
        const categories = [
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
        ];

        let queryParams = [];
        let selectedEndpoint = null;
        let tokenVisible = false;

        // Initialize the page
        function init() {
            renderCategories();
            loadSavedToken();
            addQueryParam(); // Add one empty query param by default
            
            // Expand first category
            const firstCategory = document.querySelector('.category-toggle');
            if (firstCategory) {
                toggleCategory(firstCategory.dataset.category);
            }
        }

        function renderCategories() {
            const nav = document.getElementById('categoriesNav');
            nav.innerHTML = '';
            
            categories.forEach(category => {
                const categoryDiv = document.createElement('div');
                categoryDiv.innerHTML = `
                    <div>
                        <button onclick="toggleCategory('${category.name}')" 
                                class="category-toggle w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 rounded-lg transition-colors"
                                data-category="${category.name}">
                            <span>${category.name}</span>
                            <span class="material-icons text-sm expand-icon">expand_more</span>
                        </button>
                        
                        <div class="category-section" id="category-${category.name.replace(/\s+/g, '-')}">
                            <div class="mt-1 space-y-1">
                                ${category.endpoints.map(endpoint => `
                                    <button onclick="selectEndpoint(${JSON.stringify(endpoint).replace(/"/g, '&quot;')})"
                                            class="endpoint-button w-full flex items-center space-x-2 px-3 py-2 text-left text-sm rounded-lg border border-transparent hover:bg-gray-800 transition-colors"
                                            data-method="${endpoint.method}"
                                            data-path="${endpoint.path}">
                                        <span class="method-${endpoint.method.toLowerCase()} px-2 py-0.5 text-xs font-bold rounded text-white">${endpoint.method}</span>
                                        <span class="text-gray-300 truncate">${endpoint.path}</span>
                                    </button>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                `;
                nav.appendChild(categoryDiv);
            });
        }

        function toggleCategory(categoryName) {
            const section = document.getElementById(`category-${categoryName.replace(/\s+/g, '-')}`);
            const icon = document.querySelector(`[data-category="${categoryName}"] .expand-icon`);
            
            if (section.classList.contains('expanded')) {
                section.classList.remove('expanded');
                icon.textContent = 'expand_more';
            } else {
                section.classList.add('expanded');
                icon.textContent = 'expand_less';
            }
        }

        function selectEndpoint(endpoint) {
            selectedEndpoint = endpoint;
            
            // Update form
            document.getElementById('selectedMethod').value = endpoint.method;
            document.getElementById('selectedPath').value = endpoint.path;
            
            // Update endpoint info
            document.getElementById('endpointInfo').style.display = 'block';
            document.getElementById('endpointDescription').textContent = endpoint.description;
            document.getElementById('endpointAuth').textContent = endpoint.auth ? 'Required' : 'Not Required';
            document.getElementById('endpointAuth').className = endpoint.auth ? 'text-yellow-400' : 'text-green-400';
            
            // Show/hide request body section
            const requestBodySection = document.getElementById('requestBodySection');
            if (['POST', 'PUT', 'PATCH'].includes(endpoint.method)) {
                requestBodySection.style.display = 'block';
                
                // Set default request body
                if (endpoint.method === 'POST' && endpoint.path.includes('login')) {
                    document.getElementById('requestBody').value = JSON.stringify({
                        email: 'admin@bazarino.store',
                        password: 'password'
                    }, null, 2);
                } else if (endpoint.method === 'POST' && endpoint.path.includes('register')) {
                    document.getElementById('requestBody').value = JSON.stringify({
                        name: 'Test User',
                        email: 'test@example.com',
                        password: 'password123',
                        password_confirmation: 'password123'
                    }, null, 2);
                } else {
                    document.getElementById('requestBody').value = '';
                }
            } else {
                requestBodySection.style.display = 'none';
            }
            
            // Update selected state
            document.querySelectorAll('.endpoint-button').forEach(btn => {
                btn.classList.remove('bg-red-500/20', 'border-red-500');
            });
            event.target.closest('.endpoint-button').classList.add('bg-red-500/20', 'border-red-500');
        }

        function filterEndpoints() {
            const query = document.getElementById('searchQuery').value.toLowerCase();
            
            categories.forEach(category => {
                const visibleEndpoints = category.endpoints.filter(endpoint => 
                    endpoint.path.toLowerCase().includes(query) ||
                    endpoint.description.toLowerCase().includes(query)
                );
                
                const categorySection = document.getElementById(`category-${category.name.replace(/\s+/g, '-')}`);
                const categoryButton = document.querySelector(`[data-category="${category.name}"]`);
                
                if (visibleEndpoints.length > 0) {
                    categoryButton.style.display = 'flex';
                    categorySection.innerHTML = `
                        <div class="mt-1 space-y-1">
                            ${visibleEndpoints.map(endpoint => `
                                <button onclick="selectEndpoint(${JSON.stringify(endpoint).replace(/"/g, '&quot;')})"
                                        class="endpoint-button w-full flex items-center space-x-2 px-3 py-2 text-left text-sm rounded-lg border border-transparent hover:bg-gray-800 transition-colors"
                                        data-method="${endpoint.method}"
                                        data-path="${endpoint.path}">
                                    <span class="method-${endpoint.method.toLowerCase()} px-2 py-0.5 text-xs font-bold rounded text-white">${endpoint.method}</span>
                                    <span class="text-gray-300 truncate">${endpoint.path}</span>
                                </button>
                            `).join('')}
                        </div>
                    `;
                } else {
                    categoryButton.style.display = 'none';
                }
            });
        }

        function toggleTokenVisibility() {
            tokenVisible = !tokenVisible;
            const tokenInput = document.getElementById('authToken');
            const icon = document.getElementById('tokenVisibilityIcon');
            
            tokenInput.type = tokenVisible ? 'text' : 'password';
            icon.textContent = tokenVisible ? 'visibility_off' : 'visibility';
        }

        function clearToken() {
            document.getElementById('authToken').value = '';
            localStorage.removeItem('api_tester_token');
        }

        function loadSavedToken() {
            const savedToken = localStorage.getItem('api_tester_token');
            if (savedToken) {
                document.getElementById('authToken').value = savedToken;
            }
        }

        function addQueryParam() {
            queryParams.push({ key: '', value: '' });
            renderQueryParams();
        }

        function removeQueryParam(index) {
            queryParams.splice(index, 1);
            renderQueryParams();
        }

        function renderQueryParams() {
            const container = document.getElementById('queryParams');
            container.innerHTML = queryParams.map((param, index) => `
                <div class="flex items-center space-x-2">
                    <input type="text" 
                           value="${param.key}"
                           onchange="updateQueryParam(${index}, 'key', this.value)"
                           placeholder="Key"
                           class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 text-sm focus:ring-2 focus:ring-red-500">
                    <input type="text" 
                           value="${param.value}"
                           onchange="updateQueryParam(${index}, 'value', this.value)"
                           placeholder="Value"
                           class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 text-sm focus:ring-2 focus:ring-red-500">
                    <button onclick="removeQueryParam(${index})" class="p-2 text-red-400 hover:text-red-300">
                        <span class="material-icons text-sm">delete</span>
                    </button>
                </div>
            `).join('');
        }

        function updateQueryParam(index, field, value) {
            queryParams[index][field] = value;
        }

        function formatRequestBody() {
            const textarea = document.getElementById('requestBody');
            try {
                const parsed = JSON.parse(textarea.value);
                textarea.value = JSON.stringify(parsed, null, 2);
            } catch (e) {
                alert('Invalid JSON format');
            }
        }

        async function sendRequest() {
            const path = document.getElementById('selectedPath').value;
            if (!path) {
                alert('Please select an endpoint');
                return;
            }
            
            // Save token
            const token = document.getElementById('authToken').value;
            if (token) {
                localStorage.setItem('api_tester_token', token);
            }
            
            // Update UI
            document.getElementById('loadingState').style.display = 'flex';
            document.getElementById('responseContent').style.display = 'none';
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('sendButton').disabled = true;
            document.getElementById('sendIcon').className = 'material-icons text-sm animate-spin';
            document.getElementById('sendText').textContent = 'Sending...';
            
            // Build query string
            const queryString = queryParams
                .filter(p => p.key && p.value)
                .map(p => `${encodeURIComponent(p.key)}=${encodeURIComponent(p.value)}`)
                .join('&');
            
            const url = path + (queryString ? '?' + queryString : '');
            
            const headers = {
                'Accept': 'application/json',
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }
            
            const method = document.getElementById('selectedMethod').value;
            const options = {
                method: method,
                headers: headers,
            };
            
            if (['POST', 'PUT', 'PATCH'].includes(method)) {
                const requestBody = document.getElementById('requestBody').value;
                if (requestBody) {
                    try {
                        JSON.parse(requestBody);
                        headers['Content-Type'] = 'application/json';
                        options.body = requestBody;
                    } catch (e) {
                        alert('Invalid JSON in request body');
                        resetSendButton();
                        return;
                    }
                }
            }
            
            const startTime = performance.now();
            
            try {
                const response = await fetch(url, options);
                const endTime = performance.now();
                
                const responseStatus = response.status;
                const responseTime = Math.round(endTime - startTime);
                
                // Get headers
                const headersObj = {};
                response.headers.forEach((value, key) => {
                    headersObj[key] = value;
                });
                
                // Get response body
                const contentType = response.headers.get('content-type');
                let responseData;
                if (contentType && contentType.includes('application/json')) {
                    responseData = await response.json();
                } else {
                    responseData = await response.text();
                }
                
                // Store token from login response
                if (path.includes('login') && responseData?.data?.token) {
                    document.getElementById('authToken').value = responseData.data.token;
                    localStorage.setItem('api_tester_token', responseData.data.token);
                }
                
                // Update UI with response
                document.getElementById('statusCode').textContent = responseStatus;
                document.getElementById('statusCode').className = responseStatus >= 200 && responseStatus < 300 ? 'font-bold text-green-400' : 'font-bold text-red-400';
                document.getElementById('responseTime').textContent = responseTime + 'ms';
                document.getElementById('responseStatus').style.display = 'flex';
                
                document.getElementById('responseBody').textContent = typeof responseData === 'object' ? JSON.stringify(responseData, null, 2) : responseData;
                
                // Display headers
                if (Object.keys(headersObj).length > 0) {
                    document.getElementById('responseHeadersSection').style.display = 'block';
                    document.getElementById('responseHeaders').innerHTML = Object.entries(headersObj)
                        .map(([key, value]) => `
                            <div class="flex justify-between py-1 border-b border-gray-700">
                                <span class="text-blue-400">${key}</span>
                                <span class="text-gray-300">${value}</span>
                            </div>
                        `).join('');
                }
                
                document.getElementById('responseContent').style.display = 'block';
                
            } catch (error) {
                document.getElementById('statusCode').textContent = 'Error';
                document.getElementById('statusCode').className = 'font-bold text-red-400';
                document.getElementById('responseTime').textContent = '';
                document.getElementById('responseStatus').style.display = 'flex';
                document.getElementById('responseBody').textContent = JSON.stringify({ error: error.message }, null, 2);
                document.getElementById('responseContent').style.display = 'block';
            } finally {
                resetSendButton();
            }
        }

        function resetSendButton() {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('sendButton').disabled = false;
            document.getElementById('sendIcon').className = 'material-icons text-sm';
            document.getElementById('sendText').textContent = 'Send';
        }

        function copyResponse() {
            const responseText = document.getElementById('responseBody').textContent;
            navigator.clipboard.writeText(responseText);
            alert('Response copied to clipboard!');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
