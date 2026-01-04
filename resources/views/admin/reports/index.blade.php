@extends('layouts.admin')

@section('title', 'Report Management')
@section('header', 'Report Management')

@section('content')
<div x-data="reportManagement()" x-init="fetchReports()">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-wrap gap-4 items-center">
            <select x-model="status" @change="fetchReports()" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="resolved">Resolved</option>
                <option value="dismissed">Dismissed</option>
            </select>
            
            <select x-model="type" @change="fetchReports()" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Types</option>
                <option value="ad">Ad Reports</option>
                <option value="user">User Reports</option>
                <option value="message">Message Reports</option>
            </select>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Report</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reporter</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="report in reports" :key="report.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900" x-text="report.reason"></div>
                            <div class="text-sm text-gray-500" x-text="report.description || 'No description'"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full"
                                  :class="{
                                      'bg-blue-100 text-blue-800': report.reported_type === 'ad',
                                      'bg-purple-100 text-purple-800': report.reported_type === 'user',
                                      'bg-gray-100 text-gray-800': report.reported_type === 'message'
                                  }"
                                  x-text="report.reported_type"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900" x-text="report.reporter_name"></div>
                            <div class="text-sm text-gray-500" x-text="report.reporter_email"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full"
                                  :class="{
                                      'bg-yellow-100 text-yellow-800': report.status === 'pending',
                                      'bg-green-100 text-green-800': report.status === 'resolved',
                                      'bg-gray-100 text-gray-800': report.status === 'dismissed'
                                  }"
                                  x-text="report.status"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(report.created_at)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="viewReport(report)" class="text-blue-600 hover:text-blue-900 mr-2">View</button>
                            <button x-show="report.status === 'pending'" @click="resolveReport(report.id)" class="text-green-600 hover:text-green-900 mr-2">Resolve</button>
                            <button x-show="report.status === 'pending'" @click="dismissReport(report.id)" class="text-gray-600 hover:text-gray-900">Dismiss</button>
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

    <!-- View Report Modal -->
    <div x-show="showViewModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showViewModal = false">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 sticky top-0 bg-gray-900">
                <h3 class="text-lg font-semibold text-white">Report Details</h3>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <!-- Report Info -->
                <div class="mb-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-3 py-1 text-xs font-medium rounded-full"
                              :class="{
                                  'bg-blue-500/20 text-blue-400': viewReport.reported_type === 'ad',
                                  'bg-purple-500/20 text-purple-400': viewReport.reported_type === 'user',
                                  'bg-gray-500/20 text-gray-400': viewReport.reported_type === 'message'
                              }"
                              x-text="viewReport.reported_type"></span>
                        <span class="px-3 py-1 text-xs font-medium rounded-full"
                              :class="{
                                  'bg-yellow-500/20 text-yellow-400': viewReport.status === 'pending',
                                  'bg-green-500/20 text-green-400': viewReport.status === 'resolved',
                                  'bg-gray-500/20 text-gray-400': viewReport.status === 'dismissed'
                              }"
                              x-text="viewReport.status"></span>
                    </div>
                    <h4 class="text-xl font-semibold text-white" x-text="viewReport.reason"></h4>
                    <p class="text-gray-400 mt-2" x-text="viewReport.description || 'No description provided'"></p>
                </div>
                
                <!-- Reporter Info -->
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4">
                    <p class="text-sm font-medium text-gray-400 mb-2">Reported By</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold">
                            <span x-text="viewReport.reporter_name ? viewReport.reporter_name.charAt(0).toUpperCase() : 'R'"></span>
                        </div>
                        <div>
                            <p class="text-white font-medium" x-text="viewReport.reporter_name"></p>
                            <p class="text-sm text-gray-400" x-text="viewReport.reporter_email"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Reported Content -->
                <div x-show="viewReport.reported_item" class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4">
                    <p class="text-sm font-medium text-gray-400 mb-2">Reported Content</p>
                    <div class="text-white">
                        <p class="font-medium" x-text="viewReport.reported_item_title || 'Unknown'"></p>
                        <p class="text-sm text-gray-400 mt-1" x-text="viewReport.reported_item_description || ''"></p>
                    </div>
                </div>
                
                <!-- Report History -->
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-4">
                    <p class="text-sm font-medium text-gray-400 mb-2">Report Information</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Report Date</p>
                            <p class="text-white" x-text="formatDate(viewReport.created_at)"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Report ID</p>
                            <p class="text-white" x-text="'#' + viewReport.id"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Notes -->
                <div x-show="viewReport.status !== 'pending'" class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                    <p class="text-sm font-medium text-gray-400 mb-2">Resolution Notes</p>
                    <p class="text-white" x-text="viewReport.resolution_notes || 'No notes provided'"></p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-700 sticky bottom-0 bg-gray-900">
                <button x-show="viewReport.status === 'pending'" @click="resolveReportFromModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Resolve
                </button>
                <button x-show="viewReport.status === 'pending'" @click="dismissReportFromModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Dismiss
                </button>
                <button @click="showViewModal = false" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function reportManagement() {
    return {
        reports: [],
        pagination: {},
        page: 1,
        status: '',
        type: '',
        showViewModal: false,
        viewReport: {},
        
        async fetchReports() {
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    limit: 20,
                    status: this.status,
                    type: this.type
                });
                
                const response = await fetch(`/api/admin/reports?${params}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.reports = data.data.reports;
                    this.pagination = data.data.pagination;
                }
            } catch (error) {
                console.error('Error fetching reports:', error);
            }
        },
        
        async resolveReport(id) {
            await this.reportAction(id, 'resolve');
        },
        
        async dismissReport(id) {
            await this.reportAction(id, 'dismiss');
        },
        
        async reportAction(id, action) {
            try {
                await fetch(`/api/admin/reports/${id}/${action}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                this.fetchReports();
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        viewReport(report) {
            this.showViewModal = true;
            this.viewReport = report;
        },
        
        async resolveReportFromModal() {
            await this.reportAction(this.viewReport.id, 'resolve');
            if (this.viewReport.status !== 'pending') {
                this.showViewModal = false;
            }
        },
        
        async dismissReportFromModal() {
            await this.reportAction(this.viewReport.id, 'dismiss');
            if (this.viewReport.status !== 'pending') {
                this.showViewModal = false;
            }
        },
        
        prevPage() {
            if (this.page > 1) { this.page--; this.fetchReports(); }
        },
        
        nextPage() {
            if (this.page < this.pagination.pages) { this.page++; this.fetchReports(); }
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        }
    }
}
</script>
@endpush
@endsection
