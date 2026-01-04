@extends('layouts.admin')

@section('title', 'User Management')
@section('header', 'User Management')

@section('content')
<div x-data="userManagement()" x-init="fetchUsers()">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <input type="text" 
                       x-model="search" 
                       @input.debounce.300ms="fetchUsers()"
                       placeholder="Search users..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <select x-model="status" @change="fetchUsers()" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="verified">Verified</option>
                <option value="admin">Admins</option>
            </select>
            
            <button @click="exportUsers()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <span class="material-icons text-sm mr-1">download</span>
                Export
            </button>
            
            <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <span class="material-icons text-sm mr-1">add</span>
                Add User
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600" x-text="userStats.total || 0"></p>
            <p class="text-sm text-gray-500">Total</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-green-600" x-text="userStats.active || 0"></p>
            <p class="text-sm text-gray-500">Active</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-purple-600" x-text="userStats.verified || 0"></p>
            <p class="text-sm text-gray-500">Verified</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-red-600" x-text="userStats.inactive || 0"></p>
            <p class="text-sm text-gray-500">Inactive</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Listings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="user in users" :key="user.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="material-icons text-gray-500">person</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                    <div class="text-sm text-gray-500" x-text="user.location || 'No location'"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900" x-text="user.email"></div>
                            <div class="text-sm text-gray-500" x-text="user.phone || 'No phone'"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full"
                                  :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                  x-text="user.is_active ? 'Active' : 'Inactive'"></span>
                            <span x-show="user.is_verified" class="ml-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Verified</span>
                            <span x-show="user.is_admin" class="ml-1 px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800" x-text="user.admin_role"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.active_listings"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="viewUser(user)" class="text-blue-600 hover:text-blue-900 mr-2">View</button>
                            <button x-show="user.is_active" @click="suspendUser(user.id)" class="text-yellow-600 hover:text-yellow-900 mr-2">Suspend</button>
                            <button x-show="!user.is_active" @click="activateUser(user.id)" class="text-green-600 hover:text-green-900 mr-2">Activate</button>
                            <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Showing <span x-text="pagination.from"></span> to <span x-text="pagination.to"></span> of <span x-text="pagination.total"></span> users
            </div>
            <div class="flex space-x-2">
                <button @click="prevPage()" :disabled="page === 1" class="px-3 py-1 border rounded disabled:opacity-50">Previous</button>
                <button @click="nextPage()" :disabled="page >= pagination.total_pages" class="px-3 py-1 border rounded disabled:opacity-50">Next</button>
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
        showCreateModal: false,
        
        async fetchUsers() {
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    limit: 20,
                    search: this.search,
                    status: this.status
                });
                
                const response = await fetch(`/api/admin/users?${params}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.users = data.data.users;
                    this.pagination = data.data.pagination;
                    this.userStats = data.data.stats;
                }
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        },
        
        async suspendUser(id) {
            if (!confirm('Are you sure you want to suspend this user?')) return;
            await this.userAction(id, 'suspend');
        },
        
        async activateUser(id) {
            await this.userAction(id, 'activate');
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
        
        async userAction(id, action) {
            try {
                await fetch(`/api/admin/users/${id}/${action}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
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
            return new Date(dateString).toLocaleDateString();
        }
    }
}
</script>
@endpush
@endsection
