@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="init()">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <button @click="showTrendModal('ads', 'Total Ads Posted', '+15.2%')" class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors text-left cursor-pointer hover:bg-gray-800/50">
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
        </button>

        <button @click="showTrendModal('users', 'Active Users', '+12%')" class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors text-left cursor-pointer hover:bg-gray-800/50">
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
        </button>

        <button @click="showTrendModal('revenue', 'Revenue Generated', '+2.5%')" class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors text-left cursor-pointer hover:bg-gray-800/50">
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
        </button>

        <button @click="showTrendModal('pending', 'Pending Approvals', '-8%')" class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors text-left cursor-pointer hover:bg-gray-800/50">
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
        </button>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Ad Posting Trends -->
        <div class="lg:col-span-2 card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">Ad Posting Trends</h3>
                <button class="text-gray-400 hover:text-white">
                    <span class="material-icons">more_vert</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="adTrendsChart"></canvas>
            </div>
        </div>

        <!-- Category Share -->
        <div class="card-dark rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors">
            <h3 class="text-lg font-semibold text-white mb-6">Category Share</h3>
            <div x-show="loadingCategories" class="flex items-center justify-center py-4">
                <div class="w-8 h-8 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div x-show="!loadingCategories" class="space-y-4">
                <template x-for="category in categories" :key="category.name">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-400" x-text="category.name"></span>
                            <span class="text-sm font-medium text-white" x-text="category.percentage + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300"
                                 :class="category.color"
                                 :style="'width: ' + category.percentage + '%'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performing Cities -->
        <div class="card-dark rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">Top Performing Cities</h3>
                <button @click="$dispatch('view-cities')" class="text-red-500 hover:text-red-400 text-xs font-medium uppercase tracking-wide transition-colors">View All →</button>
            </div>
            <div x-show="loadingCities" class="flex items-center justify-center py-4">
                <div class="w-8 h-8 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div x-show="!loadingCities" class="space-y-4">
                <template x-for="city in topCities" :key="city.name">
                    <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg border border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-white" x-text="city.name"></p>
                            <p class="text-xs text-gray-400" x-text="city.ads + ' active ads'"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-white" x-text="'+' + city.growth + '%'"></p>
                            <p class="text-xs" :class="city.growth >= 0 ? 'text-green-400' : 'text-red-400'" x-text="city.growth >= 0 ? 'trending up' : 'trending down'"></p>
                        </div>
                    </div>
                </template>
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

<!-- Trend Details Modal -->
<div x-show="showTrendModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showTrendModal = false" x-data="{ showTrendModal: false, trendType: '', trendTitle: '', trendValue: '' }">
    <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white" x-text="trendTitle"></h3>
            <button @click="showTrendModal = false" class="text-gray-400 hover:text-white transition-colors">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <div>
                    <p class="text-gray-400 text-sm mb-2">Current Value</p>
                    <p class="text-3xl font-bold text-white" x-text="trendValue"></p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm mb-2">Trend Details</p>
                    <p class="text-gray-300">Detailed trend analysis for <span x-text="trendTitle"></span> is loading...</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showTrendModal = false" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Close
                    </button>
                    <button class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        View Full Report
                    </button>
                </div>
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
        adTrendsChart: null,
        categories: [],
        loadingCategories: false,
        topCities: [],
        loadingCities: false,
        autoRefresh: true,
        refreshInterval: null,
        showTrendModal: false,
        trendType: '',
        trendTitle: '',
        trendValue: '',
        
        init() {
            this.fetchStats();
            this.startAutoRefresh();
        },
        
        startAutoRefresh() {
            if (this.autoRefresh) {
                this.refreshInterval = setInterval(() => {
                    this.fetchStats();
                }, 60000); // Refresh every 60 seconds
            }
        },
        
        stopAutoRefresh() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
            }
        },
        
        toggleAutoRefresh() {
            this.autoRefresh = !this.autoRefresh;
            if (this.autoRefresh) {
                this.startAutoRefresh();
            } else {
                this.stopAutoRefresh();
            }
        },
        
        showTrendModal(type, title, value) {
            this.trendType = type;
            this.trendTitle = title;
            this.trendValue = value;
            this.showTrendModal = true;
        },
        
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
                
                await this.initCharts();
                await this.fetchCategories();
                await this.fetchCities();
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        },
        
        async initCharts() {
            try {
                const trendsResponse = await fetch('/api/admin/analytics/trends', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const trendsData = await trendsResponse.json();
                
                if (trendsData.success && trendsData.data) {
                    this.initAdTrendsChart(trendsData.data);
                }
            } catch (error) {
                console.error('Error fetching chart data:', error);
                this.initAdTrendsWithMockData();
            }
        },
        
        initAdTrendsChart(data) {
            const ctx = document.getElementById('adTrendsChart');
            if (!ctx) return;
            
            if (this.adTrendsChart) {
                this.adTrendsChart.destroy();
            }
            
            this.adTrendsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Ads Posted',
                        data: data.values || [1200, 1900, 1500, 2100, 1800, 2400, 2200],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });
        },
        
        initAdTrendsWithMockData() {
            this.initAdTrendsChart({
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                values: [1200, 1900, 1500, 2100, 1800, 2400, 2200]
            });
        },
        
        async fetchCategories() {
            this.loadingCategories = true;
            try {
                const response = await fetch('/api/admin/analytics/categories', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success && data.data) {
                    this.categories = data.data.map((cat, index) => ({
                        name: cat.name,
                        percentage: cat.percentage,
                        color: this.getCategoryColor(index)
                    }));
                } else {
                    this.categories = this.getMockCategories();
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
                this.categories = this.getMockCategories();
            } finally {
                this.loadingCategories = false;
            }
        },
        
        getMockCategories() {
            return [
                { name: 'Vehicles', percentage: 35, color: 'bg-blue-500' },
                { name: 'Real Estate', percentage: 25, color: 'bg-purple-500' },
                { name: 'Electronics', percentage: 25, color: 'bg-cyan-500' },
                { name: 'Other', percentage: 15, color: 'bg-gray-500' }
            ];
        },
        
        getCategoryColor(index) {
            const colors = ['bg-blue-500', 'bg-purple-500', 'bg-cyan-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500'];
            return colors[index % colors.length];
        },
        
        async fetchCities() {
            this.loadingCities = true;
            try {
                const response = await fetch('/api/admin/analytics/locations', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success && data.data) {
                    this.topCities = data.data;
                } else {
                    this.topCities = this.getMockCities();
                }
            } catch (error) {
                console.error('Error fetching cities:', error);
                this.topCities = this.getMockCities();
            } finally {
                this.loadingCities = false;
            }
        },
        
        getMockCities() {
            return [
                { name: 'Tehran', ads: 5230, growth: 12 },
                { name: 'Mashhad', ads: 3100, growth: 8 }
            ];
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
