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
            <h1 class="text-2xl font-bold text-gray-900">Data User</h1>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')"
                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah User
            </button>
        </div>
        <div class="mb-4">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari User</label>
                    <div class="relative">
                        <input type="text" id="searchInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pl-10"
                            placeholder="Cari berdasarkan nama, email, atau NIM...">
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

        <!-- Tabel User dengan scroll horizontal -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">

                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    No</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    NIM</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Alamat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    No Telepon</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Tahun Masuk</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Role</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $index => $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->nim }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->alamat }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->no_telepon }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->tahun_masuk }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button
                                                onclick="openEditModal('{{ $user->id }}', '{{ json_encode($user) }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-md transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>
                                            <button onclick="openDeleteModal('{{ $user->id }}')"
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
                {{ $users->links() }}
            </div>
        </div>

        <!-- Modal Tambah User -->
        <div id="createModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end sm:items-center justify-center p-4 text-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                        <!-- Modal Header -->
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
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Tambah User Baru</h3>
                                        <p class="text-sm text-gray-500">Silahkan isi form dibawah dengan lengkap</p>
                                    </div>
                                </div>
                                <button type="button"
                                    onclick="document.getElementById('createModal').classList.add('hidden')"
                                    class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="bg-white px-4 py-5">
                            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                        <input type="text" name="name" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <p class="mt-1 text-xs text-gray-500">Masukkan nama lengkap sesuai KTP/KTM</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <p class="mt-1 text-xs text-gray-500">Email akan digunakan untuk login</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">NIM</label>
                                        <input type="text" name="nim" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Contoh: 20220001">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                        <input type="text" name="no_telepon" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Contoh: 08123456789">
                                    </div>

                                    <div class="space-y-2 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                        <textarea name="alamat" required rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Tahun Masuk</label>
                                        <select name="tahun_masuk" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            @for ($i = date('Y'); $i >= 2000; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Role</label>
                                        <select name="role" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="mahasiswa">Mahasiswa</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Password</label>
                                        <input type="password" name="password" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                                    <button type="button"
                                        onclick="document.getElementById('createModal').classList.add('hidden')"
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
        </div>

        <!-- Modal Edit User -->
        <!-- Modal Edit User -->
        <div id="editModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end sm:items-center justify-center p-4 text-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                        <!-- Modal Header -->
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Edit User</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Update informasi user di bawah ini</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Content -->
                        <form id="editForm" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="bg-white px-4 pb-4 sm:p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                        <input type="text" name="name" id="editName" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" id="editEmail" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">NIM</label>
                                        <input type="text" name="nim" id="editNim" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                        <input type="text" name="no_telepon" id="editNoTelepon" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                        <textarea name="alamat" id="editAlamat" required rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tahun Masuk</label>
                                        <select name="tahun_masuk" id="editTahunMasuk" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            @for ($i = date('Y'); $i >= 2000; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Role</label>
                                        <select name="role" id="editRole" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="mahasiswa">Mahasiswa</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                                        <input type="password" name="password"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                    Update
                                </button>
                                <button type="button" onclick="closeModal('editModal')"
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Delete -->
        <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="relative bg-white rounded-lg max-w-lg w-full mx-auto shadow-xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Hapus</h3>
                    <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus user ini?</p>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <button type="button"
                                onclick="document.getElementById('deleteModal').classList.add('hidden')"
                                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600">
                                Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection
@push('scripts')
    <script>
        function openEditModal(userId, userData) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const user = JSON.parse(userData);

            // Set form action
            form.action = `/users/${userId}`;

            // Fill form fields
            document.getElementById('editName').value = user.name;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editNim').value = user.nim;
            document.getElementById('editNoTelepon').value = user.no_telepon;
            document.getElementById('editAlamat').value = user.alamat;
            document.getElementById('editTahunMasuk').value = user.tahun_masuk;
            document.getElementById('editRole').value = user.role;

            // Show modal with smooth animation
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);

            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = [
                document.getElementById('createModal'),
                document.getElementById('editModal'),
                document.getElementById('deleteModal')
            ];

            modals.forEach(modal => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = [
                    document.getElementById('createModal'),
                    document.getElementById('editModal'),
                    document.getElementById('deleteModal')
                ];

                modals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modal.id);
                    }
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();
                    const table = document.querySelector('table');
                    const trs = table.querySelectorAll('tbody tr');

                    trs.forEach(function(tr) {
                        const nama = tr.querySelector('td:nth-child(2)');
                        const email = tr.querySelector('td:nth-child(3)');
                        const nim = tr.querySelector('td:nth-child(4)');

                        if (nama && email && nim) {
                            const text = nama.textContent + email.textContent + nim.textContent;
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
    <script></script>
@endpush
