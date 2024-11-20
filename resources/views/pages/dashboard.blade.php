@extends('layouts.app')

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
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Mahasiswa -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Mahasiswa</p>
                        <p class="text-xl font-bold text-gray-800">{{ $total_mahasiswa ?? 0 }}</p>
                        <p class="text-sm text-gray-500">{{ $mahasiswa_aktif ?? 0 }} Aktif</p>
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
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Tagihan</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($total_tagihan ?? 0, 0, ',', '.') }}
                        </p>
                        @if (isset($total_tagihan) && isset($total_terbayar) && $total_tagihan > 0)
                            <p class="text-sm text-gray-500">Terbayar:
                                {{ number_format(($total_terbayar / $total_tagihan) * 100, 1) }}%
                            </p>
                        @endif
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>



                <!-- Total Tunggakan -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Tunggakan</p>
                        <p class="text-xl font-bold text-gray-800">Rp
                            {{ number_format(($total_tagihan ?? 0) - ($total_terbayar ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pembayaran Menunggu Verifikasi -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Pembayaran Menunggu Verifikasi</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($pending_verifikasi ?? [] as $pembayaran)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $pembayaran->tagihan->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $pembayaran->tagihan->user->nim }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $pembayaran->tagihan->jenis_pembayaran->nama }}</div>
                                            <div class="text-sm text-gray-500">Rp
                                                {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pembayaran->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('pembayaran.index') }}"
                                                class="text-primary hover:text-primary-dark">Verifikasi</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada pembayaran yang menunggu verifikasi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mahasiswa dengan Tunggakan Terbesar -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Tunggakan Terbesar</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total
                                        Tunggakan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($mahasiswa_tunggakan ?? [] as $mahasiswa)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $mahasiswa->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $mahasiswa->nim }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Rp
                                                {{ number_format($mahasiswa->total_tunggakan, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $mahasiswa->status_mahasiswa === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($mahasiswa->status_mahasiswa) }}
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
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}</h1>
                        <p class="text-gray-600">{{ strtolower(Auth::user()->role) }}</p>
                    </div>
                    <div class="text-right bg-blue-50 px-4 py-2 rounded-lg">
                        <p class="text-sm text-gray-600">Semester Aktif</p>
                        <p class="text-2xl font-bold text-primary">{{ Auth::user()->semester_aktif }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <!-- Stats Grid for Student -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Tunggakan -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Tunggakan</p>
                        <p class="text-xl font-bold text-gray-800">Rp
                            {{ number_format($total_tunggakan ?? 0, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Total tagihan yang belum dibayar</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Total Terbayar -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Terbayar</p>
                        <p class="text-xl font-bold text-gray-800">Rp
                            {{ number_format($pembayaran_terverifikasi ?? 0, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Total pembayaran terverifikasi</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Status Mahasiswa -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Status</p>
                        <p class="text-xl font-bold text-gray-800">{{ ucfirst($user->status_mahasiswa) }}</p>
                        <p class="text-sm text-gray-500">Status akademik saat ini</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Transaksi Pembayaran -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Transaksi Pembayaran</p>
                        <p class="text-xl font-bold text-gray-800">{{ $pembayaran_count ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Total transaksi terverifikasi</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tagihan Terbaru -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Tagihan Terbaru</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis
                                    Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenggat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($tagihan_terbaru ?? [] as $tagihan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $tagihan->jenis_pembayaran->nama ?? 'Tidak ada nama' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($tagihan->total_tagihan ?? 0, 0, ',', '.') }}
                                        </div>
                                        @if (($tagihan->total_terbayar ?? 0) > 0)
                                            <div class="text-xs text-gray-500">
                                                Terbayar: Rp {{ number_format($tagihan->total_terbayar, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if ($tagihan->status == 'lunas') bg-green-100 text-green-800
                                            @elseif($tagihan->status == 'cicilan')
                                                bg-yellow-100 text-yellow-800
                                            @else
                                                bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($tagihan->status ?? 'belum_bayar') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $tagihan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') : '-' }}
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
@endsection
