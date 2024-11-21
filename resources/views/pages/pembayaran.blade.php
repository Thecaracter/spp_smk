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
                                    <option value="{{ $yr }}" {{ $yr == now()->year ? 'selected' : '' }}>
                                        {{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="button" @click="openExportModal"
                                class="flex-1 sm:flex-none px-4 py-2 bg-secondary hover:bg-gray-600 text-white font-semibold rounded-lg text-sm transition">
                                <span class="material-icons text-base mr-1">download</span>
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-gray-500">Total Siswa</div>
                    <div class="text-xl md:text-2xl font-bold text-gray-800 mt-1" x-text="stats.total"></div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-primary">Sudah Bayar</div>
                    <div class="text-xl md:text-2xl font-bold text-primary mt-1" x-text="stats.paid"></div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-red-600">Belum Bayar</div>
                    <div class="text-xl md:text-2xl font-bold text-red-600 mt-1" x-text="stats.unpaid"></div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="text-sm font-semibold text-gray-500">Persentase Pembayaran</div>
                    <div class="text-xl md:text-2xl font-bold text-gray-800 mt-1" x-text="stats.percentage + '%'"></div>
                </div>
            </div>

            <!-- Table for Desktop -->
            <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    No</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Nama</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    NIS</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Kelas</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Nominal</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-if="payments.length">
                                <template x-for="(payment, index) in payments" :key="payment.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900" x-text="index + 1"></td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900" x-text="payment.name"></div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500" x-text="payment.nim"></td>
                                        <td class="px-4 py-3 text-sm text-gray-500" x-text="payment.kelas"></td>
                                        <td class="px-4 py-3">
                                            <span
                                                :class="{
                                                    'px-2 py-1 text-xs font-semibold rounded-full': true,
                                                    'bg-green-100 text-green-800': payment.status === 'Sudah Bayar',
                                                    'bg-red-100 text-red-800': payment.status !== 'Sudah Bayar'
                                                }"
                                                x-text="payment.status"></span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <span
                                                x-text="payment.tagihan ? formatCurrency(payment.tagihan.total_tagihan) : '-'"></span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <span
                                                x-text="payment.tagihan && payment.status === 'Sudah Bayar' ? formatDate(payment.tagihan.pembayaran[0].created_at) : '-'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                            <template x-if="!payments.length">
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-sm text-center text-gray-500">
                                        Tidak ada data pembayaran
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4">
                <template x-if="payments.length">
                    <template x-for="(payment, index) in payments" :key="payment.id">
                        <div class="bg-white rounded-lg shadow-md p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-medium text-gray-900" x-text="payment.name"></div>
                                <span
                                    :class="{
                                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                                        'bg-green-100 text-green-800': payment.status === 'Sudah Bayar',
                                        'bg-red-100 text-red-800': payment.status !== 'Sudah Bayar'
                                    }"
                                    x-text="payment.status"></span>
                            </div>
                            <div class="space-y-1">
                                <div class="text-sm">
                                    <span class="text-gray-500">NIS:</span>
                                    <span class="text-gray-900" x-text="payment.nim"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-500">Kelas:</span>
                                    <span class="text-gray-900" x-text="payment.kelas"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-500">Nominal:</span>
                                    <span class="text-gray-900"
                                        x-text="payment.tagihan ? formatCurrency(payment.tagihan.total_tagihan) : '-'"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-500">Tanggal Bayar:</span>
                                    <span class="text-gray-900"
                                        x-text="payment.tagihan && payment.status === 'Sudah Bayar' ? formatDate(payment.tagihan.pembayaran[0].created_at) : '-'"></span>
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
            <div x-show="showExportModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
                @click.away="showExportModal = false">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                    <div class="relative bg-white rounded-lg max-w-xl w-full p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Export Data Pembayaran</h3>
                            <p class="mt-1 text-sm text-gray-500">Pilih periode data yang akan di export</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="flex items-center">
                                    <input type="radio" x-model="exportType" value="monthly"
                                        class="form-radio text-primary" checked>
                                    <span class="ml-2">Data Bulanan</span>
                                </label>

                                <div x-show="exportType === 'monthly'" class="mt-3 flex gap-2">
                                    <select x-model="exportMonth"
                                        class="flex-1 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                        <template x-for="(name, index) in months" :key="index">
                                            <option :value="index + 1" x-text="name"
                                                :selected="index + 1 === currentMonth"></option>
                                        </template>
                                    </select>

                                    <select x-model="exportYear"
                                        class="flex-1 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                        @foreach ($availableYears as $yr)
                                            <option value="{{ $yr }}" {{ $yr == now()->year ? 'selected' : '' }}>
                                                {{ $yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="radio" x-model="exportType" value="range"
                                        class="form-radio text-primary">
                                    <span class="ml-2">Range Periode</span>
                                </label>

                                <div x-show="exportType === 'range'" class="mt-3 space-y-3">
                                    <div class="flex gap-2">
                                        <label class="w-20 pt-2">Dari:</label>
                                        <select x-model="startMonth"
                                            class="flex-1 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                            <template x-for="(name, index) in months" :key="index">
                                                <option :value="index + 1" x-text="name"
                                                    :selected="index + 1 === currentMonth"></option>
                                            </template>
                                        </select>

                                        <select x-model="startYear"
                                            class="flex-1 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                            @foreach ($availableYears as $yr)
                                                <option value="{{ $yr }}"
                                                    {{ $yr == now()->year ? 'selected' : '' }}>{{ $yr }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex gap-2">
                                        <label class="w-20 pt-2">Sampai:</label>
                                        <select x-model="endMonth"
                                            class="flex-1 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                            <template x-for="(name, index) in months" :key="index">
                                                <option :value="index + 1" x-text="name"
                                                    :selected="index + 1 === currentMonth"></option>
                                            </template>
                                        </select>

                                        <select x-model="endYear"
                                            class="flex-1 px-3 py-2 rounded-lg border-gray-200 focus:ring-2 focus:ring-primary text-sm">
                                            @foreach ($availableYears as $yr)
                                                <option value="{{ $yr }}"
                                                    {{ $yr == now()->year ? 'selected' : '' }}>{{ $yr }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="radio" x-model="exportType" value="all"
                                        class="form-radio text-primary">
                                    <span class="ml-2">Semua Data</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showExportModal = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg text-sm">
                                Batal
                            </button>
                            <button type="button" @click="handleExport"
                                class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg text-sm">
                                Export Data
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
                    month: {{ now()->month }},
                    year: {{ now()->year }},
                    currentMonth: {{ now()->month }},
                    showExportModal: false,
                    exportType: 'monthly',
                    exportMonth: {{ now()->month }},
                    exportYear: {{ now()->year }},
                    startMonth: {{ now()->month }},
                    startYear: {{ now()->year }},
                    endMonth: {{ now()->month }},
                    endYear: {{ now()->year }},
                    months: [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ],

                    init() {
                        this.fetchData();
                    },

                    openExportModal() {
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
                        const paid = this.payments.filter(p => p.status === 'Sudah Bayar').length;
                        const total = this.payments.length;
                        return {
                            total,
                            paid,
                            unpaid: total - paid,
                            percentage: total ? Math.round((paid / total) * 100) : 0
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
                            this.payments = data;
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
