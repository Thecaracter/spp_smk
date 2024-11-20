@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="mb-4">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Cari Pembayaran</label>
                    <div class="relative">
                        <input type="text" id="searchInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pl-10"
                            placeholder="Cari berdasarkan nama mahasiswa atau NIM...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div id="searchLoading" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Area yang akan diupdate saat search -->
        <div id="searchResultArea">
            <!-- Tabel Pembayaran -->
            <div class="table-container bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        No
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Mahasiswa
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        NIM
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Jenis Pembayaran
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Jumlah Bayar
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="pembayaranTableBody">
                                @forelse ($pembayaran as $index => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ($pembayaran->currentPage() - 1) * $pembayaran->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->tagihan->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->tagihan->user->nim }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->tagihan->jenis_pembayaran->nama }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $item->status === 'terverifikasi'
                                                ? 'bg-green-100 text-green-800'
                                                : ($item->status === 'ditolak'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="openDetailModal('{{ $item->id }}')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-md transition-colors duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Detail
                                                </button>
                                                @if ($item->status === 'menunggu')
                                                    <button onclick="openVerifikasiModal('{{ $item->id }}')"
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-600 hover:bg-green-200 rounded-md transition-colors duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Verifikasi
                                                    </button>
                                                    <button onclick="openTolakModal('{{ $item->id }}')"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-600 hover:bg-red-200 rounded-md transition-colors duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Tolak
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada data pembayaran
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-4 border-t" id="paginationContainer">
                    {{ $pembayaran->links() }}
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div id="detailModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full max-w-2xl">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Detail Pembayaran</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Nama Mahasiswa</p>
                                            <p class="font-medium" id="detailNama"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">NIM</p>
                                            <p class="font-medium" id="detailNIM"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Jenis Pembayaran</p>
                                            <p class="font-medium" id="detailJenisPembayaran"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Jumlah Bayar</p>
                                            <p class="font-medium" id="detailJumlah"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Tanggal</p>
                                            <p class="font-medium" id="detailTanggal"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Status</p>
                                            <p class="font-medium" id="detailStatus"></p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-500 mb-2">Bukti Pembayaran</p>
                                        <img id="buktiPembayaran" src="" alt="Bukti Pembayaran"
                                            class="w-full rounded-lg">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" onclick="closeModal('detailModal')"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Verifikasi -->
        <div id="verifikasiModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                        <form id="verifikasiForm" onsubmit="submitVerifikasi(event)">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Verifikasi Pembayaran</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Apakah Anda yakin akan memverifikasi
                                                pembayaran ini?</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                                    Verifikasi
                                </button>
                                <button type="button" onclick="closeModal('verifikasiModal')"
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tolak -->
        <div id="tolakModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                        <form id="tolakForm" onsubmit="submitTolak(event)">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Tolak Pembayaran</h3>
                                        <div class="mt-2">
                                            <div class="mb-4">
                                                <label for="catatanPenolakan"
                                                    class="block text-sm font-medium text-gray-700">
                                                    Alasan Penolakan
                                                </label>
                                                <textarea id="catatanPenolakan" name="catatan" rows="3" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                                    placeholder="Masukkan alasan penolakan..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                    Tolak
                                </button>
                                <button type="button" onclick="closeModal('tolakModal')"
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const searchLoading = document.getElementById('searchLoading');
                const searchResultArea = document.getElementById('searchResultArea');
                let debounceTimer;

                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    searchLoading.classList.remove('hidden');

                    debounceTimer = setTimeout(() => {
                        const searchValue = this.value;

                        fetch(`/pembayaran?search=${encodeURIComponent(searchValue)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');

                                // Update hanya area hasil pencarian
                                const newSearchResult = doc.querySelector('#searchResultArea');
                                if (newSearchResult) {
                                    searchResultArea.innerHTML = newSearchResult.innerHTML;
                                }

                                searchLoading.classList.add('hidden');
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                searchLoading.classList.add('hidden');
                                showAlert('Terjadi kesalahan saat mencari data', 'error');
                            });
                    }, 300);
                });
            });

            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function openDetailModal(id) {
                fetch(`/pembayaran/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('detailNama').textContent = data.mahasiswa.name;
                        document.getElementById('detailNIM').textContent = data.mahasiswa.nim;
                        document.getElementById('detailJenisPembayaran').textContent = data.jenis_pembayaran.nama;
                        document.getElementById('detailJumlah').textContent = formatRupiah(data.pembayaran.jumlah_bayar);
                        document.getElementById('detailTanggal').textContent = formatDate(data.pembayaran.created_at);
                        document.getElementById('detailStatus').textContent = formatStatus(data.pembayaran.status);

                        // Load bukti pembayaran
                        const buktiImg = document.getElementById('buktiPembayaran');
                        buktiImg.src = `/pembayaran/bukti/${data.pembayaran.id}`;
                        buktiImg.onerror = function() {
                            this.src = ''; // Clear source if error
                            this.alt = 'Bukti pembayaran tidak tersedia';
                        };

                        openModal('detailModal');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Terjadi kesalahan saat memuat detail pembayaran', 'error');
                    });
            }

            function openVerifikasiModal(id) {
                document.getElementById('verifikasiForm').setAttribute('data-id', id);
                openModal('verifikasiModal');
            }

            function openTolakModal(id) {
                document.getElementById('tolakForm').setAttribute('data-id', id);
                openModal('tolakModal');
            }

            function submitVerifikasi(event) {
                event.preventDefault();
                const form = event.target;
                const id = form.getAttribute('data-id');

                fetch(`/pembayaran/${id}/verifikasi`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: 'terverifikasi'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        closeModal('verifikasiModal');
                        showAlert(data.message, 'success');
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Terjadi kesalahan saat memverifikasi pembayaran', 'error');
                    });
            }

            function submitTolak(event) {
                event.preventDefault();
                const form = event.target;
                const id = form.getAttribute('data-id');
                const catatan = document.getElementById('catatanPenolakan').value;

                fetch(`/pembayaran/${id}/verifikasi`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: 'ditolak',
                            catatan: catatan
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        closeModal('tolakModal');
                        showAlert(data.message, 'success');
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Terjadi kesalahan saat menolak pembayaran', 'error');
                    });
            }

            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            }

            function formatDate(dateString) {
                return new Date(dateString).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function formatStatus(status) {
                const statusMap = {
                    'menunggu': 'Menunggu Verifikasi',
                    'terverifikasi': 'Terverifikasi',
                    'ditolak': 'Ditolak'
                };
                return statusMap[status] || status;
            }

            function showAlert(message, type = 'success') {
                const alertClass = type === 'success' ?
                    'bg-green-100 border-green-400 text-green-700' :
                    'bg-red-100 border-red-400 text-red-700';

                const alertHtml = `
        <div class="${alertClass} border px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">${message}</span>
        </div>
    `;

                const container = document.querySelector('.container');
                container.insertAdjacentHTML('afterbegin', alertHtml);

                setTimeout(() => {
                    const alert = container.querySelector('[role="alert"]');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }

            // Handle clicking outside modal to close
            document.addEventListener('click', function(event) {
                const modals = ['detailModal', 'verifikasiModal', 'tolakModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (event.target === modal) {
                        closeModal(modalId);
                    }
                });
            });

            // Handle ESC key to close modal
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const modals = ['detailModal', 'verifikasiModal', 'tolakModal'];
                    modals.forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (!modal.classList.contains('hidden')) {
                            closeModal(modalId);
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
