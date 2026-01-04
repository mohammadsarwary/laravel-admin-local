@extends('layouts.admin')

@section('title', 'Ad Management')
@section('header', 'Ad Management')

@section('content')
<div x-data="adManagement()" x-init="fetchAds()">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <input type="text" 
                       x-model="search" 
                       @input.debounce.300ms="fetchAds()"
                       placeholder="Search ads..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <select x-model="status" @change="fetchAds()" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="sold">Sold</option>
                <option value="rejected">Rejected</option>
            </select>
            
            <button @click="exportAds()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <span class="material-icons text-sm mr-1">download</span>
                Export
            </button>
        </div>
    </div>

    <!-- Ads Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Views</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="ad in ads" :key="ad.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900" x-text="ad.title"></div>
                            <div class="text-sm text-gray-500" x-text="ad.category_name"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $<span x-text="parseFloat(ad.price).toFixed(2)"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900" x-text="ad.user_name"></div>
                            <div class="text-sm text-gray-500" x-text="ad.user_email"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-800': ad.status === 'active',
                                      'bg-yellow-100 text-yellow-800': ad.status === 'pending',
                                      'bg-blue-100 text-blue-800': ad.status === 'sold',
                                      'bg-red-100 text-red-800': ad.status === 'rejected'
                                  }"
                                  x-text="ad.status"></span>
                            <span x-show="ad.is_featured" class="ml-1 px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Featured</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="ad.views"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(ad.created_at)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="viewAd(ad)" class="text-blue-600 hover:text-blue-900 mr-2">View</button>
                            <button x-show="ad.status === 'pending'" @click="approveAd(ad.id)" class="text-green-600 hover:text-green-900 mr-2">Approve</button>
                            <button x-show="ad.status === 'pending'" @click="rejectAd(ad.id)" class="text-yellow-600 hover:text-yellow-900 mr-2">Reject</button>
                            <button @click="featureAd(ad.id, !ad.is_featured)" class="text-purple-600 hover:text-purple-900 mr-2" x-text="ad.is_featured ? 'Unfeature' : 'Feature'"></button>
                            <button @click="deleteAd(ad.id)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Page <span x-text="page"></span> of <span x-text="pagination.pages || 1"></span>
            </div>
            <div class="flex space-x-2">
                <button @click="prevPage()" :disabled="page === 1" class="px-3 py-1 border rounded disabled:opacity-50">Previous</button>
                <button @click="nextPage()" :disabled="page >= pagination.pages" class="px-3 py-1 border rounded disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function adManagement() {
    return {
        ads: [],
        pagination: {},
        page: 1,
        search: '',
        status: '',
        
        async fetchAds() {
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    limit: 20,
                    search: this.search,
                    status: this.status
                });
                
                const response = await fetch(`/api/admin/ads?${params}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.ads = data.data.ads;
                    this.pagination = data.data.pagination;
                }
            } catch (error) {
                console.error('Error fetching ads:', error);
            }
        },
        
        async approveAd(id) {
            await this.adAction(id, 'approve', 'PUT');
        },
        
        async rejectAd(id) {
            const reason = prompt('Enter rejection reason:');
            if (reason === null) return;
            await this.adAction(id, 'reject', 'PUT', { reason });
        },
        
        async featureAd(id, featured) {
            await this.adAction(id, 'feature', 'PUT', { featured });
        },
        
        async deleteAd(id) {
            if (!confirm('Are you sure you want to delete this ad?')) return;
            try {
                await fetch(`/api/admin/ads/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                this.fetchAds();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        async adAction(id, action, method = 'PUT', body = {}) {
            try {
                await fetch(`/api/admin/ads/${id}/${action}`, {
                    method,
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                this.fetchAds();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        viewAd(ad) {
            alert('View ad: ' + ad.title);
        },
        
        exportAds() {
            window.location.href = '/api/admin/ads/export';
        },
        
        prevPage() {
            if (this.page > 1) { this.page--; this.fetchAds(); }
        },
        
        nextPage() {
            if (this.page < this.pagination.pages) { this.page++; this.fetchAds(); }
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        }
    }
}
</script>
@endpush
@endsection
