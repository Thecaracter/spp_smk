@extends('layouts.app')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css" rel="stylesheet">
@endpush

@section('content')
    @if (auth()->user()->role === 'admin')
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
                        <p class="text-gray-600">Ringkasan dan statistik sistem</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ now()->format('l, d F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid untuk Admin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Siswa -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Total Siswa</p>
                        <p class="text-xl font-bold text-gray-800">{{ $total_siswa ?? 0 }}</p>
                        <p class="text-sm text-gray-500">{{ $siswa_aktif ?? 0 }} Aktif</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Total Tagihan -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Total Tagihan</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($total_tagihan ?? 0, 0, ',', '.') }}
                        </p>
                        @if (isset($total_tagihan) && isset($total_terbayar) && $total_tagihan > 0)
                            <p class="text-sm text-gray-500">Total Keseluruhan</p>
                        @endif
                    </div>
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Total Terbayar -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Total Terbayar</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($total_terbayar ?? 0, 0, ',', '.') }}
                        </p>
                        @if (isset($total_tagihan) && isset($total_terbayar) && $total_tagihan > 0)
                            <p class="text-sm text-gray-500">
                                {{ number_format(($total_terbayar / $total_tagihan) * 100, 1) }}% dari total</p>
                        @endif
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Tunggakan -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Total Tunggakan</p>
                        <p class="text-xl font-bold text-gray-800">Rp
                            {{ number_format(($total_tagihan ?? 0) - ($total_terbayar ?? 0), 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Belum Terbayar</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Payment Trends -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tren Pembayaran Bulanan</h2>
                    <div id="monthlyPaymentChart" class="h-80"></div>
                </div>

                <!-- Payment Status Distribution -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Status Pembayaran</h2>
                    <div id="paymentStatusChart" class="h-80"></div>
                </div>
            </div>

            <!-- Second Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Analisis Keterlambatan -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Analisis Keterlambatan Pembayaran</h2>
                    <div id="keterlambatanChart" class="h-80"></div>
                </div>

                <!-- Class Distribution -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tunggakan per Kelas</h2>
                    <div id="classTunggakanChart" class="h-80"></div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pending Payments Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Pembayaran Pending</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Siswa</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pembayaran</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pending_payments ?? [] as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $payment->tagihan->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->tagihan->user->nit }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $payment->tagihan->jenis_pembayaran->nama }}</div>
                                            <div class="text-sm text-gray-500">Rp
                                                {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $payment->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada pembayaran pending
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Outstanding Students Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Siswa dengan Tunggakan Terbesar</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Siswa</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Tunggakan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($siswa_tunggakan ?? [] as $siswa)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $siswa->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $siswa->nit }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Rp
                                                {{ number_format($siswa->total_tunggakan, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $siswa->status_siswa === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($siswa->status_siswa) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada data tunggakan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Student Dashboard --}}
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }}</h1>
                        <p class="text-gray-600">nit: {{ auth()->user()->nit }}</p>
                    </div>
                    <div class="text-right bg-blue-50 px-4 py-2 rounded-lg">
                        <p class="text-sm text-gray-600">Kelas</p>
                        <p class="text-2xl font-bold text-blue-600">{{ auth()->user()->kelas }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid untuk Siswa -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Tunggakan -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Total Tunggakan</p>
                        <p class="text-xl font-bold text-gray-800">Rp
                            {{ number_format($total_tunggakan ?? 0, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Belum Terbayar</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Terbayar -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Total Terbayar</p>
                        <p class="text-xl font-bold text-gray-800">Rp
                            {{ number_format($total_terbayar ?? 0, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Pembayaran Sukses</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Transaksi -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Total Transaksi</p>
                        <p class="text-xl font-bold text-gray-800">{{ $pembayaran_count ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Transaksi Sukses</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>

                <!-- Status Siswa -->
                <div
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <p class="text-gray-600 text-sm">Status Siswa</p>
                        <p class="text-xl font-bold text-gray-800">{{ ucfirst($user->status_siswa) }}</p>
                        <p class="text-sm text-gray-500">Tahun {{ $user->tahun_masuk }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Charts for Student -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Payment History Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Pembayaran</h2>
                    <div id="paymentHistoryChart" class="h-80"></div>
                </div>

                <!-- Bills Status Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Tagihan</h2>
                    <div id="studentStatusChart" class="h-80"></div>
                </div>
            </div>

            <!-- Recent Bills Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Tagihan Terbaru</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Tagihan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tagihan_terbaru ?? [] as $tagihan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $tagihan->jenis_pembayaran->nama }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Rp
                                            {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</div>
                                        @if ($tagihan->total_terbayar > 0)
                                            <div class="text-xs text-gray-500">
                                                Terbayar: Rp {{ number_format($tagihan->total_terbayar, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                       @if ($tagihan->status == 'lunas') bg-green-100 text-green-800
                                       @elseif($tagihan->status == 'cicilan')
                                           bg-yellow-100 text-yellow-800
                                       @else
                                           bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($tagihan->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada tagihan terbaru
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (auth()->user()->role === 'admin')
                    // Admin Charts
                    const monthlyPayments = @json($monthly_payments);
                    const statusPembayaran = @json($status_pembayaran);
                    const keterlambatan_pembayaran = @json($keterlambatan_pembayaran);
                    const classTunggakan = @json($class_tunggakan);

                    // Monthly Payment Trends Chart
                    const monthlyPaymentOptions = {
                        series: [{
                            name: 'Total Pembayaran',
                            data: monthlyPayments.map(item => item.total)
                        }],
                        chart: {
                            type: 'area',
                            height: 320,
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        xaxis: {
                            categories: monthlyPayments.map(item => item.month),
                            labels: {
                                rotate: -45,
                                rotateAlways: false
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        colors: ['#3b82f6'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.9,
                                stops: [0, 90, 100]
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    };

                    // Payment Status Distribution Chart
                    const statusPembayaranOptions = {
                        series: Object.values(statusPembayaran),
                        chart: {
                            type: 'donut',
                            height: 320
                        },
                        labels: Object.keys(statusPembayaran),
                        colors: ['#10B981', '#FBBF24', '#EF4444'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val, opts) {
                                return opts.w.globals.series[opts.seriesIndex];
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%'
                                }
                            }
                        }
                    };

                    // Keterlambatan Pembayaran Chart
                    const keterlambatanOptions = {
                        series: [{
                            name: 'Jumlah Tagihan',
                            type: 'column',
                            data: Object.values(keterlambatan_pembayaran).map(item => item.jumlah)
                        }, {
                            name: 'Total Tunggakan',
                            type: 'line',
                            data: Object.values(keterlambatan_pembayaran).map(item => item.total)
                        }],
                        chart: {
                            height: 320,
                            type: 'line',
                            stacked: false,
                            toolbar: {
                                show: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            width: [1, 4]
                        },
                        title: {
                            text: '',
                            align: 'left',
                            style: {
                                fontSize: '14px'
                            }
                        },
                        xaxis: {
                            categories: Object.keys(keterlambatan_pembayaran),
                            labels: {
                                rotate: -45,
                                rotateAlways: false
                            }
                        },
                        yaxis: [{
                            axisTicks: {
                                show: true,
                            },
                            axisBorder: {
                                show: true,
                                color: '#008FFB'
                            },
                            labels: {
                                style: {
                                    colors: '#008FFB',
                                },
                                formatter: function(value) {
                                    return value + ' tagihan';
                                }
                            },
                            title: {
                                text: "Jumlah Tagihan",
                                style: {
                                    color: '#008FFB',
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        }, {
                            seriesName: 'Total Tunggakan',
                            opposite: true,
                            axisTicks: {
                                show: true,
                            },
                            axisBorder: {
                                show: true,
                                color: '#00E396'
                            },
                            labels: {
                                style: {
                                    colors: '#00E396',
                                },
                                formatter: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            },
                            title: {
                                text: "Total Tunggakan (Rp)",
                                style: {
                                    color: '#00E396',
                                }
                            }
                        }],
                        tooltip: {
                            y: [{
                                formatter: function(value) {
                                    return value + ' tagihan';
                                }
                            }, {
                                formatter: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }]
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        colors: ['#008FFB', '#00E396']
                    };

                    // Class Distribution Chart
                    const classTunggakanOptions = {
                        series: [{
                            name: 'Total Tunggakan',
                            data: Object.values(classTunggakan)
                        }],
                        chart: {
                            type: 'bar',
                            height: 320,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                            offsetY: -20,
                            style: {
                                fontSize: '12px',
                                colors: ["#304758"]
                            }
                        },
                        xaxis: {
                            categories: Object.keys(classTunggakan),
                            position: 'bottom'
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        colors: ['#8B5CF6']
                    };

                    // Render Admin Charts
                    new ApexCharts(document.querySelector("#monthlyPaymentChart"), monthlyPaymentOptions).render();
                    new ApexCharts(document.querySelector("#paymentStatusChart"), statusPembayaranOptions).render();
                    new ApexCharts(document.querySelector("#keterlambatanChart"), keterlambatanOptions).render();
                    new ApexCharts(document.querySelector("#classTunggakanChart"), classTunggakanOptions).render();
                @else
                    // Student Charts
                    const historyPembayaran = @json($history_pembayaran ?? []);
                    const statusTagihan = @json($status_tagihan ?? []);

                    // Payment History Chart
                    const paymentHistoryOptions = {
                        series: [{
                            name: 'Total Pembayaran',
                            data: historyPembayaran.map(item => item.total)
                        }],
                        chart: {
                            type: 'bar',
                            height: 320,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '60%',
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: historyPembayaran.map(item => item.date),
                            labels: {
                                rotate: -45,
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        colors: ['#3B82F6'],
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    };

                    // Student Status Chart
                    const studentStatusOptions = {
                        series: Object.values(statusTagihan),
                        chart: {
                            type: 'pie',
                            height: 320
                        },
                        labels: Object.keys(statusTagihan).map(status => ucfirst(status)),
                        colors: ['#10B981', '#FBBF24', '#EF4444'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val, opts) {
                                return opts.w.globals.series[opts.seriesIndex];
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value + ' tagihan';
                                }
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%'
                                }
                            }
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };

                    // Render Student Charts
                    new ApexCharts(document.querySelector("#paymentHistoryChart"), paymentHistoryOptions).render();
                    new ApexCharts(document.querySelector("#studentStatusChart"), studentStatusOptions).render();
                @endif
            });

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
        </script>
    @endpush
@endsection
