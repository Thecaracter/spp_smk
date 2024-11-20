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

        <!-- Header dan Tombol Tambah -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Data Jenis Pembayaran</h1>
            <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Jenis Pembayaran
            </button>
        </div>
        <div class="mb-4">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Cari Jenis
                        Pembayaran</label>
                    <div class="relative">
                        <input type="text" id="searchInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pl-10"
                            placeholder="Cari berdasarkan nama atau keterangan...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabel dengan scroll horizontal -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Nama</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Keterangan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Nominal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Dapat Dicicil</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($jenisPembayaran as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->nama }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->keterangan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->dapat_dicicil ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $item->dapat_dicicil ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button type="button"
                                                onclick="openEditModal('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->keterangan }}', {{ $item->nominal }}, {{ $item->dapat_dicicil }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-md transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>
                                            <button type="button" onclick="openDeleteModal('{{ $item->id }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-600 hover:bg-red-200 rounded-md transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-6 py-4 border-t">
                {{ $jenisPembayaran->links() }}
            </div>
        </div>

        <!-- Modal Tambah -->
        <div id="createModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end sm:items-center justify-center p-4 text-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <div class="mt-0.5 ml-4 text-left">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Tambah Jenis Pembayaran</h3>
                                        <p class="text-sm text-gray-500">Silakan isi form di bawah dengan lengkap</p>
                                    </div>
                                </div>
                                <button type="button" onclick="closeModal('createModal')"
                                    class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('jenis-pembayaran.store') }}" method="POST" class="p-6">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Pembayaran</label>
                                    <input type="text" name="nama" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea name="keterangan" rows="3" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                    <input type="text" name="nominal_display" id="nominal_display" required
                                        onkeyup="formatNominal(this)"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <input type="hidden" name="nominal" id="nominal_hidden">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Dapat Dicicil</label>
                                    <div class="flex gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="dapat_dicicil" value="1"
                                                class="form-radio text-blue-600" required>
                                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="dapat_dicicil" value="0"
                                                class="form-radio text-blue-600" required>
                                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                                <button type="button" onclick="closeModal('createModal')"
                                    class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div id="editModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end sm:items-center justify-center p-4 text-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <div class="mt-0.5 ml-4 text-left">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Jenis Pembayaran</h3>
                                        <p class="text-sm text-gray-500">Update informasi jenis pembayaran</p>
                                    </div>
                                </div>
                                <button type="button" onclick="closeModal('editModal')"
                                    class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <form id="editForm" method="POST" class="p-6">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Pembayaran</label>
                                    <input type="text" name="nama" id="edit_nama" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea name="keterangan" id="edit_keterangan" rows="3" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                    <input type="text" name="nominal_display" id="edit_nominal_display" required
                                        onkeyup="formatNominal(this)"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <input type="hidden" name="nominal" id="edit_nominal_hidden">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Dapat Dicicil</label>
                                    <div class="flex gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="dapat_dicicil" value="1"
                                                id="edit_dapat_dicicil_ya" class="form-radio text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="dapat_dicicil" value="0"
                                                id="edit_dapat_dicicil_tidak" class="form-radio text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                                <button type="button" onclick="closeModal('editModal')"
                                    class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto sm:text-sm">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Delete -->
        <div id="deleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end sm:items-center justify-center p-4 text-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                        </div>

                        <div class="bg-white px-4 py-4">
                            <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus jenis pembayaran ini?
                                Tindakan ini tidak dapat dibatalkan.</p>
                        </div>

                        <form id="deleteForm" method="POST"
                            class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Hapus
                            </button>
                            <button type="button" onclick="closeModal('deleteModal')"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();
                    const table = document.querySelector('table');
                    const trs = table.querySelectorAll('tbody tr');

                    trs.forEach(function(tr) {
                        const nama = tr.querySelector('td:nth-child(2)');
                        const keterangan = tr.querySelector('td:nth-child(3)');

                        if (nama && keterangan) {
                            const text = nama.textContent + keterangan.textContent;
                            if (text.toLowerCase().includes(filter)) {
                                tr.style.display = '';
                            } else {
                                tr.style.display = 'none';
                            }
                        }
                    });
                });
            }
        });
    </script>
    <script>
        // Format nominal dengan titik sebagai pemisah ribuan
        function formatNominal(input) {
            let value = input.value.replace(/\D/g, '');
            let number = parseInt(value) || 0;
            input.value = number.toLocaleString('id-ID');
            document.getElementById(input.id === 'nominal_display' ? 'nominal_hidden' : 'edit_nominal_hidden').value =
                number;
        }

        // Modal functions
        function openCreateModal() {
            document.getElementById('nominal_display').value = '';
            document.getElementById('nominal_hidden').value = '';
            openModal('createModal');
        }

        function openEditModal(id, nama, keterangan, nominal, dapat_dicicil) {
            document.getElementById('editForm').action = `/jenis-pembayaran/${id}`;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_keterangan').value = keterangan;
            document.getElementById('edit_nominal_display').value = parseInt(nominal).toLocaleString('id-ID');
            document.getElementById('edit_nominal_hidden').value = nominal;
            document.getElementById(dapat_dicicil ? 'edit_dapat_dicicil_ya' : 'edit_dapat_dicicil_tidak').checked = true;
            openModal('editModal');
        }

        function openDeleteModal(id) {
            document.getElementById('deleteForm').action = `/jenis-pembayaran/${id}`;
            openModal('deleteModal');
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside or pressing Escape
        window.onclick = e => {
            if (e.target.classList.contains('modal-backdrop')) {
                closeModal(e.target.id);
            }
        };

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const visibleModal = document.querySelector('.modal:not(.hidden)');
                if (visibleModal) closeModal(visibleModal.id);
            }
        });
    </script>
@endpush
