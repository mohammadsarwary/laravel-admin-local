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
        <div class="flex flex-wrap gap-4 items-center justify-between">
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
            
            <div x-show="selectedItems.length > 0" x-transition class="flex items-center space-x-2">
                <span class="text-sm text-gray-400" x-text="selectedItems.length + ' selected'"></span>
                <button @click="bulkAction('activate')" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm">
                    Activate
                </button>
                <button @click="bulkAction('suspend')" class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors text-sm">
                    Suspend
                </button>
                <button @click="bulkAction('ban')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">
                    Ban
                </button>
                <button @click="clearSelection()" class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors text-sm">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card-dark rounded-lg border border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-800/50 border-b border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <input type="checkbox" 
                               :checked="allSelected"
                               @change="toggleSelectAll()"
                               class="rounded">
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
                            <input type="checkbox" 
                                   :checked="selectedItems.includes(user.id)"
                                   @change="toggleSelect(user.id)"
                                   class="rounded">
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
                <template x-for="pageNum in getPageNumbers()" :key="pageNum">
                    <template x-if="pageNum === '...'">
                        <span class="text-gray-400 px-2" x-text="pageNum"></span>
                    </template>
                    <template x-if="pageNum !== '...'">
                        <button 
                            @click="goToPage(pageNum)"
                            :class="page === pageNum ? 'bg-red-500 text-white' : 'bg-gray-700 hover:bg-gray-600 text-white'"
                            class="px-3 py-1 rounded transition-colors text-sm"
                            x-text="pageNum">
                        </button>
                    </template>
                </template>
                <button @click="nextPage()" :disabled="page >= pagination.total_pages" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded transition-colors text-sm">Next</button>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showCreateModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-md w-full mx-4 shadow-xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Add New User</h3>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                    <input type="text" 
                           x-model="createForm.name"
                           placeholder="Enter full name"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                    <input type="email" 
                           x-model="createForm.email"
                           placeholder="Enter email address"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone</label>
                    <input type="tel" 
                           x-model="createForm.phone"
                           placeholder="Enter phone number"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Location</label>
                    <input type="text" 
                           x-model="createForm.location"
                           placeholder="Enter location"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Role *</label>
                    <select x-model="createForm.role" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">Select a role</option>
                        <option value="buyer">Buyer</option>
                        <option value="seller">Seller</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status *</label>
                    <select x-model="createForm.is_active" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">Select a status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div x-show="createForm.error" class="p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
                    <span x-text="createForm.error"></span>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-700">
                <button @click="showCreateModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancel
                </button>
                <button @click="createUser()" :disabled="createForm.loading" class="px-4 py-2 bg-red-500 hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors flex items-center space-x-2">
                    <span x-show="!createForm.loading">Create User</span>
                    <span x-show="createForm.loading">Creating...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div x-show="showViewModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showViewModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900">
                <h3 class="text-lg font-semibold text-white">User Details</h3>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4" x-show="!viewModalLoading">
                <div class="flex items-start space-x-4 mb-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold text-2xl flex-shrink-0">
                        <span x-text="viewUser.name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-semibold text-white" x-text="viewUser.name"></h4>
                        <p class="text-gray-400" x-text="viewUser.email"></p>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="px-3 py-1 text-xs font-medium rounded-full"
                                  :class="{
                                      'bg-blue-500/20 text-blue-400': viewUser.role === 'seller',
                                      'bg-purple-500/20 text-purple-400': viewUser.role === 'buyer',
                                      'bg-red-500/20 text-red-400': viewUser.role === 'admin'
                                  }"
                                  x-text="viewUser.role ? viewUser.role.charAt(0).toUpperCase() + viewUser.role.slice(1) : 'Buyer'"></span>
                            <span class="px-3 py-1 text-xs font-medium rounded-full"
                                  :class="{
                                      'bg-green-500/20 text-green-400': viewUser.is_active,
                                      'bg-yellow-500/20 text-yellow-400': !viewUser.is_active && viewUser.is_verified,
                                      'bg-red-500/20 text-red-400': !viewUser.is_active && !viewUser.is_verified
                                  }"
                                  x-text="viewUser.is_active ? 'Active' : (viewUser.is_verified ? 'Pending' : 'Banned')"></span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Phone</p>
                        <p class="text-white" x-text="viewUser.phone || 'Not provided'"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Location</p>
                        <p class="text-white" x-text="viewUser.location || 'Not provided'"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Rating</p>
                        <p class="text-white" x-text="viewUser.rating || 'N/A'"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Active Listings</p>
                        <p class="text-white" x-text="viewUser.active_listings || '0'"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Joined</p>
                        <p class="text-white" x-text="formatDate(viewUser.created_at)"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Last Login</p>
                        <p class="text-white" x-text="viewUser.last_login ? formatDate(viewUser.last_login) : 'Never'"></p>
                    </div>
                </div>
                
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                    <p class="text-sm text-gray-400 mb-2">Verification Status</p>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full"
                              :class="viewUser.is_verified ? 'bg-green-500' : 'bg-red-500'"></span>
                        <span class="text-white" x-text="viewUser.is_verified ? 'Verified' : 'Not Verified'"></span>
                    </div>
                </div>
            </div>
            
            <!-- Loading State -->
            <div class="px-6 py-8 flex items-center justify-center" x-show="viewModalLoading">
                <div class="text-gray-400">Loading user details...</div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-700 sticky bottom-0 bg-gray-900">
                <button @click="showViewModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showEditModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-md w-full mx-4 shadow-xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Edit User</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                    <input type="text" 
                           x-model="editForm.name"
                           placeholder="Enter full name"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                    <input type="email" 
                           x-model="editForm.email"
                           placeholder="Enter email address"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone</label>
                    <input type="tel" 
                           x-model="editForm.phone"
                           placeholder="Enter phone number"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Location</label>
                    <input type="text" 
                           x-model="editForm.location"
                           placeholder="Enter location"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Role *</label>
                    <select x-model="editForm.role" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">Select a role</option>
                        <option value="buyer">Buyer</option>
                        <option value="seller">Seller</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status *</label>
                    <select x-model="editForm.is_active" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">Select a status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div x-show="editForm.error" class="p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
                    <span x-text="editForm.error"></span>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-700">
                <button @click="showEditModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancel
                </button>
                <button @click="updateUser()" :disabled="editForm.loading" class="px-4 py-2 bg-red-500 hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors flex items-center space-x-2">
                    <span x-show="!editForm.loading">Update User</span>
                    <span x-show="editForm.loading">Updating...</span>
                </button>
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
        selectedItems: [],
        showViewModal: false,
        showEditModal: false,
        viewUser: {},
        editUser: {},
        viewModalLoading: false,
        get allSelected() {
            return this.users.length > 0 && this.selectedItems.length === this.users.length;
        },
        createForm: {
            name: '',
            email: '',
            phone: '',
            location: '',
            role: '',
            is_active: '',
            loading: false,
            error: ''
        },
        editForm: {
            id: null,
            name: '',
            email: '',
            phone: '',
            location: '',
            role: '',
            is_active: '',
            loading: false,
            error: ''
        },
        
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
        
        async viewUser(user) {
            this.showViewModal = true;
            this.viewModalLoading = true;
            this.viewUser = user;
            
            try {
                const response = await fetch(`/api/admin/users/${user.id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.viewUser = data.data;
                }
            } catch (error) {
                console.error('Error fetching user details:', error);
            } finally {
                this.viewModalLoading = false;
            }
        },
        
        editUser(user) {
            this.showEditModal = true;
            this.editForm = {
                id: user.id,
                name: user.name,
                email: user.email,
                phone: user.phone || '',
                location: user.location || '',
                role: user.role || 'buyer',
                is_active: user.is_active ? '1' : '0',
                loading: false,
                error: ''
            };
        },
        
        async updateUser() {
            this.editForm.error = '';
            
            if (!this.editForm.name.trim()) {
                this.editForm.error = 'Full name is required';
                return;
            }
            
            if (!this.editForm.email.trim()) {
                this.editForm.error = 'Email is required';
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.editForm.email)) {
                this.editForm.error = 'Please enter a valid email address';
                return;
            }
            
            if (!this.editForm.role) {
                this.editForm.error = 'Role is required';
                return;
            }
            
            if (this.editForm.is_active === '') {
                this.editForm.error = 'Status is required';
                return;
            }
            
            this.editForm.loading = true;
            
            try {
                const response = await fetch(`/api/admin/users/${this.editForm.id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.editForm.name,
                        email: this.editForm.email,
                        phone: this.editForm.phone || null,
                        location: this.editForm.location || null,
                        role: this.editForm.role,
                        is_active: parseInt(this.editForm.is_active)
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showEditModal = false;
                    this.fetchUsers();
                } else {
                    this.editForm.error = data.message || 'Failed to update user';
                }
            } catch (error) {
                console.error('Error updating user:', error);
                this.editForm.error = 'An error occurred while updating the user';
            } finally {
                this.editForm.loading = false;
            }
        },
        
        exportUsers() {
            window.location.href = '/api/admin/users/export';
        },
        
        toggleSelect(userId) {
            const index = this.selectedItems.indexOf(userId);
            if (index === -1) {
                this.selectedItems.push(userId);
            } else {
                this.selectedItems.splice(index, 1);
            }
        },
        
        toggleSelectAll() {
            if (this.allSelected) {
                this.selectedItems = [];
            } else {
                this.selectedItems = this.users.map(user => user.id);
            }
        },
        
        clearSelection() {
            this.selectedItems = [];
        },
        
        async bulkAction(action) {
            if (this.selectedItems.length === 0) return;
            
            const actionText = action.charAt(0).toUpperCase() + action.slice(1);
            if (!confirm(`Are you sure you want to ${actionText} ${this.selectedItems.length} user(s)?`)) return;
            
            try {
                const response = await fetch('/api/admin/users/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: action,
                        user_ids: this.selectedItems
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.selectedItems = [];
                    this.fetchUsers();
                } else {
                    alert(data.message || 'Failed to perform bulk action');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while performing the bulk action');
            }
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
        
        goToPage(pageNum) {
            if (pageNum !== this.page && pageNum >= 1 && pageNum <= this.pagination.total_pages) {
                this.page = pageNum;
                this.fetchUsers();
            }
        },
        
        getPageNumbers() {
            const totalPages = this.pagination.total_pages || 1;
            const currentPage = this.page;
            const pages = [];
            
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) {
                    pages.push(i);
                }
            } else {
                if (currentPage <= 4) {
                    for (let i = 1; i <= 5; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(totalPages);
                } else if (currentPage >= totalPages - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = totalPages - 4; i <= totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = currentPage - 1; i <= currentPage + 1; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(totalPages);
                }
            }
            
            return pages;
        },
        
        async createUser() {
            this.createForm.error = '';
            
            if (!this.createForm.name.trim()) {
                this.createForm.error = 'Full name is required';
                return;
            }
            
            if (!this.createForm.email.trim()) {
                this.createForm.error = 'Email is required';
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.createForm.email)) {
                this.createForm.error = 'Please enter a valid email address';
                return;
            }
            
            if (!this.createForm.role) {
                this.createForm.error = 'Role is required';
                return;
            }
            
            if (this.createForm.is_active === '') {
                this.createForm.error = 'Status is required';
                return;
            }
            
            this.createForm.loading = true;
            
            try {
                const response = await fetch('/api/admin/users/create', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.createForm.name,
                        email: this.createForm.email,
                        phone: this.createForm.phone || null,
                        location: this.createForm.location || null,
                        role: this.createForm.role,
                        is_active: parseInt(this.createForm.is_active)
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showCreateModal = false;
                    this.createForm = {
                        name: '',
                        email: '',
                        phone: '',
                        location: '',
                        role: '',
                        is_active: '',
                        loading: false,
                        error: ''
                    };
                    this.fetchUsers();
                } else {
                    this.createForm.error = data.message || 'Failed to create user';
                }
            } catch (error) {
                console.error('Error creating user:', error);
                this.createForm.error = 'An error occurred while creating the user';
            } finally {
                this.createForm.loading = false;
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
