@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">Data Jurusan</h1>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')"
                class="w-full sm:w-auto flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200">
                <span class="material-icons text-lg mr-2">add</span>
                Tambah Jurusan
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Nama Jurusan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Total Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($jurusans as $index => $jurusan)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $jurusan->nama_jurusan }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-sm text-primary-dark bg-primary-50 rounded-full">
                                        {{ $jurusan->users_count }} Siswa
                                    </span>
                                </td>
                                <td class="px-6 py-4 flex flex-wrap gap-2">
                                    <button
                                        onclick="document.getElementById('editModal{{ $jurusan->id }}').classList.remove('hidden')"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                        <span class="material-icons text-sm mr-1">edit</span>
                                        Edit
                                    </button>
                                    <button
                                        onclick="document.getElementById('deleteModal{{ $jurusan->id }}').classList.remove('hidden')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                        <span class="material-icons text-sm mr-1">delete</span>
                                        Hapus
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div id="editModal{{ $jurusan->id }}"
                                class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
                                <div class="flex min-h-full items-center justify-center p-4">
                                    <div class="bg-white rounded-lg w-full max-w-md">
                                        <form action="{{ route('jurusan.update', $jurusan->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="p-6">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Jurusan</h3>
                                                <input type="text" name="nama_jurusan" required
                                                    value="{{ $jurusan->nama_jurusan }}"
                                                    class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-shadow duration-200">
                                            </div>
                                            <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                                                <button type="button"
                                                    onclick="document.getElementById('editModal{{ $jurusan->id }}').classList.add('hidden')"
                                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div id="deleteModal{{ $jurusan->id }}"
                                class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
                                <div class="flex min-h-full items-center justify-center p-4">
                                    <div class="bg-white rounded-lg w-full max-w-md p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Hapus</h3>
                                        <p class="text-gray-500 mb-6">Yakin ingin menghapus jurusan ini?</p>
                                        <form action="{{ route('jurusan.destroy', $jurusan->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex justify-end gap-3">
                                                <button type="button"
                                                    onclick="document.getElementById('deleteModal{{ $jurusan->id }}').classList.add('hidden')"
                                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                                    Hapus
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Modal -->
        <div id="createModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="bg-white rounded-lg w-full max-w-md">
                    <form action="{{ route('jurusan.store') }}" method="POST">
                        @csrf
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Jurusan</h3>
                            <input type="text" name="nama_jurusan" required placeholder="Nama Jurusan"
                                class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-shadow duration-200">
                        </div>
                        <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .animate-fade-in {
                animation: fadeIn 0.3s ease-in;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }
        </style>
    @endpush
@endsection
