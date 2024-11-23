@extends('layouts.app')

@section('content')
    <div x-data="payments" class="min-h-screen bg-gray-100" x-init="init">
        <div class="container mx-auto px-4 py-6 max-w-7xl">
            <!-- Header -->
            <div class="mb-6 bg-white rounded-lg shadow-md p-4">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                        Status Pembayaran <span class="text-primary" x-text="currentMonthYear"></span>
                    </h2>

                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                            <select x-model="month" @change="fetchData"
                                class="flex-1 sm:w-40 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                <template x-for="(name, index) in months" :key="index">
                                    <option :value="index + 1" x-text="name" :selected="index + 1 === currentMonth">
                                    </option>
                                </template>
                            </select>

                            <select x-model="year" @change="fetchData"
                                class="flex-1 sm:w-32 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                @foreach ($availableYears as $yr)
                                    <option value="{{ $yr }}" {{ $yr == $latestYear ? 'selected' : '' }}>
                                        {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="button" @click="openExportModal"
                                class="flex-1 sm:flex-none px-4 py-2 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg text-sm transition">
                                <i class="fas fa-file-export mr-2"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-gray-500">Total Item Tagihan</div>
                    <div class="text-xl md:text-2xl font-bold text-gray-800 mt-1" x-text="stats.total"></div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-green-600">Sudah Lunas</div>
                    <div class="text-xl md:text-2xl font-bold text-green-600 mt-1" x-text="stats.paid"></div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-red-600">Belum Lunas</div>
                    <div class="text-xl md:text-2xl font-bold text-red-600 mt-1" x-text="stats.unpaid"></div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-gray-500">Persentase Lunas</div>
                    <div class="text-xl md:text-2xl font-bold text-gray-800 mt-1" x-text="stats.percentage + '%'"></div>
                </div>
            </div>

            <!-- Table untuk layar besar -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hidden lg:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Tagihan</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Tanggal Bayar</th>
                        </tr>
                    </thead>
                    <template x-if="payments.length">
                        <template x-for="(payment, index) in payments" :key="payment.id">
                            <tbody class="divide-y divide-gray-200" x-data="{ open: false }">
                                <!-- Main Row -->
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="index + 1"></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="payment.name"></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="payment.nim"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="payment.kelas">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            :class="{
                                                'px-2 py-1 text-xs font-semibold rounded-full': true,
                                                'bg-green-100 text-green-800': payment.status === 'Lunas',
                                                'bg-red-100 text-red-800': payment.status === 'Belum Lunas',
                                                'bg-gray-100 text-gray-800': payment.status === 'Tidak Ada Tagihan'
                                            }"
                                            x-text="payment.tagihan.length === 0 ? 'Tidak Ada Tagihan' : payment.status"></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <button @click="open = !open"
                                            class="text-blue-600 hover:text-blue-800 text-sm focus:outline-none flex items-center gap-1">
                                            <span>Detail Tagihan</span>
                                            <svg class="w-4 h-4 transition-transform duration-200"
                                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <template
                                            x-if="payment.tagihan && payment.tagihan.some(t => t.pembayaran && t.pembayaran.length > 0)">
                                            <span
                                                x-text="formatDate(payment.tagihan.find(t => t.pembayaran && t.pembayaran.length > 0).pembayaran[0].created_at)"></span>
                                        </template>
                                        <template
                                            x-if="!payment.tagihan || !payment.tagihan.some(t => t.pembayaran && t.pembayaran.length > 0)">
                                            <span>-</span>
                                        </template>
                                    </td>
                                </tr>
                                <!-- Detail Row -->
                                <tr x-show="open" x-collapse>
                                    <td colspan="7" class="px-4 py-3 bg-gray-50">
                                        <div class="space-y-2">
                                            <template x-if="payment.tagihan.length > 0">
                                                <template x-for="tag in payment.tagihan" :key="tag.id">
                                                    <div class="p-3 bg-white rounded-lg shadow-sm">
                                                        <div class="flex justify-between items-start">
                                                            <div class="font-medium text-gray-900" x-text="tag.jenis">
                                                            </div>
                                                            <span
                                                                :class="{
                                                                    'px-2 py-1 text-xs font-semibold rounded-full': true,
                                                                    'bg-green-100 text-green-800': tag
                                                                        .status === 'lunas',
                                                                    'bg-yellow-100 text-yellow-800': tag
                                                                        .status === 'cicilan',
                                                                    'bg-red-100 text-red-800': tag
                                                                        .status === 'belum_bayar'
                                                                }"
                                                                x-text="tag.status === 'belum_bayar' ? 'Belum Bayar' : (tag.status === 'cicilan' ? 'Cicilan' : 'Lunas')"></span>
                                                        </div>
                                                        <div class="mt-2 grid grid-cols-3 gap-4 text-sm">
                                                            <div>
                                                                <div class="text-gray-500">Total:</div>
                                                                <div class="font-medium"
                                                                    x-text="formatCurrency(tag.total_tagihan)"></div>
                                                            </div>
                                                            <div>
                                                                <div class="text-gray-500">Terbayar:</div>
                                                                <div class="font-medium"
                                                                    x-text="formatCurrency(tag.total_terbayar)"></div>
                                                            </div>
                                                            <div>
                                                                <div class="text-gray-500">Sisa:</div>
                                                                <div class="font-medium"
                                                                    x-text="formatCurrency(tag.total_tagihan - tag.total_terbayar)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <template x-if="tag.pembayaran && tag.pembayaran.length > 0">
                                                            <div class="mt-2 text-xs text-gray-500">
                                                                Terakhir bayar: <span
                                                                    x-text="formatDate(tag.pembayaran[0].created_at)"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </template>
                                            <template x-if="payment.tagihan.length === 0">
                                                <div class="text-center text-gray-500 text-sm py-4 bg-white rounded-lg">
                                                    Belum ada tagihan untuk periode ini
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                    </template>
                    <template x-if="!payments.length">
                        <tbody>
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-sm text-center text-gray-500">
                                    Tidak ada data pembayaran
                                </td>
                            </tr>
                        </tbody>
                    </template>
                </table>
            </div>

            <!-- Card view untuk layar kecil dan medium -->
            <div class="lg:hidden space-y-4">
                <template x-if="payments.length">
                    <template x-for="(payment, index) in payments" :key="payment.id">
                        <div class="bg-white rounded-lg shadow-md p-4" x-data="{ open: false }">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-medium text-gray-900" x-text="payment.name"></div>
                                    <div class="text-sm text-gray-500" x-text="payment.nim"></div>
                                    <div class="text-sm text-gray-500" x-text="payment.kelas"></div>
                                </div>
                                <span
                                    :class="{
                                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                                        'bg-green-100 text-green-800': payment.status === 'Lunas',
                                        'bg-red-100 text-red-800': payment.status === 'Belum Lunas',
                                        'bg-gray-100 text-gray-800': payment.status === 'Tidak Ada Tagihan'
                                    }"
                                    x-text="payment.tagihan.length === 0 ? 'Tidak Ada Tagihan' : payment.status"></span>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <template
                                        x-if="payment.tagihan && payment.tagihan.some(t => t.pembayaran && t.pembayaran.length > 0)">
                                        <div class="text-gray-500">
                                            Terakhir bayar: <span
                                                x-text="formatDate(payment.tagihan.find(t => t.pembayaran && t.pembayaran.length > 0).pembayaran[0].created_at)"></span>
                                        </div>
                                    </template>
                                </div>
                                <button @click="open = !open"
                                    class="text-blue-600 hover:text-blue-800 focus:outline-none flex items-center gap-1">
                                    <span>Detail Tagihan</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Detail Tagihan -->
                            <div x-show="open" x-collapse>
                                <div class="mt-4 space-y-3">
                                    <template x-if="payment.tagihan.length > 0">
                                        <template x-for="tag in payment.tagihan" :key="tag.id">
                                            <div class="p-3 bg-gray-50 rounded-lg">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div class="font-medium text-gray-900" x-text="tag.jenis"></div>
                                                    <span
                                                        :class="{
                                                            'px-2 py-1 text-xs font-semibold rounded-full': true,
                                                            'bg-green-100 text-green-800': tag.status === 'lunas',
                                                            'bg-yellow-100 text-yellow-800': tag.status === 'cicilan',
                                                            'bg-red-100 text-red-800': tag.status === 'belum_bayar'
                                                        }"
                                                        x-text="tag.status === 'belum_bayar' ? 'Belum Bayar' : (tag.status === 'cicilan' ? 'Cicilan' : 'Lunas')"></span>
                                                </div>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Total:</span>
                                                        <span class="font-medium"
                                                            x-text="formatCurrency(tag.total_tagihan)"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Terbayar:</span>
                                                        <span class="font-medium"
                                                            x-text="formatCurrency(tag.total_terbayar)"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Sisa:</span>
                                                        <span class="font-medium"
                                                            x-text="formatCurrency(tag.total_tagihan - tag.total_terbayar)"></span>
                                                    </div>
                                                </div>
                                                <template x-if="tag.pembayaran && tag.pembayaran.length > 0">
                                                    <div class="mt-2 text-xs text-gray-500">
                                                        Terakhir bayar: <span
                                                            x-text="formatDate(tag.pembayaran[0].created_at)"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </template>
                                    <template x-if="payment.tagihan.length === 0">
                                        <div class="text-center text-gray-500 text-sm p-4 bg-gray-50 rounded-lg">
                                            Belum ada tagihan untuk periode ini
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>

                <template x-if="!payments.length">
                    <div class="bg-white rounded-lg shadow-md p-4 text-center text-gray-500">
                        Tidak ada data pembayaran
                    </div>
                </template>
            </div>

            <!-- Export Modal -->
            <div x-show="showExportModal" class="fixed inset-0 z-50 overflow-y-auto"
                @click.away="showExportModal = false">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black opacity-50"></div>

                    <div class="relative bg-white rounded-lg w-full max-w-md p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Export Data</h3>

                        <div class="space-y-4">
                            <!-- Monthly -->
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="exportType" value="monthly"
                                        class="form-radio text-primary" checked>
                                    <span class="ml-2">Data Bulanan</span>
                                </label>
                                <div x-show="exportType === 'monthly'" class="flex gap-2 mt-2">
                                    <select x-model="exportMonth" class="form-select flex-1">
                                        <template x-for="(name, index) in months" :key="index">
                                            <option :value="index + 1" x-text="name"
                                                :selected="index + 1 === currentMonth"></option>
                                        </template>
                                    </select>
                                    <select x-model="exportYear" class="form-select flex-1">
                                        @foreach ($availableYears as $yr)
                                            <option value="{{ $yr }}" {{ $yr == $latestYear ? 'selected' : '' }}>
                                                {{ $yr }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Range -->
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="exportType" value="range"
                                        class="form-radio text-primary">
                                    <span class="ml-2">Range Periode</span>
                                </label>
                                <div x-show="exportType === 'range'" class="space-y-2 mt-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-16">Dari:</span>
                                        <div class="flex gap-2 flex-1">
                                            <select x-model="startMonth" class="form-select flex-1">
                                                <template x-for="(name, index) in months" :key="index">
                                                    <option :value="index + 1" x-text="name"></option>
                                                </template>
                                            </select>
                                            <select x-model="startYear" class="form-select flex-1">
                                                @foreach ($availableYears as $yr)
                                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-16">Sampai:</span>
                                        <div class="flex gap-2 flex-1">
                                            <select x-model="endMonth" class="form-select flex-1">
                                                <template x-for="(name, index) in months" :key="index">
                                                    <option :value="index + 1" x-text="name"
                                                        :selected="index + 1 === currentMonth"></option>
                                                </template>
                                            </select>
                                            <select x-model="endYear" class="form-select flex-1">
                                                @foreach ($availableYears as $yr)
                                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- All Data -->
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="exportType" value="all"
                                        class="form-radio text-primary">
                                    <span class="ml-2">Semua Data</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showExportModal = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm font-medium">
                                Batal
                            </button>
                            <button type="button" @click="handleExport"
                                class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg text-sm font-medium">
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('payments', () => ({
                    payments: @json($payments),
                    month: {{ $currentMonth }},
                    year: {{ $latestYear }},
                    currentMonth: {{ $currentMonth }},
                    showExportModal: false,
                    exportType: 'monthly',
                    exportMonth: {{ $currentMonth }},
                    exportYear: {{ $latestYear }},
                    startMonth: {{ $currentMonth }},
                    startYear: {{ $latestYear }},
                    endMonth: {{ $currentMonth }},
                    endYear: {{ $latestYear }},
                    months: [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ],

                    init() {
                        this.fetchData();
                    },

                    openExportModal() {
                        this.exportType = 'monthly';
                        this.exportMonth = this.month;
                        this.exportYear = this.year;
                        this.startMonth = this.month;
                        this.startYear = this.year;
                        this.endMonth = this.month;
                        this.endYear = this.year;
                        this.showExportModal = true;
                    },

                    get currentMonthYear() {
                        return `${this.months[this.month - 1]} ${this.year}`;
                    },

                    get stats() {
                        let totalItems = 0;
                        let paidItems = 0;

                        this.payments.forEach(payment => {
                            if (payment.tagihan && payment.tagihan.length > 0) {
                                payment.tagihan.forEach(tag => {
                                    totalItems++;
                                    if (tag.status === 'lunas') {
                                        paidItems++;
                                    }
                                });
                            }
                        });

                        const unpaidItems = totalItems - paidItems;
                        const percentage = totalItems > 0 ? Math.round((paidItems / totalItems) * 100) :
                            0;

                        return {
                            total: totalItems,
                            paid: paidItems,
                            unpaid: unpaidItems,
                            percentage: percentage
                        };
                    },

                    handleExport() {
                        let url = '/pembayaran/export?';
                        const params = new URLSearchParams();

                        params.append('type', this.exportType);

                        if (this.exportType === 'monthly') {
                            params.append('month', this.exportMonth);
                            params.append('year', this.exportYear);
                        } else if (this.exportType === 'range') {
                            params.append('start_month', this.startMonth);
                            params.append('start_year', this.startYear);
                            params.append('end_month', this.endMonth);
                            params.append('end_year', this.endYear);
                        }

                        window.location.href = url + params.toString();
                        this.showExportModal = false;
                    },

                    async fetchData() {
                        try {
                            const response = await fetch(
                                `/pembayaran?month=${this.month}&year=${this.year}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                });
                            if (!response.ok) throw new Error('Network response was not ok');
                            const data = await response.json();
                            this.payments = data.payments;
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat memfilter data');
                        }
                    },

                    formatCurrency(value) {
                        return `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;
                    },

                    formatDate(date) {
                        return new Date(date).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                    }
                }));
            });
        </script>
    @endpush
@endsection
