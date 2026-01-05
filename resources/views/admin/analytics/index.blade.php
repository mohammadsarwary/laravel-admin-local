@extends('layouts.admin')

@section('title', 'Analytics')
@section('header', 'Analytics')

@section('content')
<div x-data="analytics()" x-init="fetchAnalytics()">
    <!-- Period Selector -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex gap-4 items-center">
            <label class="text-sm font-medium text-gray-700">Period:</label>
            <select x-model="period" @change="fetchAnalytics()" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="7days">Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="90days">Last 90 Days</option>
            </select>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Growth -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">User Growth</h3>
            <div class="h-64 flex items-end space-x-2">
                <template x-for="(item, index) in userGrowth" :key="index">
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-blue-500 rounded-t" 
                             :style="'height: ' + (item.count * 10) + 'px; min-height: 4px;'"></div>
                        <span class="text-xs text-gray-500 mt-1" x-text="formatShortDate(item.date)"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Ad Posting Trends -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Ad Posting Trends</h3>
            <div class="h-64 flex items-end space-x-2">
                <template x-for="(item, index) in adTrends" :key="index">
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-green-500 rounded-t" 
                             :style="'height: ' + (item.count * 10) + 'px; min-height: 4px;'"></div>
                        <span class="text-xs text-gray-500 mt-1" x-text="formatShortDate(item.date)"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Category Distribution</h3>
            <div class="space-y-3">
                <template x-for="(item, index) in categories" :key="index">
                    <div class="flex items-center">
                        <span class="w-32 text-sm text-gray-600" x-text="item.name"></span>
                        <div class="flex-1 mx-4">
                            <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full" 
                                     :style="'width: ' + getPercentage(item.count, maxCategoryCount) + '%'"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium" x-text="item.count"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Top Locations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Top Locations</h3>
            <div class="space-y-3">
                <template x-for="(item, index) in locations" :key="index">
                    <div class="flex items-center">
                        <span class="w-32 text-sm text-gray-600 truncate" x-text="item.location"></span>
                        <div class="flex-1 mx-4">
                            <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-500 rounded-full" 
                                     :style="'width: ' + getPercentage(item.count, maxLocationCount) + '%'"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium" x-text="item.count"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function analytics() {
    return {
        period: '30days',
        userGrowth: [],
        adTrends: [],
        categories: [],
        locations: [],
        maxCategoryCount: 1,
        maxLocationCount: 1,
        
        async fetchAnalytics() {
            const headers = {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json'
            };
            
            try {
                const [usersRes, adsRes, categoriesRes, locationsRes] = await Promise.all([
                    fetch(`/api/admin/analytics/users?period=${this.period}`, { headers }),
                    fetch(`/api/admin/analytics/ads?period=${this.period}`, { headers }),
                    fetch('/api/admin/analytics/categories', { headers }),
                    fetch('/api/admin/analytics/locations', { headers })
                ]);
                
                const usersData = await usersRes.json();
                const adsData = await adsRes.json();
                const categoriesData = await categoriesRes.json();
                const locationsData = await locationsRes.json();
                
                if (usersData.success) this.userGrowth = usersData.data;
                if (adsData.success) this.adTrends = adsData.data;
                if (categoriesData.success) {
                    this.categories = categoriesData.data;
                    this.maxCategoryCount = Math.max(...this.categories.map(c => c.count), 1);
                }
                if (locationsData.success) {
                    this.locations = locationsData.data;
                    this.maxLocationCount = Math.max(...this.locations.map(l => l.count), 1);
                }
            } catch (error) {
                console.error('Error fetching analytics:', error);
            }
        },
        
        getPercentage(value, max) {
            return Math.round((value / max) * 100);
        },
        
        formatShortDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }
    }
}
</script>
@endpush
@endsection
