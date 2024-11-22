@extends('layouts.app')

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-7xl" x-data="{
        activeTab: 'belum_bayar',
        showPaymentModal: false,
        expandedItems: {},
        selectedBill: null,
        paymentAmount: '',
    
        toggleExpand(id) {
            this.expandedItems[id] = !this.expandedItems[id];
        },
    
        async startPayment(bill) {
            this.selectedBill = bill;
            const remainingAmount = bill.total_tagihan - bill.total_terbayar;
    
            if (!bill.jenis_pembayaran.dapat_dicicil) {
                await this.processPayment(remainingAmount);
            } else {
                this.paymentAmount = '';
                this.showPaymentModal = true;
            }
        },
    
        async processPayment(amount) {
            try {
                const response = await fetch(`/user/tagihan/${this.selectedBill.id}/bayar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ jumlah_bayar: amount })
                });
    
                const result = await response.json();
                console.log('Payment result:', result);
    
                if (result.success) {
                    window.snap.pay(result.snap_token, {
                        onSuccess: async () => {
                            try {
                                console.log('Payment success, updating status');
                                const updateResponse = await fetch(`/user/tagihan/update-status/${result.kode_transaksi}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    }
                                });
    
                                console.log('Update response:', updateResponse);
                                const updateResult = await updateResponse.json();
                                console.log('Update result:', updateResult);
    
                                if (updateResult.success) {
                                    this.showPaymentModal = false;
                                    this.paymentAmount = '';
                                    alert('Pembayaran berhasil!');
                                    window.location.reload();
                                } else {
                                    throw new Error('Gagal mengupdate status pembayaran');
                                }
                            } catch (error) {
                                console.error('Error updating payment:', error);
                                alert('Pembayaran berhasil tetapi gagal memperbarui status. Halaman akan dimuat ulang.');
                                window.location.reload();
                            }
                        },
                        onPending: () => {
                            alert('Pembayaran pending. Silakan selesaikan pembayaran.');
                            this.showPaymentModal = false;
                            this.paymentAmount = '';
                        },
                        onError: () => {
                            alert('Pembayaran gagal.');
                            window.location.reload();
                        },
                        onClose: () => {
                            this.showPaymentModal = false;
                            this.paymentAmount = '';
                        }
                    });
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembayaran');
            }
        }
    }">
        <!-- Header Section -->
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Tagihan Pembayaran</h1>
            <p class="mt-2 text-sm text-gray-600">Kelola dan pantau pembayaran Anda</p>
        </header>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <!-- Total Tagihan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

            <!-- Total Terbayar -->
            <div class="bg-white rounded-xl shadow-sm p-6 border">
                <div class="flex items-center">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

            <!-- Sisa Tagihan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border">
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 rounded-lg">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-sm border">
            <!-- Tab Navigation -->
            <div class="border-b">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'belum_bayar'"
                        :class="{ 'border-primary text-primary': activeTab === 'belum_bayar' }"
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm transition-colors">
                        Belum Bayar
                    </button>
                    <button @click="activeTab = 'cicilan'"
                        :class="{ 'border-primary text-primary': activeTab === 'cicilan' }"
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm transition-colors">
                        Cicilan
                    </button>
                    <button @click="activeTab = 'lunas'"
                        :class="{ 'border-primary text-primary': activeTab === 'lunas' }"
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm transition-colors">
                        Lunas
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                <!-- Belum Bayar Tab -->
                <div x-show="activeTab === 'belum_bayar'" x-cloak>
                    @forelse($tagihan->where('status', 'belum_bayar') as $item)
                        <div class="bg-white border rounded-lg mb-4 overflow-hidden">
                            <div class="p-4 sm:p-6 cursor-pointer" @click="toggleExpand('belum_{{ $item->id }}')">
                                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->jenis_pembayaran->nama }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Jatuh Tempo: {{ $item->tanggal_jatuh_tempo->format('d F Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-4">
                                        <p class="text-lg font-semibold">
                                            Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                        </p>
                                        <svg class="w-5 h-5 text-gray-400"
                                            :class="{ 'rotate-180': expandedItems['belum_{{ $item->id }}'] }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div x-show="expandedItems['belum_{{ $item->id }}']" x-collapse>
                                <div class="px-4 sm:px-6 pb-4 sm:pb-6 border-t pt-4">
                                    <div class="flex flex-wrap gap-2 mb-4">
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
                                    <button @click="startPayment({{ json_encode($item) }})"
                                        class="w-full bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary-dark transition">
                                        Bayar Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tidak ada tagihan yang belum dibayar</p>
                        </div>
                    @endforelse
                </div>

                <!-- Cicilan Tab -->
                <div x-show="activeTab === 'cicilan'" x-cloak>
                    @forelse($tagihan->where('status', 'cicilan') as $item)
                        <div class="bg-white border rounded-lg mb-4 overflow-hidden">
                            <div class="p-4 sm:p-6 cursor-pointer" @click="toggleExpand('cicilan_{{ $item->id }}')">
                                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->jenis_pembayaran->nama }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Terbayar: Rp {{ number_format($item->total_terbayar, 0, ',', '.') }}
                                            dari Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-4">
                                        <p class="text-lg font-semibold text-gray-900">
                                            Sisa: Rp
                                            {{ number_format($item->total_tagihan - $item->total_terbayar, 0, ',', '.') }}
                                        </p>
                                        <svg class="w-5 h-5 text-gray-400"
                                            :class="{ 'rotate-180': expandedItems['cicilan_{{ $item->id }}'] }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div x-show="expandedItems['cicilan_{{ $item->id }}']" x-collapse>
                                <div class="px-4 sm:px-6 pb-4 sm:pb-6 border-t">
                                    <div class="py-4">
                                        <h4 class="font-medium mb-3">Riwayat Pembayaran</h4>
                                        <div class="space-y-3">
                                            @foreach ($item->pembayaran as $pembayaran)
                                                <div class="flex justify-between items-center py-2 border-b last:border-0">
                                                    <div>
                                                        <p class="font-medium">
                                                            Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $pembayaran->created_at->format('d F Y H:i') }}
                                                        </p>
                                                    </div>
                                                    @if ($pembayaran->status_transaksi === 'pending')
                                                        <button
                                                            @click="$event.stopPropagation(); checkPaymentStatus('{{ $pembayaran->kode_transaksi }}')"
                                                            class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition">
                                                            Bayar
                                                        </button>
                                                    @else
                                                        <span
                                                            class="px-2.5 py-1 text-xs rounded-full 
                                                        @if ($pembayaran->status_transaksi === 'settlement') bg-green-100 text-green-800
                                                        @else
                                                            bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($pembayaran->status_transaksi) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button @click="startPayment({{ json_encode($item) }})"
                                        class="w-full mt-4 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary-dark transition">
                                        Bayar Lagi
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tidak ada tagihan yang sedang dicicil</p>
                        </div>
                    @endforelse
                </div>

                <!-- Lunas Tab -->
                <div x-show="activeTab === 'lunas'" x-cloak>
                    @forelse($tagihan->where('status', 'lunas') as $item)
                        <div class="bg-white border rounded-lg mb-4 overflow-hidden">
                            <div class="p-4 sm:p-6 cursor-pointer" @click="toggleExpand('lunas_{{ $item->id }}')">
                                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $item->jenis_pembayaran->nama }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Lunas pada: {{ $item->updated_at->format('d F Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-4">
                                        <span class="px-2.5 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            Lunas
                                        </span>
                                        <svg class="w-5 h-5 text-gray-400"
                                            :class="{ 'rotate-180': expandedItems['lunas_{{ $item->id }}'] }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div x-show="expandedItems['lunas_{{ $item->id }}']" x-collapse>
                                <div class="px-4 sm:px-6 pb-4 sm:pb-6 border-t">
                                    <div class="py-4">
                                        <h4 class="font-medium mb-3">Riwayat Pembayaran</h4>
                                        <div class="space-y-3">
                                            @foreach ($item->pembayaran as $pembayaran)
                                                <div class="flex justify-between items-center py-2 border-b last:border-0">
                                                    <div>
                                                        <p class="font-medium">
                                                            Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $pembayaran->created_at->format('d F Y H:i') }}
                                                        </p>
                                                    </div>
                                                    <span
                                                        class="px-2.5 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                        Selesai
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tidak ada tagihan yang sudah lunas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div x-show="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="processPayment(paymentAmount)">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Form Pembayaran</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Bayar
                                </label>
                                <input type="number" x-model="paymentAmount"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                                    required>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                Bayar
                            </button>
                            <button type="button" @click="showPaymentModal = false; paymentAmount = ''"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function checkPaymentStatus(kodeTransaksi) {
            try {
                console.log('Checking payment status for:', kodeTransaksi);
                const response = await fetch(`/user/tagihan/check-status/${kodeTransaksi}`);
                const data = await response.json();
                console.log('Status check response:', data);

                if (data.success && data.status === 'pending' && data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: async () => {
                            try {
                                console.log('Payment success, updating status');
                                const updateResponse = await fetch(
                                    `/user/tagihan/update-status/${kodeTransaksi}`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content
                                        }
                                    });

                                const updateResult = await updateResponse.json();
                                console.log('Update status response:', updateResult);

                                if (updateResult.success) {
                                    alert('Pembayaran berhasil!');
                                    window.location.reload();
                                } else {
                                    throw new Error('Gagal mengupdate status pembayaran');
                                }
                            } catch (error) {
                                console.error('Error updating status:', error);
                                alert(
                                    'Pembayaran berhasil tetapi gagal memperbarui status. Halaman akan dimuat ulang.');
                                window.location.reload();
                            }
                        },
                        onPending: () => {
                            alert('Pembayaran pending. Silakan selesaikan pembayaran.');
                        },
                        onError: () => {
                            alert('Pembayaran gagal.');
                            window.location.reload();
                        },
                        onClose: () => {}
                    });
                } else if (data.success && data.status === 'settlement') {
                    alert('Pembayaran sudah berhasil!');
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error checking payment status:', error);
                alert('Terjadi kesalahan saat memeriksa status pembayaran');
            }
        }
    </script>
@endsection
