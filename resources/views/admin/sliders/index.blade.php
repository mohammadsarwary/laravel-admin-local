@extends('layouts.admin')

@section('title', 'Sliders Management')
@section('header', 'Sliders')

@section('content')
<div x-data="sliderManagement" x-init="$nextTick(() => fetchSliders())">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-gray-400 text-sm">Manage homepage, search results, and category sliders.</p>
        </div>
        <div class="flex items-center space-x-4">
            <button @click="fetchSliders()" :disabled="loading" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50">
                <span class="material-icons text-sm" :class="{ 'animate-spin': loading }">refresh</span>
                <span>Refresh</span>
            </button>
            <button @click="openCreateModal()" class="flex items-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                <span class="material-icons text-sm">add</span>
                <span>Add Slider</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
            <p class="text-sm text-gray-400 mt-1">Total Sliders</p>
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
            <p class="text-3xl font-bold text-purple-400" x-text="stats.homepage || '0'"></p>
            <p class="text-sm text-gray-400 mt-1">Homepage</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark rounded-lg p-4 border border-gray-700 mb-6">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="fetchSliders()"
                           placeholder="Search sliders..."
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500">
                </div>
                
                <select x-model="typeFilter" @change="fetchSliders()" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-red-500">
                    <option value="">All Types</option>
                    <option value="homepage">Homepage</option>
                    <option value="search_results">Search Results</option>
                    <option value="category">Category</option>
                </select>
                
                <select x-model="statusFilter" @change="fetchSliders()" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-red-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <!-- Bulk Actions -->
            <div x-show="selectedSliders.length > 0" class="flex items-center space-x-2">
                <span class="text-sm text-gray-400" x-text="`${selectedSliders.length} selected`"></span>
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

    <!-- Sliders Table -->
    <div class="card-dark rounded-lg border border-gray-700 overflow-hidden relative">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-gray-900/80 flex items-center justify-center z-10">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-gray-400 mt-4">Loading sliders...</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" 
                                   @change="toggleSelectAll()"
                                   :checked="selectedSliders.length === sliders.length && sliders.length > 0"
                                   class="rounded border-gray-600 bg-gray-700 text-red-500 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('id')">
                            ID
                            <span x-show="sortBy === 'id'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('title')">
                            Title
                            <span x-show="sortBy === 'title'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase cursor-pointer hover:text-gray-300 transition-colors" @click="toggleSort('display_order')">
                            Order
                            <span x-show="sortBy === 'display_order'" class="ml-1">
                                <span class="material-icons text-sm" x-text="sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    <!-- Empty State -->
                    <tr x-show="!loading && sliders.length === 0">
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-icons text-gray-600 text-5xl mb-4">slideshow</span>
                                <p class="text-gray-400 text-lg mb-2">No sliders found</p>
                                <p class="text-gray-500 text-sm mb-4">Try adjusting your search or create a new slider</p>
                                <button @click="search = ''; typeFilter = ''; statusFilter = ''; fetchSliders()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <template x-for="slider in sliders" :key="slider.id">
                        <tr class="hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                       :value="slider.id"
                                       @change="toggleSelect(slider.id)"
                                       :checked="selectedSliders.includes(slider.id)"
                                       class="rounded border-gray-600 bg-gray-700 text-red-500 focus:ring-red-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="slider.id"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img :src="slider.image_url" :alt="slider.title" class="w-16 h-10 object-cover rounded border border-gray-700">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white" x-text="slider.title"></div>
                                <div class="text-sm text-gray-400 truncate max-w-xs" x-text="slider.description"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-medium"
                                      :class="{
                                          'bg-blue-500/20 text-blue-400': slider.slider_type === 'homepage',
                                          'bg-green-500/20 text-green-400': slider.slider_type === 'search_results',
                                          'bg-purple-500/20 text-purple-400': slider.slider_type === 'category'
                                      }"
                                      x-text="slider.slider_type.replace('_', ' ')"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400" x-text="slider.display_order"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-medium"
                                      :class="slider.is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                                      x-text="slider.is_active ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button @click="viewSlider(slider)" class="text-blue-400 hover:text-blue-300 transition-colors">
                                    <span class="material-icons text-sm">visibility</span>
                                </button>
                                <button @click="editSlider(slider)" class="text-yellow-400 hover:text-yellow-300 transition-colors">
                                    <span class="material-icons text-sm">edit</span>
                                </button>
                                <button @click="toggleStatus(slider)" class="text-purple-400 hover:text-purple-300 transition-colors">
                                    <span class="material-icons text-sm" x-text="slider.is_active ? 'toggle_on' : 'toggle_off'"></span>
                                </button>
                                <button @click="deleteSlider(slider.id)" class="text-red-400 hover:text-red-300 transition-colors">
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
                Showing <span x-text="sliders.length"></span> of <span x-text="pagination.total || 0"></span> sliders
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

    <!-- Create/Edit Slider Modal -->
    <div x-show="showCreateModal || showEditModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900 z-10">
                <h3 class="text-lg font-semibold text-white" x-text="showEditModal ? 'Edit Slider' : 'Create Slider'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
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
                           placeholder="Enter slider title">
                    <p x-show="formErrors.title" class="text-red-400 text-xs mt-1" x-text="formErrors.title"></p>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea x-model="form.description"
                              rows="3"
                              class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                              placeholder="Enter slider description"></textarea>
                </div>
                
                <!-- Image URL -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Image URL <span class="text-red-500">*</span>
                    </label>
                    <input type="url"
                           x-model="form.image_url"
                           required
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                           placeholder="https://example.com/image.jpg">
                    <p x-show="form.image_url" class="mt-2">
                        <img :src="form.image_url" alt="Preview" class="w-full h-32 object-cover rounded border border-gray-700">
                    </p>
                    <p x-show="formErrors.image_url" class="text-red-400 text-xs mt-1" x-text="formErrors.image_url"></p>
                </div>
                
                <!-- Link Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Link Type</label>
                    <select x-model="form.link_type" class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                        <option value="external">External URL</option>
                        <option value="ad">Ad</option>
                        <option value="category">Category</option>
                    </select>
                </div>
                
                <!-- Link Value -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Link Value <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           x-model="form.link_value"
                           required
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white placeholder-gray-500"
                           :placeholder="form.link_type === 'external' ? 'https://example.com' : (form.link_type === 'ad' ? 'Ad ID' : 'Category slug')">
                    <p x-show="formErrors.link_value" class="text-red-400 text-xs mt-1" x-text="formErrors.link_value"></p>
                </div>
                
                <!-- Slider Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Slider Type</label>
                    <select x-model="form.slider_type" class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                        <option value="homepage">Homepage</option>
                        <option value="search_results">Search Results</option>
                        <option value="category">Category</option>
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
                
                <!-- Scheduling -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Start Date</label>
                        <input type="datetime-local"
                               x-model="form.starts_at"
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">End Date</label>
                        <input type="datetime-local"
                               x-model="form.ends_at"
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 text-white">
                    </div>
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

    <!-- View Slider Modal -->
    <div x-show="showViewModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showViewModal = false" @keydown.escape.window="showViewModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900">
                <h3 class="text-lg font-semibold text-white">Slider Details</h3>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <template x-if="!viewModalLoading && selectedSlider">
                <div class="px-6 py-4">
                    <img :src="selectedSlider.image_url" :alt="selectedSlider.title" class="w-full h-48 object-cover rounded-lg mb-4 border border-gray-700">
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-xl font-semibold text-white" x-text="selectedSlider.title"></h4>
                            <p class="text-gray-400 text-sm" x-text="selectedSlider.description"></p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full"
                              :class="selectedSlider.is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                              x-text="selectedSlider.is_active ? 'Active' : 'Inactive'"></span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                            <p class="text-sm text-gray-400 mb-1">Type</p>
                            <p class="text-white font-semibold" x-text="selectedSlider.slider_type.replace('_', ' ')"></p>
                        </div>
                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                            <p class="text-sm text-gray-400 mb-1">Display Order</p>
                            <p class="text-white font-semibold" x-text="selectedSlider.display_order"></p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4">
                        <p class="text-sm font-medium text-gray-400 mb-2">Link</p>
                        <p class="text-white">
                            <span class="px-2 py-1 bg-gray-700 rounded text-xs mr-2" x-text="selectedSlider.link_type"></span>
                            <span x-text="selectedSlider.link_value"></span>
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                            <p class="text-sm text-gray-400 mb-1">Start Date</p>
                            <p class="text-white" x-text="formatDate(selectedSlider.starts_at)"></p>
                        </div>
                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                            <p class="text-sm text-gray-400 mb-1">End Date</p>
                            <p class="text-white" x-text="formatDate(selectedSlider.ends_at)"></p>
                        </div>
                    </div>
                </div>
            </template>
            
            <div class="px-6 py-8 flex items-center justify-center" x-show="viewModalLoading">
                <div class="text-gray-400">Loading slider details...</div>
            </div>
            
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
                <p class="text-gray-300">Are you sure you want to delete this slider? This action cannot be undone.</p>
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

<!-- NOTE: Defer Alpine initialization until Alpine is loaded -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('sliderManagement', () => ({
        sliders: [],
        stats: { total: 0, active: 0, inactive: 0, homepage: 0 },
        pagination: {},
        page: 1,
        search: '',
        typeFilter: '',
        statusFilter: '',
        sortBy: 'display_order',
        sortOrder: 'asc',
        loading: false,
        error: '',
        
        selectedSliders: [],
        showCreateModal: false,
        showEditModal: false,
        showViewModal: false,
        showDeleteModal: false,
        
        form: {
            title: '',
            description: '',
            image_url: '',
            link_type: 'external',
            link_value: '',
            slider_type: 'homepage',
            display_order: 0,
            is_active: true,
            starts_at: '',
            ends_at: ''
        },
        formErrors: {},
        formLoading: false,
        
        selectedSlider: null,
        deletingSliderId: null,
        viewModalLoading: false,
        
        async fetchSliders() {
            this.loading = true;
            this.error = '';
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    limit: 20,
                    search: this.search,
                    type: this.typeFilter,
                    status: this.statusFilter,
                    sort_by: this.sortBy,
                    sort_order: this.sortOrder
                });
                
                // Get token from session storage (set by admin middleware)
                const token = sessionStorage.getItem('admin_token') || localStorage.getItem('admin_token');
                const response = await fetch(`/api/admin/sliders?${params}`, {
                    headers: {
                        'Authorization': token ? `Bearer ${token}` : '',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: Failed to fetch sliders`);
                }
                
                const data = await response.json();
                if (data.success) {
                    this.sliders = data.data.sliders;
                    this.pagination = data.data.pagination;
                    this.calculateStats();
                } else {
                    throw new Error(data.message || 'Failed to fetch sliders');
                }
            } catch (error) {
                this.error = error.message;
                console.error('Error fetching sliders:', error);
            } finally {
                this.loading = false;
            }
        },
        
        calculateStats() {
            this.stats.total = this.pagination.total || 0;
            this.stats.active = this.sliders.filter(s => s.is_active).length;
            this.stats.inactive = this.sliders.filter(s => !s.is_active).length;
            this.stats.homepage = this.sliders.filter(s => s.slider_type === 'homepage').length;
        },
        
        toggleSort(column) {
            if (this.sortBy === column) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortOrder = 'asc';
            }
            this.fetchSliders();
        },
        
        toggleSelect(sliderId) {
            const index = this.selectedSliders.indexOf(sliderId);
            if (index > -1) {
                this.selectedSliders.splice(index, 1);
            } else {
                this.selectedSliders.push(sliderId);
            }
        },
        
        toggleSelectAll() {
            if (this.selectedSliders.length === this.sliders.length) {
                this.selectedSliders = [];
            } else {
                this.selectedSliders = this.sliders.map(s => s.id);
            }
        },
        
        async bulkAction(action) {
            if (this.selectedSliders.length === 0) return;
            
            if (!confirm(`Are you sure you want to ${action} ${this.selectedSliders.length} sliders?`)) return;
            
            try {
                const token = sessionStorage.getItem('admin_token') || localStorage.getItem('admin_token');
                const response = await fetch('/api/admin/sliders/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Authorization': token ? `Bearer ${token}` : '',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        action: action,
                        slider_ids: this.selectedSliders
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    this.selectedSliders = [];
                    this.fetchSliders();
                } else {
                    throw new Error(data.message || 'Bulk action failed');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            }
        },
        
        openCreateModal() {
            this.form = {
                title: '',
                description: '',
                image_url: '',
                link_type: 'external',
                link_value: '',
                slider_type: 'homepage',
                display_order: 0,
                is_active: true,
                starts_at: '',
                ends_at: ''
            };
            this.formErrors = {};
            this.showCreateModal = true;
        },
        
        editSlider(slider) {
            this.form = { ...slider };
            this.formErrors = {};
            this.showEditModal = true;
        },
        
        async submitForm() {
            this.formLoading = true;
            this.formErrors = {};
            
            try {
                const token = sessionStorage.getItem('admin_token') || localStorage.getItem('admin_token');
                const url = this.showEditModal 
                    ? `/api/admin/sliders/${this.form.id}`
                    : '/api/admin/sliders';
                
                const method = this.showEditModal ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': token ? `Bearer ${token}` : '',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    this.closeModal();
                    this.fetchSliders();
                } else {
                    if (data.errors) {
                        this.formErrors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to save slider');
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
            this.form = {};
            this.formErrors = {};
        },
        
        async viewSlider(slider) {
            this.selectedSlider = slider;
            this.viewModalLoading = false;
            this.showViewModal = true;
        },
        
        async toggleStatus(slider) {
            try {
                const token = sessionStorage.getItem('admin_token') || localStorage.getItem('admin_token');
                const response = await fetch(`/api/admin/sliders/${slider.id}/toggle-status`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': token ? `Bearer ${token}` : '',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    this.fetchSliders();
                } else {
                    throw new Error(data.message || 'Failed to toggle status');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            }
        },
        
        deleteSlider(sliderId) {
            this.deletingSliderId = sliderId;
            this.showDeleteModal = true;
        },
        
        async confirmDelete() {
            this.formLoading = true;
            try {
                const token = sessionStorage.getItem('admin_token') || localStorage.getItem('admin_token');
                const response = await fetch(`/api/admin/sliders/${this.deletingSliderId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': token ? `Bearer ${token}` : '',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    this.showDeleteModal = false;
                    this.fetchSliders();
                } else {
                    throw new Error(data.message || 'Failed to delete slider');
                }
            } catch (error) {
                window.showToast(error.message, 'error');
            } finally {
                this.formLoading = false;
            }
        },
        
        prevPage() {
            if (this.page > 1) {
                this.page--;
                this.fetchSliders();
            }
        },
        
        nextPage() {
            if (this.page < this.pagination.pages) {
                this.page++;
                this.fetchSliders();
            }
        },
        
        goToPage(pageNum) {
            this.page = pageNum;
            this.fetchSliders();
        },
        
        getPageNumbers() {
            const pages = [];
            const maxVisible = 5;
            const start = Math.max(1, this.page - Math.floor(maxVisible / 2));
            const end = Math.min(this.pagination.pages || 1, start + maxVisible - 1);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        
        formatDate(dateStr) {
            if (!dateStr) return 'Not set';
            return new Date(dateStr).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }));
});
</script>
