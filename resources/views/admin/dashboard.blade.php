@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="fetchStats()">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 mb-2">Total Ads Posted</p>
                    <p class="text-3xl font-bold text-white" x-text="stats.total_ads || '12,450'"></p>
                </div>
                <div class="p-3 rounded-lg bg-blue-500/20 text-blue-400">
                    <span class="material-icons text-3xl">inventory_2</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-400 text-sm">
                <span class="material-icons text-sm">trending_up</span>
                <span class="ml-1">+15.2%</span>
            </div>
        </div>

        <div class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 mb-2">Active Users</p>
                    <p class="text-3xl font-bold text-white" x-text="stats.active_users || '8,320'"></p>
                </div>
                <div class="p-3 rounded-lg bg-purple-500/20 text-purple-400">
                    <span class="material-icons text-3xl">people</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-400 text-sm">
                <span class="material-icons text-sm">trending_up</span>
                <span class="ml-1">+12%</span>
            </div>
        </div>

        <div class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 mb-2">Revenue Generated</p>
                    <p class="text-3xl font-bold text-white" x-text="stats.revenue || '$45,200'"></p>
                </div>
                <div class="p-3 rounded-lg bg-green-500/20 text-green-400">
                    <span class="material-icons text-3xl">attach_money</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-400 text-sm">
                <span class="material-icons text-sm">trending_up</span>
                <span class="ml-1">+2.5%</span>
            </div>
        </div>

        <div class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 mb-2">Pending Approvals</p>
                    <p class="text-3xl font-bold text-white" x-text="stats.pending_reports || '142'"></p>
                </div>
                <div class="p-3 rounded-lg bg-red-500/20 text-red-400">
                    <span class="material-icons text-3xl">pending_actions</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-red-400 text-sm">
                <span class="material-icons text-sm">trending_down</span>
                <span class="ml-1">-8%</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Ad Posting Trends -->
        <div class="lg:col-span-2 card-dark rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">Ad Posting Trends</h3>
                <button class="text-gray-400 hover:text-white">
                    <span class="material-icons">more_vert</span>
                </button>
            </div>
            <div class="h-64 bg-gray-800/50 rounded-lg flex items-center justify-center border border-gray-700">
                <p class="text-gray-500">Chart visualization would go here</p>
            </div>
        </div>

        <!-- Category Share -->
        <div class="card-dark rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-6">Category Share</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-400">Vehicles</span>
                        <span class="text-sm font-medium text-white">35%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 35%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-400">Real Estate</span>
                        <span class="text-sm font-medium text-white">25%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 25%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-400">Electronics</span>
                        <span class="text-sm font-medium text-white">25%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div class="bg-cyan-500 h-2 rounded-full" style="width: 25%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-400">Other</span>
                        <span class="text-sm font-medium text-white">15%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div class="bg-gray-500 h-2 rounded-full" style="width: 15%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performing Cities -->
        <div class="card-dark rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">Top Performing Cities</h3>
                <a href="#" class="text-red-500 hover:text-red-400 text-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg border border-gray-700">
                    <div>
                        <p class="text-sm font-medium text-white">Tehran</p>
                        <p class="text-xs text-gray-400">5,230 active ads</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-white">+12%</p>
                        <p class="text-xs text-green-400">trending up</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg border border-gray-700">
                    <div>
                        <p class="text-sm font-medium text-white">Mashhad</p>
                        <p class="text-xs text-gray-400">3,100 active ads</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-white">+8%</p>
                        <p class="text-xs text-green-400">trending up</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card-dark rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-6">Recent Activity</h3>
            <div class="space-y-4">
                <template x-for="item in activity.slice(0, 5)" :key="item.id + item.type">
                    <div class="flex items-center p-3 bg-gray-800/50 rounded-lg border border-gray-700">
                        <div class="p-2 rounded-lg" 
                             :class="{
                                 'bg-blue-500/20 text-blue-400': item.type === 'user',
                                 'bg-green-500/20 text-green-400': item.type === 'ad',
                                 'bg-red-500/20 text-red-400': item.type === 'report'
                             }">
                            <span class="material-icons text-sm">
                                <template x-if="item.type === 'user'">person_add</template>
                                <template x-if="item.type === 'ad'">inventory_2</template>
                                <template x-if="item.type === 'report'">flag</template>
                            </span>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate" x-text="item.title"></p>
                            <p class="text-xs text-gray-400 truncate" x-text="item.subtitle"></p>
                        </div>
                        <span class="text-xs text-gray-500 ml-2 whitespace-nowrap" x-text="formatDate(item.created_at)"></span>
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
