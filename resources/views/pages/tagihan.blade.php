{{-- resources/views/pages/tagihan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Tagihan')

@section('content')
    <div class="container mx-auto px-4" x-data="tagihanManager()">
        <!-- Main List -->
        <div class="mb-6" x-show="!showUserDetail">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Daftar Mahasiswa dan Tunggakan</h1>
                <div class="flex space-x-2">
                    <div class="relative">
                        <input type="text" id="searchInput" x-model="searchQuery" @input="handleSearch()"
                            class="w-64 pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-primary"
                            placeholder="Cari mahasiswa...">
                        <span class="material-icons absolute left-3 top-2.5 text-gray-400">search</span>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Semester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Tunggakan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tagihan Aktif</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="searchResults">
                            @foreach ($users as $user)
                                <tr class="search-row hover:bg-gray-50" data-user-id="{{ $user->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->nim }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if ($user->foto)
                                                    <img class="h-10 w-10 rounded-full" src="{{ $user->foto }}"
                                                        alt="{{ $user->name }}">
                                                @else
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->semester_aktif }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 total-tunggakan">Rp
                                            {{ number_format($user->total_tunggakan, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 tagihan-aktif">{{ $user->tagihan_belum_lunas }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="getStatusClass('{{ $user->status_mahasiswa }}')">
                                            {{ ucfirst($user->status_mahasiswa) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button @click="showUserDetails($event)" data-user="{{ json_encode($user) }}"
                                            class="text-primary hover:text-primary-dark">Detail</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Detail View -->
        <div x-show="showUserDetail" class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button @click="showUserDetail = false" class="text-gray-500 hover:text-gray-700">
                        <span class="material-icons">arrow_back</span>
                    </button>
                    <h1 class="text-2xl font-semibold">Detail Tagihan Mahasiswa</h1>
                </div>
                <button @click="handleShowCreateModal()"
                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
                    Tambah Tagihan
                </button>
            </div>

            <!-- Student Info Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex items-center space-x-4">
                        <template x-if="selectedUser">
                            <div class="flex items-center space-x-4">
                                <div class="h-16 w-16 rounded-full bg-primary text-white flex items-center justify-center text-xl"
                                    x-text="selectedUser.name.charAt(0).toUpperCase()"></div>
                                <div>
                                    <h2 class="text-xl font-semibold" x-text="selectedUser.name"></h2>
                                    <p class="text-gray-600" x-text="'NIM: ' + selectedUser.nim"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <template x-if="selectedUser">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Semester Aktif</p>
                                    <p class="font-semibold summary-semester" x-text="selectedUser.semester_aktif"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full summary-status"
                                        :class="getStatusClass(selectedUser.status_mahasiswa)"
                                        x-text="capitalizeFirst(selectedUser.status_mahasiswa)">
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Tunggakan</p>
                                    <p class="font-semibold text-red-600 summary-tunggakan"
                                        x-text="formatCurrency(selectedUser.total_tunggakan)">
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tabs and Tagihan List -->
            <div class="bg-white rounded-lg shadow-sm" x-data="{ activeTab: 'belum_bayar' }">
                <div class="border-b">
                    <nav class="flex space-x-4 px-6" aria-label="Tabs">
                        <button @click="activeTab = 'belum_bayar'"
                            :class="{ 'border-primary text-primary': activeTab === 'belum_bayar' }"
                            class="px-3 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300">
                            Belum Bayar
                        </button>
                        <button @click="activeTab = 'cicilan'"
                            :class="{ 'border-primary text-primary': activeTab === 'cicilan' }"
                            class="px-3 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300">
                            Cicilan
                        </button>
                        <button @click="activeTab = 'lunas'"
                            :class="{ 'border-primary text-primary': activeTab === 'lunas' }"
                            class="px-3 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300">
                            Lunas
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <template x-if="selectedUser && selectedUser.tagihan">
                        <div>
                            <template x-for="tagihan in selectedUser.tagihan[activeTab]" :key="tagihan.id">
                                <div class="bg-white border rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold" x-text="tagihan.jenis_pembayaran.nama"></h3>
                                            <p class="text-sm text-gray-600"
                                                x-text="'Jatuh Tempo: ' + formatDate(tagihan.tanggal_jatuh_tempo)">
                                            </p>
                                            <div x-show="tagihan.jenis_pembayaran.dapat_dicicil"
                                                class="text-sm text-blue-600 mt-1">
                                                <span class="material-icons text-sm align-middle">info</span>
                                                <span>Dapat dicicil</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold" x-text="formatCurrency(tagihan.total_tagihan)"></p>
                                            <p class="text-sm text-gray-600"
                                                x-text="'Terbayar: ' + formatCurrency(tagihan.total_terbayar)"></p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full"
                                                :style="'width: ' + getProgressWidth(tagihan.total_terbayar, tagihan
                                                    .total_tagihan) + '%'">
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500 text-right"
                                            x-text="getProgressText(tagihan.total_terbayar, tagihan.total_tagihan)">
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button @click="handleEditTagihan(tagihan)"
                                            class="text-sm px-3 py-1 rounded border border-primary text-primary hover:bg-primary hover:text-white">
                                            Edit
                                        </button>
                                        <button @click="handleDeleteTagihan(tagihan.id)"
                                            class="text-sm px-3 py-1 rounded border border-red-500 text-red-500 hover:bg-red-500 hover:text-white">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            @keydown.escape.window="showCreateModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="handleCreateTagihan">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Tambah Tagihan Baru</h3>
                                <button type="button" @click="showCreateModal = false"
                                    class="text-gray-400 hover:text-gray-500">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
                                    <select name="jenis_pembayaran_id" x-model="createForm.jenis_pembayaran_id"
                                        @change="handleJenisPembayaranChange($event)" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                        <option value="">Pilih Jenis Pembayaran</option>
                                        @foreach ($jenisPembayaran as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Tagihan</label>
                                    <div class="relative mt-1">
                                        <input type="text" x-model="createForm.formatted_nominal" disabled
                                            class="block w-full rounded-md border-gray-300 bg-gray-50 cursor-not-allowed shadow-sm">
                                        <div x-show="createForm.loading" class="absolute right-3 top-2">
                                            <svg class="animate-spin h-5 w-5 text-primary"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                                    <input type="date" name="tanggal_jatuh_tempo"
                                        x-model="createForm.tanggal_jatuh_tempo" required min="{{ date('Y-m-d') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="createForm.loading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Simpan
                            </button>
                            <button type="button" @click="showCreateModal = false" :disabled="createForm.loading"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            @keydown.escape.window="showEditModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="handleUpdateTagihan">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Edit Tagihan</h3>
                                <button type="button" @click="showEditModal = false"
                                    class="text-gray-400 hover:text-gray-500">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <template x-if="selectedTagihan">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
                                        <input type="text" :value="selectedTagihan.jenis_pembayaran.nama" disabled
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 cursor-not-allowed shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Tagihan</label>
                                        <input type="text" :value="formatCurrency(selectedTagihan.total_tagihan)"
                                            disabled
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 cursor-not-allowed shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                                        <input type="date" x-model="selectedTagihan.tanggal_jatuh_tempo" required
                                            min="{{ date('Y-m-d') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                Update
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toast implementation
            const Toast = {
                container: null,

                init() {
                    if (!this.container) {
                        this.container = document.createElement('div');
                        this.container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                        document.body.appendChild(this.container);
                    }
                },

                show(message, type = 'success') {
                    this.init();

                    const toast = document.createElement('div');
                    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

                    toast.className =
                        `${bgColor} text-white px-6 py-3 rounded shadow-lg transition-all transform translate-x-0 duration-300 min-w-[300px]`;
                    toast.innerHTML = message;

                    this.container.appendChild(toast);

                    setTimeout(() => {
                        toast.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                },

                success(message) {
                    this.show(message, 'success');
                },

                error(message) {
                    this.show(message, 'error');
                }
            };

            // Main Tagihan Manager
            function tagihanManager() {
                return {
                    showUserDetail: false,
                    showCreateModal: false,
                    showEditModal: false,
                    selectedUser: null,
                    selectedTagihan: null,
                    searchQuery: '',
                    createForm: {
                        jenis_pembayaran_id: '',
                        tanggal_jatuh_tempo: '',
                        nominal: 0,
                        loading: false,
                        formatted_nominal: 'Rp 0'
                    },

                    init() {
                        this.handleSearch();
                    },

                    handleSearch() {
                        const searchValue = this.searchQuery.toLowerCase();
                        const rows = document.querySelectorAll('.search-row');

                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            const match = text.includes(searchValue);
                            row.style.display = match ? '' : 'none';
                        });
                    },

                    showUserDetails(event) {
                        const userData = JSON.parse(event.target.dataset.user);
                        this.selectedUser = userData;
                        this.showUserDetail = true;
                        this.loadUserDetail(userData.id);
                    },

                    async loadUserDetail(userId) {
                        try {
                            const headers = {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            };

                            const response = await fetch(`/tagihan/${userId}/detail`, {
                                headers: headers
                            });

                            if (!response.ok) {
                                const errorData = await response.json().catch(() => null);
                                throw new Error(errorData?.message || `HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.tagihan) {
                                // Update selectedUser dengan data terbaru
                                this.selectedUser = {
                                    ...this.selectedUser,
                                    tagihan: this.groupTagihan(data.tagihan),
                                    tunggakan_by_jenis: data.tunggakan_by_jenis || [],
                                    tunggakan_summary: data.tunggakan_summary || {},
                                    total_tunggakan: data.tunggakan_summary?.total_tunggakan || 0,
                                    semester_aktif: data.user?.semester_aktif || this.selectedUser.semester_aktif,
                                    status_mahasiswa: data.user?.status_mahasiswa || this.selectedUser.status_mahasiswa
                                };

                                // Update tampilan summary
                                this.updateUserSummary();
                            } else {
                                throw new Error('Data tagihan tidak ditemukan');
                            }
                        } catch (error) {
                            console.error('Error loading user detail:', error);
                            Toast.error(error.message || 'Gagal memuat detail user');
                        }
                    },

                    updateUserSummary() {
                        // Update total tunggakan di summary
                        const summaryTunggakan = document.querySelector('.summary-tunggakan');
                        if (summaryTunggakan) {
                            summaryTunggakan.textContent = this.formatCurrency(this.selectedUser.total_tunggakan);
                        }

                        // Update semester aktif
                        const summarySemester = document.querySelector('.summary-semester');
                        if (summarySemester) {
                            summarySemester.textContent = this.selectedUser.semester_aktif;
                        }

                        // Update status mahasiswa
                        const summaryStatus = document.querySelector('.summary-status');
                        if (summaryStatus) {
                            const statusClass = this.getStatusClass(this.selectedUser.status_mahasiswa);
                            summaryStatus.className =
                                `px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}`;
                            summaryStatus.textContent = this.capitalizeFirst(this.selectedUser.status_mahasiswa);
                        }

                        // Update row di tabel juga
                        const userRow = document.querySelector(`tr[data-user-id="${this.selectedUser.id}"]`);
                        if (userRow) {
                            const totalTunggakanEl = userRow.querySelector('.total-tunggakan');
                            const tagihanAktifEl = userRow.querySelector('.tagihan-aktif');

                            if (totalTunggakanEl) {
                                totalTunggakanEl.textContent = this.formatCurrency(this.selectedUser.total_tunggakan);
                            }
                            if (tagihanAktifEl) {
                                tagihanAktifEl.textContent = this.selectedUser.tagihan_belum_lunas;
                            }
                        }
                    },

                    groupTagihan(tagihan) {
                        return {
                            belum_bayar: tagihan.filter(t => t.status === 'belum_bayar'),
                            cicilan: tagihan.filter(t => t.status === 'cicilan'),
                            lunas: tagihan.filter(t => t.status === 'lunas')
                        };
                    },

                    handleShowCreateModal() {
                        this.createForm = {
                            jenis_pembayaran_id: '',
                            tanggal_jatuh_tempo: '',
                            nominal: 0,
                            loading: false,
                            formatted_nominal: this.formatCurrency(0)
                        };
                        this.showCreateModal = true;
                    },

                    async handleJenisPembayaranChange(event) {
                        const id = event.target.value;
                        if (!id) {
                            this.createForm.nominal = 0;
                            this.createForm.formatted_nominal = this.formatCurrency(0);
                            return;
                        }

                        this.createForm.loading = true;
                        try {
                            const response = await fetch(`/tagihan/jenis-pembayaran/${id}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) {
                                throw new Error('Gagal memuat data jenis pembayaran');
                            }

                            const data = await response.json();
                            this.createForm.nominal = data.nominal;
                            this.createForm.formatted_nominal = this.formatCurrency(data.nominal);
                        } catch (error) {
                            console.error('Error:', error);
                            Toast.error(error.message || 'Gagal memuat detail jenis pembayaran');
                            this.createForm.nominal = 0;
                            this.createForm.formatted_nominal = this.formatCurrency(0);
                        } finally {
                            this.createForm.loading = false;
                        }
                    },

                    async handleCreateTagihan(event) {
                        event.preventDefault();

                        try {
                            if (!this.createForm.jenis_pembayaran_id) {
                                Toast.error('Pilih jenis pembayaran terlebih dahulu');
                                return;
                            }

                            const response = await fetch(`/tagihan/${this.selectedUser.id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    jenis_pembayaran_id: this.createForm.jenis_pembayaran_id,
                                    tanggal_jatuh_tempo: this.createForm.tanggal_jatuh_tempo
                                })
                            });

                            const result = await response.json();

                            if (response.ok) {
                                this.showCreateModal = false;
                                Toast.success(result.message || 'Tagihan berhasil dibuat');
                                event.target.reset();
                                await this.loadUserDetail(this.selectedUser.id);
                            } else {
                                throw new Error(result.message || 'Terjadi kesalahan saat membuat tagihan');
                            }
                        } catch (error) {
                            console.error('Error creating tagihan:', error);
                            Toast.error(error.message || 'Gagal membuat tagihan');
                        }
                    },

                    handleEditTagihan(tagihan) {
                        this.selectedTagihan = {
                            ...tagihan
                        }; // Create a copy
                        this.showEditModal = true;
                    },

                    async handleUpdateTagihan() {
                        try {
                            if (!this.selectedTagihan) {
                                throw new Error('Tidak ada tagihan yang dipilih');
                            }

                            const response = await fetch(`/tagihan/${this.selectedUser.id}/${this.selectedTagihan.id}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    tanggal_jatuh_tempo: this.selectedTagihan.tanggal_jatuh_tempo
                                })
                            });

                            const result = await response.json();

                            if (response.ok) {
                                this.showEditModal = false;
                                Toast.success(result.message || 'Tagihan berhasil diperbarui');
                                await this.loadUserDetail(this.selectedUser.id);
                            } else {
                                throw new Error(result.message || 'Terjadi kesalahan saat memperbarui tagihan');
                            }
                        } catch (error) {
                            console.error('Error updating tagihan:', error);
                            Toast.error(error.message || 'Gagal memperbarui tagihan');
                        }
                    },

                    async handleDeleteTagihan(tagihanId) {
                        if (!confirm('Apakah Anda yakin ingin menghapus tagihan ini?')) {
                            return;
                        }

                        try {
                            const response = await fetch(`/tagihan/${this.selectedUser.id}/${tagihanId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const result = await response.json();

                            if (response.ok) {
                                Toast.success(result.message || 'Tagihan berhasil dihapus');
                                await this.loadUserDetail(this.selectedUser.id);
                            } else {
                                throw new Error(result.message || 'Terjadi kesalahan saat menghapus tagihan');
                            }
                        } catch (error) {
                            console.error('Error deleting tagihan:', error);
                            Toast.error(error.message || 'Gagal menghapus tagihan');
                        }
                    },

                    formatCurrency(amount) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(amount || 0);
                    },

                    formatDate(date) {
                        if (!date) return '-';
                        return new Date(date).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    },

                    getStatusClass(status) {
                        const statusClasses = {
                            'aktif': 'bg-green-100 text-green-800',
                            'cuti': 'bg-yellow-100 text-yellow-800',
                            'do': 'bg-red-100 text-red-800'
                        };
                        return statusClasses[status] || 'bg-gray-100 text-gray-800';
                    },

                    getProgressWidth(terbayar, total) {
                        if (!total || !terbayar) return '0';
                        return Math.min(100, (terbayar / total * 100)).toFixed(2);
                    },

                    getProgressText(terbayar, total) {
                        if (!total || !terbayar) return '0% terbayar';
                        return `${Math.min(100, Math.round((terbayar / total * 100)))}% terbayar`;
                    },

                    capitalizeFirst(string) {
                        if (!string) return '';
                        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
                    }
                };
            }
        </script>
    @endpush
@endsection
