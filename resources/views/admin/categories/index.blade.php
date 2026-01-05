@extends('layouts.admin')

@section('title', 'Categories Management')
@section('header', 'Categories')

@section('content')
<div x-data="categoryManagement()" x-init="fetchCategories()">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-gray-400 text-sm">Manage product categories and subcategories for your marketplace.</p>
        </div>
        <div class="flex items-center space-x-4">
            <div x-data="{ exportOpen: false, format: 'csv' }" class="relative">
                <button @click="exportOpen = !exportOpen" class="flex items-center space-x-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <span class="material-icons text-sm">download</span>
                    <span>Export</span>
                    <span class="material-icons text-sm">expand_more</span>
                </button>
                <div x-show="exportOpen" @click.away="exportOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-10">
                    <div class="p-2">
                        <p class="text-xs text-gray-400 mb-2 px-2">Format</p>
                        <button @click="format = 'csv'; exportCategories(format); exportOpen = false" 
                                class="w-full text-left px-3 py-2 text-sm text-white hover:bg-gray-700 rounded transition-colors flex items-center">
                            <span class="material-icons text-sm mr-2">description</span>
                            CSV
                        </button>
                    </div>
                </div>
            </div>
            <button @click="fetchCategories()" :disabled="loading" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50">
                <span class="material-icons text-sm" :class="{ 'animate-spin': loading }">refresh</span>
                <span>Refresh</span>
            </button>
            <button @click="openCreateModal()" class="flex items-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                <span class="material-icons text-sm">add</span>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Error Alert -->
        <div x-show="error" x-transition class="col-span-full bg-red-500/20 border border-red-500/50 rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center">
                <span class="material-icons text-red-400 mr-3">error</span>
                <p class="text-red-400" x-text="error"></p>
            </div>
            <button @click="error = ''" class="text-red-400 hover:text-white">
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-white" x-text="stats.total || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Total Categories</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-green-400" x-text="stats.active || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Active</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-gray-400" x-text="stats.inactive || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Inactive</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-purple-400" x-text="stats.parent || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Parent Categories</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark rounded-lg p-4 border border-gray-700 mb-6">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="fetchCategories()"
                           placeholder="Search categories..."
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500">
                </div>
                
                <select x-model="statusFilter" @change="fetchCategories()" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-red-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <!-- Bulk Actions -->
            <div x-show="selectedCategories.length > 0" class="flex items-center space-x-2">
                <span class="text-sm text-gray-400" x-text="`${selectedCategories.length} selected`"></span>
                <button @click="bulkAction('activate')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition-colors">
                    Activate
                </button>
                <button @click="bulkAction('deactivate')" class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded transition-colors">
                    Deactivate
                </button>
                <button @click="bulkAction('delete')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card-dark rounded-lg border border-gray-700 overflow-hidden relative">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-gray-900/80 flex items-center justify-center z-10">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-gray-400 mt-4">Loading categories...</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" 
                                   @change="toggleSelectAll()"
                                   :checked="selectedCategories.length === categories.length && categories.length > 0"
                                   class="rounded border-gray-600 bg-gray-700 text-red-500 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('id')">
                            ID
                            <span x-show="sortBy === 'id'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('name')">
                            Name
                            <span x-show="sortBy === 'name'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">
                            Icon
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">
                            Parent
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">
                            Ads Count
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('display_order')">
                            Order
                            <span x-show="sortBy === 'display_order'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    <!-- Empty State -->
                    <tr x-show="!loading && categories.length === 0">
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-icons text-gray-600 text-5xl mb-4">category</span>
                                <p class="text-gray-400 text-lg mb-2">No categories found</p>
                                <p class="text-gray-500 text-sm mb-4">Try adjusting your search or create a new category</p>
                                <button @click="search = ''; statusFilter = ''; fetchCategories()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <template x-for="category in categories" :key="category.id">
                        <tr class="hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                       :value="category.id"
                                       @change="toggleSelect(category.id)"
                                       :checked="selectedCategories.includes(category.id)"
                                       class="rounded border-gray-600 bg-gray-700 text-red-500 focus:ring-red-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="category.id"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white" x-text="category.name"></div>
                                <div class="text-sm text-gray-400" x-text="category.slug"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span x-show="category.icon" class="material-icons text-gray-300" x-text="category.icon"></span>
                                <span x-show="!category.icon" class="text-gray-500 text-sm">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span x-show="category.parent_name" class="text-sm text-gray-300" x-text="category.parent_name"></span>
                                <span x-show="!category.parent_name" class="text-gray-500 text-sm">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="category.ads_count || 0"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="category.display_order"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-medium"
                                      :class="category.is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                                      x-text="category.is_active ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button @click="viewCategory(category)" class="text-blue-400 hover:text-blue-300 transition-colors">
                                    <span class="material-icons text-sm">visibility</span>
                                </button>
                                <button @click="editCategory(category)" class="text-yellow-400 hover:text-yellow-300 transition-colors">
                                    <span class="material-icons text-sm">edit</span>
                                </button>
                                <button @click="toggleStatus(category)" class="text-purple-400 hover:text-purple-300 transition-colors">
                                    <span class="material-icons text-sm" x-text="category.is_active ? 'toggle_on' : 'toggle_off'"></span>
                                </button>
                                <button @click="deleteCategory(category.id)" class="text-red-400 hover:text-red-300 transition-colors">
                                    <span class="material-icons text-sm">delete</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-800 px-6 py-3 flex items-center justify-between border-t border-gray-700">
            <div class="text-sm text-gray-400">
                Showing <span x-text="categories.length"></span> of <span x-text="pagination.total || 0"></span> categories
            </div>
            <div class="flex items-center space-x-2">
                <button @click="prevPage()" :disabled="page === 1" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Previous
                </button>
                <template x-for="pageNum in getPageNumbers()" :key="pageNum">
                    <button @click="goToPage(pageNum)" 
                            :class="pageNum === page ? 'bg-red-500 text-white' : 'bg-gray-700 hover:bg-gray-600 text-white'"
                            class="px-3 py-1 rounded transition-colors"
                            x-text="pageNum"></button>
                </template>
                <button @click="nextPage()" :disabled="page >= pagination.pages" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- Create/Edit Category Modal -->
    <div x-show="showCreateModal || showEditModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900 z-10">
                <h3 class="text-lg font-semibold text-white" x-text="showEditModal ? 'Edit Category' : 'Create Category'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="submitForm()" class="px-6 py-4">
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           x-model="form.name"
                           required
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                           placeholder="Enter category name">
                    <p x-show="formErrors.name" class="text-red-400 text-xs mt-1" x-text="formErrors.name"></p>
                </div>
                
                <!-- Icon -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Icon (Material Icons)</label>
                    <div class="flex items-center space-x-2">
                        <input type="text"
                               x-model="form.icon"
                               class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                               placeholder="e.g., smartphone, home, directions_car">
                        <span x-show="form.icon" class="material-icons text-gray-300 text-2xl" x-text="form.icon"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Visit <a href="https://fonts.google.com/icons" target="_blank" class="text-blue-400 hover:underline">Material Icons</a> for icon names</p>
                </div>
                
                <!-- Parent Category -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Parent Category</label>
                    <select x-model="form.parent_id" class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                        <option value="">None (Top Level)</option>
                        <template x-for="cat in parentCategories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>
                </div>
                
                <!-- Display Order -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Display Order</label>
                    <input type="number"
                           x-model="form.display_order"
                           min="0"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>
                
                <!-- Status -->
                <div class="mb-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox"
                               x-model="form.is_active"
                               class="rounded border-gray-600 bg-gray-700 text-red-500 focus:ring-red-500">
                        <span class="text-sm text-gray-300">Active</span>
                    </label>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-700">
                    <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" :disabled="formLoading" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                        <span class="material-icons text-sm" :class="{ 'animate-spin': formLoading }">
                            <span x-text="formLoading ? 'refresh' : (showEditModal ? 'save' : 'add')"></span>
                        </span>
                        <span x-text="formLoading ? 'Saving...' : (showEditModal ? 'Update' : 'Create')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Category Modal -->
    <div x-show="showViewModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showViewModal = false" @keydown.escape.window="showViewModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900">
                <h3 class="text-lg font-semibold text-white">Category Details</h3>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4" x-show="!viewModalLoading && selectedCategory">
                <!-- Name and Icon -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <span x-show="selectedCategory.icon" class="material-icons text-red-400 text-3xl" x-text="selectedCategory.icon"></span>
                        <div>
                            <h4 class="text-xl font-semibold text-white" x-text="selectedCategory.name"></h4>
                            <p class="text-gray-400 text-sm" x-text="selectedCategory.slug"></p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                          :class="selectedCategory.is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                          x-text="selectedCategory.is_active ? 'Active' : 'Inactive'"></span>
                </div>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Total Ads</p>
                        <p class="text-white font-semibold text-2xl" x-text="selectedCategory.ads_count || '0'"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Display Order</p>
                        <p class="text-white font-semibold text-2xl" x-text="selectedCategory.display_order"></p>
                    </div>
                </div>
                
                <!-- Parent Category -->
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4" x-show="selectedCategory.parent_name">
                    <p class="text-sm font-medium text-gray-400 mb-2">Parent Category</p>
                    <p class="text-white" x-text="selectedCategory.parent_name"></p>
                </div>
                
                <!-- Subcategories -->
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4" x-show="selectedCategory.children && selectedCategory.children.length > 0">
                    <p class="text-sm font-medium text-gray-400 mb-2">Subcategories</p>
                    <div class="space-y-2">
                        <template x-for="child in selectedCategory.children" :key="child.id">
                            <div class="flex items-center space-x-2 text-white">
                                <span class="material-icons text-sm text-gray-400">subdirectory_arrow_right</span>
                                <span x-text="child.name"></span>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Created</p>
                        <p class="text-white" x-text="formatDate(selectedCategory.created_at)"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Last Updated</p>
                        <p class="text-white" x-text="formatDate(selectedCategory.updated_at)"></p>
                    </div>
                </div>
            </div>
            
            <!-- Loading State -->
            <div class="px-6 py-8 flex items-center justify-center" x-show="viewModalLoading">
                <div class="text-gray-400">Loading category details...</div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-700 sticky bottom-0 bg-gray-900">
                <button @click="showViewModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showDeleteModal = false" @keydown.escape.window="showDeleteModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-md w-full mx-4 shadow-xl">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Confirm Deletion</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-300">Are you sure you want to delete this category? This action cannot be undone.</p>
                <p class="text-sm text-gray-500 mt-2" x-show="deletingCategory" x-text="'Deleting: ' + deletingCategory.name"></p>
                <p class="text-sm text-yellow-400 mt-2" x-show="deletingCategory && deletingCategory.ads_count > 0">
                    ⚠️ This category has <span x-text="deletingCategory.ads_count"></span> ads. Please reassign them first.
                </p>
            </div>
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-700">
                <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancel
                </button>
                <button @click="confirmDelete()" :disabled="formLoading" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors disabled:opacity-50">
                    <span x-text="formLoading ? 'Deleting...' : 'Delete'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function categoryManagement() {
    return {
        categories: [],
        parentCategories: [],
        stats: { total: 0, active: 0, inactive: 0, parent: 0 },
        pagination: {},
        page: 1,
        search: '',
        statusFilter: '',
        sortBy: 'display_order',
        sortOrder: 'asc',
        loading: false,
        error: '',
        
        selectedCategories: [],
        showCreateModal: false,
        showEditModal: false,
        showViewModal: false,
        showDeleteModal: false,
        
        form: {
            name: '',
            icon: '',
            parent_id: '',
            display_order: 0,
            is_active: true
        },
        formErrors: {},
        formLoading: false,
        
        selectedCategory: null,
        deletingCategory: null,
        viewModalLoading: false,
        
        init() {
            this.fetchParentCategories();
        },
        
        async fetchCategories() {
            this.loading = true;
            this.error = '';
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    limit: 20,
                    search: this.search,
                    status: this.statusFilter
                });
                
                const response = await fetch(`/api/admin/categories?${params}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch categories');
                }
                
                const data = await response.json();
                if (data.success) {
                    this.categories = data.data.categories;
                    this.pagination = data.data.pagination;
                    this.calculateStats();
                } else {
                    throw new Error(data.message || 'Failed to fetch categories');
                }
            } catch (error) {
                this.error = error.message;
                console.error('Error fetching categories:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async fetchParentCategories() {
            try {
                const response = await fetch('/api/admin/categories?limit=100', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.parentCategories = data.data.categories.filter(c => !c.parent_id);
                }
            } catch (error) {
                console.error('Error fetching parent categories:', error);
            }
        },
        
        calculateStats() {
            this.stats.total = this.pagination.total || 0;
            this.stats.active = this.categories.filter(c => c.is_active).length;
            this.stats.inactive = this.categories.filter(c => !c.is_active).length;
            this.stats.parent = this.categories.filter(c => !c.parent_id).length;
        },
        
        toggleSort(column) {
            if (this.sortBy === column) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortOrder = 'asc';
            }
            this.fetchCategories();
        },
        
        toggleSelect(categoryId) {
            const index = this.selectedCategories.indexOf(categoryId);
            if (index > -1) {
                this.selectedCategories.splice(index, 1);
            } else {
                this.selectedCategories.push(categoryId);
            }
        },
        
        toggleSelectAll() {
            if (this.selectedCategories.length === this.categories.length) {
                this.selectedCategories = [];
            } else {
                this.selectedCategories = this.categories.map(c => c.id);
            }
        },
        
        async bulkAction(action) {
            if (this.selectedCategories.length === 0) return;
            
            if (!confirm(`Are you sure you want to ${action} ${this.selectedCategories.length} categories?`)) return;
            
            try {
                const response = await fetch('/api/admin/categories/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: action,
                        category_ids: this.selectedCategories
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    this.selectedCategories = [];
                    this.fetchCategories();
                } else {
                    throw new Error(data.message || 'Bulk action failed');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            }
        },
        
        openCreateModal() {
            this.resetForm();
            this.showCreateModal = true;
        },
        
        editCategory(category) {
            this.form.name = category.name;
            this.form.icon = category.icon || '';
            this.form.parent_id = category.parent_id || '';
            this.form.display_order = category.display_order;
            this.form.is_active = category.is_active;
            this.selectedCategory = category;
            this.showEditModal = true;
        },
        
        async viewCategory(category) {
            this.showViewModal = true;
            this.viewModalLoading = true;
            this.selectedCategory = category;
            
            try {
                const response = await fetch(`/api/admin/categories/${category.id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.selectedCategory = data.data;
                }
            } catch (error) {
                console.error('Error fetching category details:', error);
            } finally {
                this.viewModalLoading = false;
            }
        },
        
        deleteCategory(categoryId) {
            const category = this.categories.find(c => c.id === categoryId);
            this.deletingCategory = category;
            this.showDeleteModal = true;
        },
        
        async confirmDelete() {
            if (!this.deletingCategory) return;
            
            this.formLoading = true;
            try {
                const response = await fetch(`/api/admin/categories/${this.deletingCategory.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast('Category deleted successfully', 'success');
                    this.showDeleteModal = false;
                    this.deletingCategory = null;
                    this.fetchCategories();
                    this.fetchParentCategories();
                } else {
                    throw new Error(data.message || 'Failed to delete category');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            } finally {
                this.formLoading = false;
            }
        },
        
        async toggleStatus(category) {
            try {
                const response = await fetch(`/api/admin/categories/${category.id}/toggle-status`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    this.fetchCategories();
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            }
        },
        
        async submitForm() {
            this.formErrors = {};
            this.formLoading = true;
            
            try {
                const url = this.showEditModal 
                    ? `/api/admin/categories/${this.selectedCategory.id}` 
                    : '/api/admin/categories';
                    
                const method = this.showEditModal ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.showToast(
                        this.showEditModal ? 'Category updated successfully' : 'Category created successfully',
                        'success'
                    );
                    this.closeModal();
                    this.fetchCategories();
                    this.fetchParentCategories();
                } else {
                    if (data.errors) {
                        this.formErrors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to save category');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            } finally {
                this.formLoading = false;
            }
        },
        
        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.resetForm();
        },
        
        resetForm() {
            this.form = {
                name: '',
                icon: '',
                parent_id: '',
                display_order: 0,
                is_active: true
            };
            this.formErrors = {};
            this.selectedCategory = null;
        },
        
        exportCategories(format) {
            const token = localStorage.getItem('admin_token');
            window.location.href = `/api/admin/categories/export?format=${format}&token=${token}`;
        },
        
        prevPage() {
            if (this.page > 1) {
                this.page--;
                this.fetchCategories();
            }
        },
        
        nextPage() {
            if (this.page < this.pagination.pages) {
                this.page++;
                this.fetchCategories();
            }
        },
        
        goToPage(pageNum) {
            this.page = pageNum;
            this.fetchCategories();
        },
        
        getPageNumbers() {
            const pages = [];
            const totalPages = this.pagination.pages || 1;
            const current = this.page;
            
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) {
                    pages.push(i);
                }
            } else {
                if (current <= 4) {
                    for (let i = 1; i <= 5; i++) pages.push(i);
                    pages.push('...');
                    pages.push(totalPages);
                } else if (current >= totalPages - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = totalPages - 4; i <= totalPages; i++) pages.push(i);
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(totalPages);
                }
            }
            
            return pages;
        },
        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    }
}
</script>
@endpush
@endsection
