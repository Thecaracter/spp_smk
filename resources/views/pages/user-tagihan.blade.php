@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6" x-data="{
        activeTab: 'belum_bayar',
        showPaymentForm: false,
        showUpdateModal: false,
        openItems: {},
        selectedTagihan: null,
        selectedPembayaran: null,
        tagihanTemp: null,
        pembayaranTemp: null,
        toggleAccordion(id) {
            this.openItems[id] = !this.openItems[id];
        },
        updateForm: {
            jumlah_bayar: 0,
            bukti_pembayaran: null,
            bukti_pembayaran_preview: null,
            catatan: ''
        },
        paymentForm: {
            jumlah_bayar: 0,
            bukti_pembayaran: null,
            bukti_pembayaran_preview: null
        },
        openUpdateModal(tagihan, pembayaran) {
            this.tagihanTemp = tagihan;
            this.pembayaranTemp = pembayaran;
            this.updateForm.jumlah_bayar = pembayaran.jumlah_bayar;
            this.updateForm.catatan = pembayaran.catatan || '';
            this.showUpdateModal = true;
        },
        closeUpdateModal() {
            this.showUpdateModal = false;
            this.tagihanTemp = null;
            this.pembayaranTemp = null;
            this.updateForm = {
                jumlah_bayar: 0,
                bukti_pembayaran: null,
                bukti_pembayaran_preview: null,
                catatan: ''
            };
        },
        handleImageSelect(event, form) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (form === 'update') {
                        this.updateForm.bukti_pembayaran = e.target.result;
                        this.updateForm.bukti_pembayaran_preview = e.target.result;
                    } else {
                        this.paymentForm.bukti_pembayaran = e.target.result;
                        this.paymentForm.bukti_pembayaran_preview = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    }">

        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Tagihan Pembayaran</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola dan pantau status pembayaran Anda</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Tagihan -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Tagihan</p>
                            <p class="text-lg font-semibold text-gray-900">
                                Rp {{ number_format($tagihan->sum('total_tagihan'), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Terbayar -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Terbayar</p>
                            <p class="text-lg font-semibold text-green-600">
                                Rp {{ number_format($tagihan->sum('total_terbayar'), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sisa Tagihan -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="rounded-full bg-red-100 p-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Sisa Tagihan</p>
                            <p class="text-lg font-semibold text-red-600">
                                Rp
                                {{ number_format($tagihan->sum('total_tagihan') - $tagihan->sum('total_terbayar'), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'belum_bayar'"
                        :class="{ 'border-primary text-primary': activeTab === 'belum_bayar' }"
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Belum Bayar
                    </button>
                    <button @click="activeTab = 'cicilan'"
                        :class="{ 'border-primary text-primary': activeTab === 'cicilan' }"
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Sedang Dicicil
                    </button>
                    <button @click="activeTab = 'lunas'"
                        :class="{ 'border-primary text-primary': activeTab === 'lunas' }"
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Lunas
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                <!-- Belum Bayar Tab -->
                <div x-show="activeTab === 'belum_bayar'">
                    @forelse($tagihan->where('status', 'belum_bayar') as $item)
                        <div class="bg-white border rounded-lg mb-4">
                            <!-- Header - Always visible -->
                            <div class="p-6 cursor-pointer" @click="toggleAccordion('belum_{{ $item->id }}')">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ $item->jenis_pembayaran->nama }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Jatuh Tempo:
                                            {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d F Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <p class="text-lg font-semibold">Rp
                                            {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                        </p>
                                        <svg class="w-6 h-6 transform transition-transform"
                                            :class="{ 'rotate-180': openItems['belum_{{ $item->id }}'] }" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Expandable Content -->
                            <div x-show="openItems['belum_{{ $item->id }}']" x-collapse>
                                <div class="px-6 pb-6 border-t pt-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            @if ($item->jenis_pembayaran->dapat_dicicil)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Dapat dicicil
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Harus dibayar penuh
                                                </span>
                                            @endif
                                        </div>
                                        <button
                                            @click="selectedTagihan = $refs.tagihan_{{ $item->id }}.value; showPaymentForm = true"
                                            class="inline-flex items-center px-4 py-2 border border-primary text-primary text-sm font-medium rounded-md hover:bg-primary hover:text-white transition-all">
                                            Bayar Sekarang
                                        </button>
                                        <input type="hidden" x-ref="tagihan_{{ $item->id }}"
                                            value="{{ $item->toJson() }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <h3 class="text-sm font-medium text-gray-900">Tidak ada tagihan</h3>
                            <p class="mt-1 text-sm text-gray-500">Semua tagihan Anda sudah dibayar</p>
                        </div>
                    @endforelse
                </div>

                <!-- Cicilan Tab -->
                <div x-show="activeTab === 'cicilan'">
                    @forelse($tagihan->where('status', 'cicilan') as $item)
                        <div class="bg-white border rounded-lg mb-4">
                            <!-- Header - Always visible -->
                            <div class="p-6 cursor-pointer" @click="toggleAccordion('cicilan_{{ $item->id }}')">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ $item->jenis_pembayaran->nama }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Sisa Tagihan: Rp
                                            {{ number_format($item->total_tagihan - $item->total_terbayar, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Cicilan
                                        </span>
                                        <svg class="w-6 h-6 transform transition-transform"
                                            :class="{ 'rotate-180': openItems['cicilan_{{ $item->id }}'] }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Expandable Content -->
                            <div x-show="openItems['cicilan_{{ $item->id }}']" x-collapse>
                                <div class="px-6 pb-6 border-t pt-4">
                                    <!-- Progress Bar -->
                                    <div class="mt-4">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full"
                                                style="width: {{ $item->total_tagihan > 0 ? ($item->total_terbayar / $item->total_tagihan) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500 text-right">
                                            {{ $item->total_tagihan > 0 ? number_format(($item->total_terbayar / $item->total_tagihan) * 100, 0) : 0 }}%
                                            terbayar
                                        </div>
                                    </div>
                                    <!-- Payment Details -->
                                    <div class="mt-4 flex justify-between items-center">
                                        <div>
                                            <p class="text-sm text-gray-600">Total Tagihan:</p>
                                            <p class="font-semibold">Rp
                                                {{ number_format($item->total_tagihan, 0, ',', '.') }}</p>
                                        </div>
                                        <button
                                            @click="selectedTagihan = $refs.tagihan_{{ $item->id }}.value; showPaymentForm = true"
                                            class="inline-flex items-center px-4 py-2 border border-primary text-primary text-sm font-medium rounded-md hover:bg-primary hover:text-white">
                                            Lanjutkan Pembayaran
                                        </button>
                                        <input type="hidden" x-ref="tagihan_{{ $item->id }}"
                                            value="{{ $item->toJson() }}">
                                    </div>

                                    <!-- Riwayat Pembayaran -->
                                    @if ($item->pembayaran->isNotEmpty())
                                        <div class="mt-6">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Riwayat Pembayaran</h4>
                                            <div class="space-y-4">
                                                @foreach ($item->pembayaran as $pembayaran)
                                                    <div class="bg-gray-50 p-4 rounded-lg">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <p class="text-sm text-gray-600">
                                                                    {{ $pembayaran->created_at->format('d F Y H:i') }}
                                                                </p>
                                                                <p class="font-medium">
                                                                    Rp
                                                                    {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                                                </p>
                                                            </div>
                                                            <div class="flex flex-col items-end gap-2">
                                                                <span
                                                                    class="px-2 py-1 text-xs font-medium rounded-full
                                                        {{ $pembayaran->status === 'terverifikasi'
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($pembayaran->status === 'ditolak'
                                                                ? 'bg-red-100 text-red-800'
                                                                : 'bg-yellow-100 text-yellow-800') }}">
                                                                    {{ ucfirst($pembayaran->status) }}
                                                                </span>

                                                                @if ($pembayaran->status === 'ditolak')
                                                                    <button
                                                                        @click="openUpdateModal({{ $item }}, {{ $pembayaran }})"
                                                                        class="text-sm px-3 py-1 border border-primary text-primary rounded-md hover:bg-primary hover:text-white">
                                                                        Update Pembayaran
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if ($pembayaran->status === 'ditolak' && $pembayaran->catatan)
                                                            <div class="mt-2 p-2 bg-red-50 rounded text-sm">
                                                                <p class="font-medium text-red-800">Catatan Admin:</p>
                                                                <p class="text-red-600">{{ $pembayaran->catatan }}</p>
                                                            </div>
                                                        @endif

                                                        @if ($pembayaran->bukti_pembayaran)
                                                            <div class="mt-3">
                                                                <p class="text-xs text-gray-500 mb-1">Bukti Pembayaran:</p>
                                                                <img src="{{ $pembayaran->bukti_pembayaran }}"
                                                                    alt="Bukti Pembayaran"
                                                                    class="w-32 h-32 object-cover rounded-lg cursor-pointer hover:opacity-75"
                                                                    onclick="window.open(this.src)">
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <h3 class="text-sm font-medium text-gray-900">Tidak ada cicilan aktif</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada tagihan yang sedang dicicil</p>
                        </div>
                    @endforelse
                </div>

                <!-- Lunas Tab -->
                <div x-show="activeTab === 'lunas'">
                    @forelse($tagihan->where('status', 'lunas') as $item)
                        <div class="bg-white border rounded-lg mb-4">
                            <!-- Header - Always visible -->
                            <div class="p-6 cursor-pointer" @click="toggleAccordion('lunas_{{ $item->id }}')">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ $item->jenis_pembayaran->nama }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Lunas pada: {{ $item->updated_at->format('d F Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Lunas
                                        </span>
                                        <svg class="w-6 h-6 transform transition-transform"
                                            :class="{ 'rotate-180': openItems['lunas_{{ $item->id }}'] }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Expandable Content -->
                            <div x-show="openItems['lunas_{{ $item->id }}']" x-collapse>
                                <div class="px-6 pb-6 border-t pt-4">
                                    <!-- Riwayat Pembayaran -->
                                    @if ($item->pembayaran->isNotEmpty())
                                        <div class="mt-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Riwayat Pembayaran</h4>
                                            <div class="space-y-4">
                                                @foreach ($item->pembayaran as $pembayaran)
                                                    <div class="bg-gray-50 p-4 rounded-lg">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <p class="text-sm text-gray-600">
                                                                    {{ $pembayaran->created_at->format('d F Y H:i') }}
                                                                </p>
                                                                <p class="font-medium">
                                                                    Rp
                                                                    {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                                                </p>
                                                            </div>
                                                            <span
                                                                class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                                Terverifikasi
                                                            </span>
                                                        </div>

                                                        @if ($pembayaran->bukti_pembayaran)
                                                            <div class="mt-3">
                                                                <p class="text-xs text-gray-500 mb-1">Bukti Pembayaran:</p>
                                                                <img src="{{ $pembayaran->bukti_pembayaran }}"
                                                                    alt="Bukti Pembayaran"
                                                                    class="w-32 h-32 object-cover rounded-lg cursor-pointer hover:opacity-75"
                                                                    onclick="window.open(this.src)">
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <h3 class="text-sm font-medium text-gray-900">Belum ada tagihan lunas</h3>
                            <p class="mt-1 text-sm text-gray-500">Segera lunasi tagihan Anda</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Modal Pembayaran -->
        <div x-show="showPaymentForm" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <div
                    class="inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Form Pembayaran</h3>
                        <button @click="showPaymentForm = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="handleSubmitPembayaran" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Bayar</label>
                            <input type="text" x-model="paymentForm.displayValue"
                                @input="paymentForm.displayValue = paymentForm.displayValue.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                @change="paymentForm.jumlah_bayar = parseInt(paymentForm.displayValue.replace(/\./g, ''))"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                placeholder="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bukti Pembayaran</label>
                            <input type="file" @change="handleImageSelect($event, 'payment')" accept="image/*"
                                class="mt-1 block w-full border border-gray-300 rounded-lg text-sm cursor-pointer file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-primary file:text-white hover:file:bg-primary-dark"
                                required>

                            <!-- Image Preview -->
                            <div x-show="paymentForm.bukti_pembayaran_preview" class="mt-2">
                                <img :src="paymentForm.bukti_pembayaran_preview" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="showPaymentForm = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-dark">
                                Kirim Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Update Pembayaran -->
        <div x-show="showUpdateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <div
                    class="inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Update Pembayaran</h3>
                        <button @click="closeUpdateModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-red-50 p-4 rounded-lg mb-6">
                        <p class="text-sm font-medium text-red-800">Pembayaran Sebelumnya Ditolak</p>
                        <template x-if="pembayaranTemp">
                            <p class="text-sm text-red-600 mt-1" x-text="'Catatan: ' + pembayaranTemp?.catatan"></p>
                        </template>
                    </div>

                    <form @submit.prevent="handleUpdatePembayaran" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Bayar</label>
                            <input type="text" x-model="updateForm.displayValue"
                                @input="updateForm.displayValue = updateForm.displayValue.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                @change="updateForm.jumlah_bayar = parseInt(updateForm.displayValue.replace(/\./g, ''))"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                placeholder="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bukti Pembayaran Baru</label>
                            <input type="file" @change="handleImageSelect($event, 'update')" accept="image/*"
                                class="mt-1 block w-full border border-gray-300 rounded-lg text-sm cursor-pointer file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-primary file:text-white hover:file:bg-primary-dark"
                                required>

                            <!-- Image Preview -->
                            <div x-show="updateForm.bukti_pembayaran_preview" class="mt-2">
                                <img :src="updateForm.bukti_pembayaran_preview" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="closeUpdateModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-dark">
                                Update Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function handleSubmitPembayaran() {
                const tagihan = JSON.parse(this.selectedTagihan);
                const formData = new FormData();
                formData.append('jumlah_bayar', this.paymentForm.jumlah_bayar);
                formData.append('bukti_pembayaran', this.paymentForm.bukti_pembayaran);

                fetch(`/user/tagihan/${tagihan.id}/pembayaran`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showPaymentForm = false;
                            window.location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan sistem');
                    });
            }

            function handleUpdatePembayaran() {
                const formData = new FormData();
                formData.append('jumlah_bayar', this.updateForm.jumlah_bayar);
                formData.append('bukti_pembayaran', this.updateForm.bukti_pembayaran);
                formData.append('_method', 'PUT');

                fetch(`/user/tagihan/${this.tagihanTemp.id}/pembayaran/${this.pembayaranTemp.id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closeUpdateModal();
                            window.location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan sistem');
                    });
            }
        </script>
    @endpush
@endsection
