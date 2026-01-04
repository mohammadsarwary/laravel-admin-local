@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="fetchStats()">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <span class="material-icons">people</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Users</p>
                    <p class="text-2xl font-semibold" x-text="stats.total_users || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <span class="material-icons">inventory_2</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Active Ads</p>
                    <p class="text-2xl font-semibold" x-text="stats.active_ads || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <span class="material-icons">pending</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pending Reports</p>
                    <p class="text-2xl font-semibold" x-text="stats.pending_reports || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <span class="material-icons">trending_up</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">New Users Today</p>
                    <p class="text-2xl font-semibold" x-text="stats.new_users_today || 0"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">User Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Users</span>
                    <span class="font-medium" x-text="stats.active_users || 0"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">New This Week</span>
                    <span class="font-medium" x-text="stats.new_users_week || 0"></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Ad Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Ads</span>
                    <span class="font-medium" x-text="stats.total_ads || 0"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">New This Week</span>
                    <span class="font-medium" x-text="stats.new_ads_week || 0"></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Report Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Reports</span>
                    <span class="font-medium" x-text="stats.total_reports || 0"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pending</span>
                    <span class="font-medium text-yellow-600" x-text="stats.pending_reports || 0"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <template x-for="item in activity" :key="item.id + item.type">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="p-2 rounded-full" 
                             :class="{
                                 'bg-blue-100 text-blue-600': item.type === 'user',
                                 'bg-green-100 text-green-600': item.type === 'ad',
                                 'bg-red-100 text-red-600': item.type === 'report'
                             }">
                            <span class="material-icons text-sm">
                                <template x-if="item.type === 'user'">person</template>
                                <template x-if="item.type === 'ad'">inventory_2</template>
                                <template x-if="item.type === 'report'">flag</template>
                            </span>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-medium" x-text="item.title"></p>
                            <p class="text-sm text-gray-500" x-text="item.subtitle"></p>
                        </div>
                        <span class="text-sm text-gray-400" x-text="formatDate(item.created_at)"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function dashboard() {
    return {
        stats: {},
        activity: [],
        
        async fetchStats() {
            try {
                const response = await fetch('/api/admin/stats', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                }
                
                const activityResponse = await fetch('/api/admin/activity?limit=10', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const activityData = await activityResponse.json();
                if (activityData.success) {
                    this.activity = activityData.data;
                }
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>
@endpush
@endsection
