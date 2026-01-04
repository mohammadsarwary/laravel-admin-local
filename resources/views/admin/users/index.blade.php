@extends('layouts.admin')

@section('title', 'User Management')
@section('header', 'User Management')

@section('content')
<div x-data="userManagement()" x-init="fetchUsers()">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-gray-400 text-sm">View, search, and manage verified user accounts and marketplace sellers.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportUsers()" class="flex items-center space-x-2 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                <span class="material-icons text-sm">download</span>
                <span>Export</span>
            </button>
            <button @click="showCreateModal = true" class="flex items-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                <span class="material-icons text-sm">add</span>
                <span>Add New User</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-white" x-text="userStats.total || '24,593'"></p>
            <p class="text-sm text-gray-400 mt-1">Total Users</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-white" x-text="userStats.new_today || '+145'"></p>
            <p class="text-sm text-gray-400 mt-1">New Today</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-white" x-text="userStats.verified || '8,420'"></p>
            <p class="text-sm text-gray-400 mt-1">Verified Sellers</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-white" x-text="userStats.banned || '542'"></p>
            <p class="text-sm text-gray-400 mt-1">Banned</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark rounded-lg p-4 border border-gray-700 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <input type="text" 
                       x-model="search" 
                       @input.debounce.300ms="fetchUsers()"
                       placeholder="Search by name, email, or phone..."
                       class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
            </div>
            
            <select x-model="status" @change="fetchUsers()" class="px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="">Status: All</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="banned">Banned</option>
            </select>
            
            <select x-model="role" @change="fetchUsers()" class="px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="">Role: All</option>
                <option value="seller">Seller</option>
                <option value="buyer">Buyer</option>
                <option value="admin">Admin</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card-dark rounded-lg border border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-800/50 border-b border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <input type="checkbox" class="rounded">
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact Info</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <template x-for="user in users" :key="user.id">
                    <tr class="hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white" x-text="user.name"></p>
                                    <p class="text-xs text-gray-400" x-text="'@' + user.username"></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-white" x-text="user.email"></div>
                            <div class="text-xs text-gray-400" x-text="user.phone || 'No phone'"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-medium rounded-full"
                                  :class="{
                                      'bg-blue-500/20 text-blue-400': user.role === 'seller',
                                      'bg-purple-500/20 text-purple-400': user.role === 'buyer',
                                      'bg-red-500/20 text-red-400': user.role === 'admin'
                                  }"
                                  x-text="user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'Buyer'"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full"
                                      :class="{
                                          'bg-green-500': user.is_active,
                                          'bg-yellow-500': !user.is_active && user.is_verified,
                                          'bg-red-500': !user.is_active && !user.is_verified
                                      }"></span>
                                <span class="text-sm"
                                      :class="{
                                          'text-green-400': user.is_active,
                                          'text-yellow-400': !user.is_active && user.is_verified,
                                          'text-red-400': !user.is_active && !user.is_verified
                                      }"
                                      x-text="user.is_active ? 'Active' : (user.is_verified ? 'Pending' : 'Banned')"></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="formatDate(user.created_at)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button @click="viewUser(user)" class="p-2 hover:bg-gray-700 rounded-lg transition-colors text-gray-400 hover:text-white" title="View">
                                    <span class="material-icons text-sm">visibility</span>
                                </button>
                                <button @click="editUser(user)" class="p-2 hover:bg-gray-700 rounded-lg transition-colors text-gray-400 hover:text-white" title="Edit">
                                    <span class="material-icons text-sm">edit</span>
                                </button>
                                <button @click="deleteUser(user.id)" class="p-2 hover:bg-red-900/30 rounded-lg transition-colors text-gray-400 hover:text-red-400" title="Delete">
                                    <span class="material-icons text-sm">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="bg-gray-800/50 px-6 py-4 border-t border-gray-700 flex items-center justify-between">
            <div class="text-sm text-gray-400">
                Showing 1 to 5 of <span x-text="pagination.total || '24,593'"></span> results
            </div>
            <div class="flex items-center space-x-2">
                <button @click="prevPage()" :disabled="page === 1" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded transition-colors text-sm">Previous</button>
                <button class="px-3 py-1 bg-red-500 text-white rounded text-sm">1</button>
                <button class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded transition-colors text-sm">2</button>
                <button class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded transition-colors text-sm">3</button>
                <span class="text-gray-400">...</span>
                <button class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded transition-colors text-sm">48</button>
                <button @click="nextPage()" :disabled="page >= pagination.total_pages" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded transition-colors text-sm">Next</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function userManagement() {
    return {
        users: [],
        userStats: {},
        pagination: {},
        page: 1,
        search: '',
        status: '',
        role: '',
        showCreateModal: false,
        
        async fetchUsers() {
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    limit: 20,
                    search: this.search,
                    status: this.status,
                    role: this.role
                });
                
                const response = await fetch(`/api/admin/users?${params}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.users = data.data.users || [];
                    this.pagination = data.data.pagination || {};
                    this.userStats = data.data.stats || {};
                }
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        },
        
        async deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
            try {
                await fetch(`/api/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                this.fetchUsers();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        viewUser(user) {
            alert('View user: ' + user.name);
        },
        
        editUser(user) {
            alert('Edit user: ' + user.name);
        },
        
        exportUsers() {
            window.location.href = '/api/admin/users/export';
        },
        
        prevPage() {
            if (this.page > 1) {
                this.page--;
                this.fetchUsers();
            }
        },
        
        nextPage() {
            if (this.page < this.pagination.total_pages) {
                this.page++;
                this.fetchUsers();
            }
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    }
}
</script>
@endpush
@endsection
