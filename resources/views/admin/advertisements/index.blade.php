@extends('layouts.admin')

@section('title', 'Advertisements Management')
@section('header', 'Advertisements')

@section('content')
<div x-data="advertisementManagement()" x-init="fetchAdvertisements()">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-gray-400 text-sm">Manage promotional advertisements and featured listings for your marketplace.</p>
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
                        <button @click="format = 'csv'; exportAdvertisements(format); exportOpen = false" 
                                class="w-full text-left px-3 py-2 text-sm text-white hover:bg-gray-700 rounded transition-colors flex items-center">
                            <span class="material-icons text-sm mr-2">description</span>
                            CSV
                        </button>
                        <button @click="format = 'excel'; exportAdvertisements(format); exportOpen = false" 
                                class="w-full text-left px-3 py-2 text-sm text-white hover:bg-gray-700 rounded transition-colors flex items-center">
                            <span class="material-icons text-sm mr-2">table_chart</span>
                            Excel
                        </button>
                        <button @click="format = 'pdf'; exportAdvertisements(format); exportOpen = false" 
                                class="w-full text-left px-3 py-2 text-sm text-white hover:bg-gray-700 rounded transition-colors flex items-center">
                            <span class="material-icons text-sm mr-2">picture_as_pdf</span>
                            PDF
                        </button>
                    </div>
                </div>
            </div>
            <button @click="fetchAdvertisements()" :disabled="loading" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50">
                <span class="material-icons text-sm" :class="{ 'animate-spin': loading }">refresh</span>
                <span>Refresh</span>
            </button>
            <button @click="openCreateModal()" class="flex items-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                <span class="material-icons text-sm">add</span>
                <span>Add Advertisement</span>
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
            <p class="text-sm text-gray-400 mt-1">Total Advertisements</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-green-400" x-text="stats.active || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Active</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-yellow-400" x-text="stats.pending || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Pending</p>
        </div>
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <p class="text-3xl font-bold text-gray-400" x-text="stats.inactive || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Inactive</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark rounded-lg p-4 border border-gray-700 mb-6">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="fetchAdvertisements()"
                           placeholder="Search advertisements..."
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500">
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="date"
                           x-model="dateFrom"
                           @change="fetchAdvertisements()"
                           class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                    <span class="text-gray-500">to</span>
                    <input type="date"
                           x-model="dateTo"
                           @change="fetchAdvertisements()"
                           class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                </div>
                
                <select x-model="statusFilter" @change="fetchAdvertisements()" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-red-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
                
                <select x-model="categoryFilter" @change="fetchAdvertisements()" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-red-500">
                    <option value="">All Categories</option>
                    <template x-for="category in categories" :key="category.id">
                        <option :value="category.id" x-text="category.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- Advertisements Table -->
    <div class="card-dark rounded-lg border border-gray-700 overflow-hidden relative">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-gray-900/80 flex items-center justify-center z-10">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-gray-400 mt-4">Loading advertisements...</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('id')">
                            ID
                            <span x-show="sortBy === 'id'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('title')">
                            Title
                            <span x-show="sortBy === 'title'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('status')">
                            Status
                            <span x-show="sortBy === 'status'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('created_at')">
                            Created At
                            <span x-show="sortBy === 'created_at'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    <!-- Empty State -->
                    <tr x-show="!loading && advertisements.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-icons text-gray-600 text-5xl mb-4">campaign</span>
                                <p class="text-gray-400 text-lg mb-2">No advertisements found</p>
                                <p class="text-gray-500 text-sm mb-4">Try adjusting your search or filters, or create a new advertisement</p>
                                <button @click="search = ''; statusFilter = ''; categoryFilter = ''; fetchAdvertisements()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <template x-for="ad in advertisements" :key="ad.id">
                        <tr class="hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="ad.id"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white" x-text="ad.title"></div>
                                <div class="text-sm text-gray-400 truncate max-w-xs" x-text="ad.description"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-300" x-text="ad.category_name || 'N/A'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-medium"
                                      :class="{
                                          'bg-green-500/20 text-green-400': ad.status === 'active',
                                          'bg-yellow-500/20 text-yellow-400': ad.status === 'pending',
                                          'bg-gray-500/20 text-gray-400': ad.status === 'inactive' || ad.status === 'rejected'
                                      }"
                                      x-text="ad.status"></span>
                                <span x-show="ad.is_featured" class="ml-1 px-2 py-1 text-xs rounded-full bg-purple-500/20 text-purple-400 font-medium">Featured</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="formatDate(ad.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button @click="viewAdvertisement(ad)" class="text-blue-400 hover:text-blue-300 transition-colors">
                                    <span class="material-icons text-sm">visibility</span>
                                </button>
                                <button @click="editAdvertisement(ad)" class="text-yellow-400 hover:text-yellow-300 transition-colors">
                                    <span class="material-icons text-sm">edit</span>
                                </button>
                                <button @click="toggleStatus(ad)" class="text-purple-400 hover:text-purple-300 transition-colors">
                                    <span class="material-icons text-sm" x-text="ad.status === 'active' ? 'toggle_on' : 'toggle_off'"></span>
                                </button>
                                <button @click="deleteAdvertisement(ad.id)" class="text-red-400 hover:text-red-300 transition-colors">
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
                Showing <span x-text="advertisements.length"></span> of <span x-text="pagination.total || 0"></span> advertisements
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

    <!-- Create/Edit Advertisement Modal -->
    <div x-show="showCreateModal || showEditModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900 z-10">
                <h3 class="text-lg font-semibold text-white" x-text="showEditModal ? 'Edit Advertisement' : 'Create Advertisement'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="submitForm()" class="px-6 py-4">
                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           x-model="form.title"
                           required
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                           placeholder="Enter advertisement title">
                    <p x-show="formErrors.title" class="text-red-400 text-xs mt-1" x-text="formErrors.title"></p>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea x-model="form.description"
                              rows="3"
                              class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                              placeholder="Enter advertisement description"></textarea>
                </div>
                
                <!-- Category -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.category_id" required class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                        <option value="">Select a category</option>
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                    <p x-show="formErrors.category_id" class="text-red-400 text-xs mt-1" x-text="formErrors.category_id"></p>
                </div>
                
                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Advertisement Image</label>
                    <div class="flex items-center space-x-4">
                        <input type="file"
                               @change="handleImageUpload($event)"
                               accept="image/*"
                               class="hidden"
                               id="imageUpload">
                        <label for="imageUpload" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg cursor-pointer transition-colors flex items-center space-x-2">
                            <span class="material-icons text-sm">upload</span>
                            <span>Choose Image</span>
                        </label>
                        <span x-show="form.imageFile" class="text-sm text-gray-400" x-text="form.imageFile ? form.imageFile.name : ''"></span>
                    </div>
                    <div x-show="imagePreview" class="mt-3">
                        <img :src="imagePreview" class="max-w-xs h-auto rounded-lg border border-gray-700">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Recommended: JPG, PNG, or WebP. Max 5MB.</p>
                </div>
                
                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.status" required class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                
                <!-- Price (optional for ads) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Price</label>
                    <input type="number"
                           x-model="form.price"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                           placeholder="0.00">
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

    <!-- View Advertisement Modal -->
    <div x-show="showViewModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showViewModal = false" @keydown.escape.window="showViewModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-3xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900">
                <h3 class="text-lg font-semibold text-white">Advertisement Details</h3>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4" x-show="!viewModalLoading && selectedAd">
                <!-- Title and Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h4 class="text-xl font-semibold text-white" x-text="selectedAd.title"></h4>
                        <p class="text-gray-400 mt-1" x-text="selectedAd.category_name"></p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-xs font-medium rounded-full"
                              :class="{
                                  'bg-green-500/20 text-green-400': selectedAd.status === 'active',
                                  'bg-yellow-500/20 text-yellow-400': selectedAd.status === 'pending',
                                  'bg-gray-500/20 text-gray-400': selectedAd.status === 'inactive'
                              }"
                              x-text="selectedAd.status"></span>
                        <span x-show="selectedAd.is_featured" class="px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400">Featured</span>
                    </div>
                </div>
                
                <!-- Price -->
                <div class="mb-4" x-show="selectedAd.price">
                    <p class="text-3xl font-bold text-white">$<span x-text="parseFloat(selectedAd.price || 0).toFixed(2)"></span></p>
                </div>
                
                <!-- Description -->
                <div class="mb-4" x-show="selectedAd.description">
                    <p class="text-sm text-gray-300" x-text="selectedAd.description"></p>
                </div>
                
                <!-- Images Gallery -->
                <div x-show="selectedAd.images && selectedAd.images.length > 0" class="mb-4">
                    <p class="text-sm font-medium text-gray-400 mb-2">Images</p>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="(image, index) in selectedAd.images" :key="index">
                            <div class="aspect-square bg-gray-800 rounded-lg overflow-hidden">
                                <img :src="image.url" :alt="image.alt || 'Advertisement image'" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Views</p>
                        <p class="text-white font-semibold" x-text="selectedAd.views || '0'"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Favorites</p>
                        <p class="text-white font-semibold" x-text="selectedAd.favorites || '0'"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Condition</p>
                        <p class="text-white font-semibold" x-text="selectedAd.condition || 'N/A'"></p>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4" x-show="selectedAd.user_name">
                    <p class="text-sm font-medium text-gray-400 mb-2">Posted By</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold">
                            <span x-text="selectedAd.user_name ? selectedAd.user_name.charAt(0).toUpperCase() : 'U'"></span>
                        </div>
                        <div>
                            <p class="text-white font-medium" x-text="selectedAd.user_name"></p>
                            <p class="text-sm text-gray-400" x-text="selectedAd.user_email"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Location -->
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4" x-show="selectedAd.location">
                    <p class="text-sm font-medium text-gray-400 mb-2">Location</p>
                    <p class="text-white" x-text="selectedAd.location || 'Not specified'"></p>
                </div>
                
                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Created</p>
                        <p class="text-white" x-text="formatDate(selectedAd.created_at)"></p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Last Updated</p>
                        <p class="text-white" x-text="formatDate(selectedAd.updated_at)"></p>
                    </div>
                </div>
            </div>
            
            <!-- Loading State -->
            <div class="px-6 py-8 flex items-center justify-center" x-show="viewModalLoading">
                <div class="text-gray-400">Loading advertisement details...</div>
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
                <p class="text-gray-300">Are you sure you want to delete this advertisement? This action cannot be undone.</p>
                <p class="text-sm text-gray-500 mt-2" x-show="deletingAd" x-text="'Deleting: ' + deletingAd.title"></p>
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
function advertisementManagement() {
    return {
        advertisements: [],
        categories: [],
        stats: { total: 0, active: 0, pending: 0, inactive: 0 },
        pagination: {},
        page: 1,
        search: '',
        dateFrom: '',
        dateTo: '',
        statusFilter: '',
        categoryFilter: '',
        sortBy: 'created_at',
        sortOrder: 'desc',
        loading: false,
        error: '',
        
        showCreateModal: false,
        showEditModal: false,
        showViewModal: false,
        showDeleteModal: false,
        
        form: {
            title: '',
            description: '',
            category_id: '',
            status: 'active',
            price: '',
            imageFile: null
        },
        formErrors: {},
        formLoading: false,
        imagePreview: null,
        
        selectedAd: null,
        deletingAd: null,
        viewModalLoading: false,
        
        init() {
            this.fetchCategories();
        },
        
        async fetchCategories() {
            try {
                const response = await fetch('/api/categories', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.categories = data.data;
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        },
        
        async fetchAdvertisements() {
            this.loading = true;
            this.error = '';
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    limit: 20,
                    search: this.search,
                    date_from: this.dateFrom,
                    date_to: this.dateTo,
                    status: this.statusFilter,
                    category: this.categoryFilter,
                    sort_by: this.sortBy,
                    sort_order: this.sortOrder
                });
                
                const response = await fetch(`/api/admin/ads?${params}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch advertisements');
                }
                
                const data = await response.json();
                if (data.success) {
                    this.advertisements = data.data.ads;
                    this.pagination = data.data.pagination;
                    this.calculateStats();
                } else {
                    throw new Error(data.message || 'Failed to fetch advertisements');
                }
            } catch (error) {
                this.error = error.message;
                console.error('Error fetching advertisements:', error);
            } finally {
                this.loading = false;
            }
        },
        
        calculateStats() {
            this.stats.total = this.pagination.total || 0;
            this.stats.active = this.advertisements.filter(ad => ad.status === 'active').length;
            this.stats.pending = this.advertisements.filter(ad => ad.status === 'pending').length;
            this.stats.inactive = this.advertisements.filter(ad => ad.status === 'inactive' || ad.status === 'rejected').length;
        },
        
        toggleSort(column) {
            if (this.sortBy === column) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortOrder = 'asc';
            }
            this.fetchAdvertisements();
        },
        
        openCreateModal() {
            this.resetForm();
            this.showCreateModal = true;
        },
        
        editAdvertisement(ad) {
            this.form.title = ad.title;
            this.form.description = ad.description;
            this.form.category_id = ad.category_id;
            this.form.status = ad.status;
            this.form.price = ad.price;
            this.selectedAd = ad;
            this.showEditModal = true;
        },
        
        async viewAdvertisement(ad) {
            this.showViewModal = true;
            this.viewModalLoading = true;
            this.selectedAd = ad;
            
            try {
                const response = await fetch(`/api/admin/ads/${ad.id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.selectedAd = data.data;
                }
            } catch (error) {
                console.error('Error fetching advertisement details:', error);
            } finally {
                this.viewModalLoading = false;
            }
        },
        
        deleteAdvertisement(adId) {
            const ad = this.advertisements.find(a => a.id === adId);
            this.deletingAd = ad;
            this.showDeleteModal = true;
        },
        
        async confirmDelete() {
            if (!this.deletingAd) return;
            
            this.formLoading = true;
            try {
                const response = await fetch(`/api/admin/ads/${this.deletingAd.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast('Advertisement deleted successfully', 'success');
                    this.showDeleteModal = false;
                    this.deletingAd = null;
                    this.fetchAdvertisements();
                } else {
                    throw new Error(data.message || 'Failed to delete advertisement');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            } finally {
                this.formLoading = false;
            }
        },
        
        async toggleStatus(ad) {
            const newStatus = ad.status === 'active' ? 'inactive' : 'active';
            
            try {
                const response = await fetch(`/api/admin/ads/${ad.id}/approve`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast(`Advertisement ${newStatus}`, 'success');
                    this.fetchAdvertisements();
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
                const formData = new FormData();
                formData.append('title', this.form.title);
                formData.append('description', this.form.description || '');
                formData.append('category_id', this.form.category_id);
                formData.append('status', this.form.status);
                formData.append('price', this.form.price || 0);
                formData.append('condition', 'new');
                formData.append('location', 'Admin Created');
                
                if (this.form.imageFile) {
                    formData.append('images[]', this.form.imageFile);
                }
                
                const url = this.showEditModal 
                    ? `/api/admin/ads/${this.selectedAd.id}` 
                    : '/api/ads';
                    
                const method = this.showEditModal ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    },
                    body: this.showEditModal ? JSON.stringify({
                        title: this.form.title,
                        description: this.form.description,
                        category_id: this.form.category_id,
                        status: this.form.status,
                        price: this.form.price
                    }) : formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.showToast(
                        this.showEditModal ? 'Advertisement updated successfully' : 'Advertisement created successfully',
                        'success'
                    );
                    this.closeModal();
                    this.fetchAdvertisements();
                } else {
                    if (data.errors) {
                        this.formErrors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to save advertisement');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            } finally {
                this.formLoading = false;
            }
        },
        
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    window.showToast('Image size must be less than 5MB', 'error');
                    return;
                }
                
                this.form.imageFile = file;
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.resetForm();
        },
        
        resetForm() {
            this.form = {
                title: '',
                description: '',
                category_id: '',
                status: 'active',
                price: '',
                imageFile: null
            };
            this.formErrors = {};
            this.imagePreview = null;
            this.selectedAd = null;
        },
        
        exportAdvertisements(format) {
            const token = localStorage.getItem('admin_token');
            window.location.href = `/api/admin/ads/export?format=${format}&token=${token}`;
        },
        
        prevPage() {
            if (this.page > 1) {
                this.page--;
                this.fetchAdvertisements();
            }
        },
        
        nextPage() {
            if (this.page < this.pagination.pages) {
                this.page++;
                this.fetchAdvertisements();
            }
        },
        
        goToPage(pageNum) {
            this.page = pageNum;
            this.fetchAdvertisements();
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
