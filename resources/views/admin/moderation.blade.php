@extends('layouts.admin')

@section('title', 'Moderation')
@section('header', 'Moderation')

@section('content')
<div x-data="moderation()" x-init="fetchPendingItems()">
    <!-- Header with Stats -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-gray-400 text-sm">Review and moderate pending listings, reports, and user content.</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="card-dark rounded-lg px-4 py-2 border border-gray-700 flex items-center space-x-2">
                <span class="material-icons text-red-500">pending_actions</span>
                <span class="text-white font-semibold" x-text="totalPending"></span>
                <span class="text-gray-400 text-sm">Pending</span>
            </div>
        </div>
    </div>

    <!-- Queue Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Pending Listings</p>
                    <p class="text-3xl font-bold text-white" x-text="stats.pending_listings || '142'"></p>
                </div>
                <div class="p-3 rounded-lg bg-yellow-500/20 text-yellow-400">
                    <span class="material-icons">inventory_2</span>
                </div>
            </div>
        </div>

        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Reported Items</p>
                    <p class="text-3xl font-bold text-white" x-text="stats.reported_items || '58'"></p>
                </div>
                <div class="p-3 rounded-lg bg-red-500/20 text-red-400">
                    <span class="material-icons">flag</span>
                </div>
            </div>
        </div>

        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Avg Review Time</p>
                    <p class="text-3xl font-bold text-white">45s</p>
                </div>
                <div class="p-3 rounded-lg bg-blue-500/20 text-blue-400">
                    <span class="material-icons">schedule</span>
                </div>
            </div>
        </div>

        <div class="card-dark rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Reviewed Today</p>
                    <p class="text-3xl font-bold text-white">847</p>
                </div>
                <div class="p-3 rounded-lg bg-green-500/20 text-green-400">
                    <span class="material-icons">check_circle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card-dark rounded-lg border border-gray-700 mb-6">
        <div class="flex border-b border-gray-700">
            <button @click="activeTab = 'listings'" :class="activeTab === 'listings' ? 'border-b-2 border-red-500 text-white' : 'text-gray-400 hover:text-white'" class="px-6 py-4 font-medium transition-colors">
                <span class="flex items-center space-x-2">
                    <span class="material-icons text-sm">inventory_2</span>
                    <span>Pending Listings</span>
                    <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1" x-text="stats.pending_listings || '142'"></span>
                </span>
            </button>
            <button @click="activeTab = 'reports'" :class="activeTab === 'reports' ? 'border-b-2 border-red-500 text-white' : 'text-gray-400 hover:text-white'" class="px-6 py-4 font-medium transition-colors">
                <span class="flex items-center space-x-2">
                    <span class="material-icons text-sm">flag</span>
                    <span>Reported Items</span>
                    <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1" x-text="stats.reported_items || '58'"></span>
                </span>
            </button>
        </div>

        <!-- Pending Listings Tab -->
        <div x-show="activeTab === 'listings'" class="p-6">
            <div class="space-y-4">
                <template x-for="item in pendingListings" :key="item.id">
                    <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4 hover:border-gray-600 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons text-gray-500">image</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold" x-text="item.title"></h4>
                                    <p class="text-sm text-gray-400 mt-1" x-text="item.description"></p>
                                    <div class="flex items-center space-x-4 mt-3 text-xs text-gray-400">
                                        <span x-text="'Category: ' + item.category"></span>
                                        <span x-text="'Price: $' + item.price"></span>
                                        <span x-text="'Posted: ' + formatDate(item.created_at)"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <button @click="approveListing(item.id)" class="px-4 py-2 bg-green-500/20 text-green-400 hover:bg-green-500/30 rounded-lg transition-colors flex items-center space-x-2">
                                    <span class="material-icons text-sm">check</span>
                                    <span>Approve</span>
                                </button>
                                <button @click="rejectListing(item.id)" class="px-4 py-2 bg-red-500/20 text-red-400 hover:bg-red-500/30 rounded-lg transition-colors flex items-center space-x-2">
                                    <span class="material-icons text-sm">close</span>
                                    <span>Reject</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Reported Items Tab -->
        <div x-show="activeTab === 'reports'" class="p-6">
            <div class="space-y-4">
                <template x-for="report in reportedItems" :key="report.id">
                    <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4 hover:border-gray-600 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-white font-semibold" x-text="report.item_title"></h4>
                                    <span class="px-2 py-1 bg-red-500/20 text-red-400 text-xs rounded-full" x-text="report.reason"></span>
                                </div>
                                <p class="text-sm text-gray-400 mb-3" x-text="report.description"></p>
                                <div class="flex items-center space-x-4 text-xs text-gray-400">
                                    <span x-text="'Reported by: ' + report.reporter_name"></span>
                                    <span x-text="'Reports: ' + report.report_count"></span>
                                    <span x-text="'Posted: ' + formatDate(report.created_at)"></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <button @click="viewReport(report.id)" class="px-4 py-2 bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 rounded-lg transition-colors flex items-center space-x-2">
                                    <span class="material-icons text-sm">visibility</span>
                                    <span>View</span>
                                </button>
                                <button @click="removeItem(report.id)" class="px-4 py-2 bg-red-500/20 text-red-400 hover:bg-red-500/30 rounded-lg transition-colors flex items-center space-x-2">
                                    <span class="material-icons text-sm">delete</span>
                                    <span>Remove</span>
                                </button>
                                <button @click="dismissReport(report.id)" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center space-x-2">
                                    <span class="material-icons text-sm">close</span>
                                    <span>Dismiss</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function moderation() {
    return {
        activeTab: 'listings',
        pendingListings: [],
        reportedItems: [],
        stats: {},
        totalPending: 0,
        
        async fetchPendingItems() {
            try {
                const response = await fetch('/api/admin/moderation', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.pendingListings = data.data.pending_listings || [];
                    this.reportedItems = data.data.reported_items || [];
                    this.stats = data.data.stats || {};
                    this.totalPending = (this.stats.pending_listings || 0) + (this.stats.reported_items || 0);
                }
            } catch (error) {
                console.error('Error fetching moderation items:', error);
            }
        },
        
        async approveListing(id) {
            try {
                await fetch(`/api/admin/listings/${id}/approve`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                this.fetchPendingItems();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        async rejectListing(id) {
            if (!confirm('Are you sure you want to reject this listing?')) return;
            try {
                await fetch(`/api/admin/listings/${id}/reject`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                this.fetchPendingItems();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        async removeItem(id) {
            if (!confirm('Are you sure you want to remove this item?')) return;
            try {
                await fetch(`/api/admin/listings/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                this.fetchPendingItems();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        async dismissReport(id) {
            try {
                await fetch(`/api/admin/reports/${id}/dismiss`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                this.fetchPendingItems();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        viewReport(id) {
            alert('View report: ' + id);
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>
@endpush
@endsection
