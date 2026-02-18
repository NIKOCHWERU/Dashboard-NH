@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Klien</h2>
            <p class="text-sm text-gray-500">Kelola informasi klien, PIC, dan status kontrak.</p>
        </div>
        @if(auth()->user()->isAdmin())
            <button onclick="openClientModal()"
                class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Klien
            </button>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PIC
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Layanan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status
                            Perkara</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontrak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $client->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $client->category === 'retainer' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $client->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-600 font-medium">{{ $client->pic->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500">{{ $client->service_type ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500">{{ $client->case_status ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500">
                                    @if($client->retainer_contract_end)
                                        {{ $client->retainer_contract_end->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full 
                                            {{ $client->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $client->status === 'inactive' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $client->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                    {{ $client->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <div class="flex justify-center items-center gap-2">
                                    @if(auth()->user()->isAdmin())
                                        <button onclick="editClient({{ json_encode($client) }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 p-1.5 rounded-lg"
                                            title="Edit Klien">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus klien ini?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors bg-red-50 p-1.5 rounded-lg"
                                                title="Hapus Klien">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Akses Terbatas</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Client Modal -->
    <div id="clientModal"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[60] backdrop-blur-sm transition-opacity duration-300 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Klien Baru</h3>
                <button onclick="closeClientModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form id="clientForm" action="{{ route('clients.store') }}" method="POST" class="p-6">
                @csrf
                <div id="methodField"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Klien
                            *</label>
                        <input type="text" name="name" id="clientName" required
                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori
                            *</label>
                        <select name="category" id="clientCategory" required
                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                            <option value="retainer">Retainer</option>
                            <option value="perorangan">Perorangan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis
                            Layanan</label>
                        <input type="text" name="service_type" id="serviceType" placeholder="e.g., Litigasi, Konsultasi"
                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status
                            Perkara</label>
                        <input type="text" name="case_status" id="caseStatus" placeholder="e.g., Berjalan, Selesai"
                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">PIC
                            (Karyawan)</label>
                        <select name="pic_id" id="picId"
                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                            <option value="">-- Pilih PIC --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kontrak Berakhir
                            (Retainer)</label>
                        <input type="date" name="retainer_contract_end" id="contractEnd"
                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status *</label>
                        <select name="status" id="clientStatus" required
                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Izin Akses Berkas
                            (Users)</label>
                        <div class="h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($users as $user)
                                    <label
                                        class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:shadow-sm transition cursor-pointer">
                                        <input type="checkbox" name="access_user_ids[]" value="{{ $user->id }}"
                                            class="access-user-checkbox rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                                        <span class="text-sm text-gray-700">{{ $user->name }}</span>
                                        @if($user->isAdmin())
                                            <span class="text-[10px] bg-gray-200 text-gray-600 px-1 rounded">Admin</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="closeClientModal()"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-dark shadow-lg shadow-primary/20 transition-all">Simpan
                        Klien</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openClientModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Klien Baru';
            document.getElementById('clientForm').action = "{{ route('clients.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('clientForm').reset();
            document.getElementById('clientModal').classList.remove('hidden');
        }

        function closeClientModal() {
            document.getElementById('clientModal').classList.add('hidden');
        }

        function editClient(client) {
            document.getElementById('modalTitle').textContent = 'Edit Klien';
            document.getElementById('clientForm').action = `/clients/${client.id}`;
            document.getElementById('methodField').innerHTML = '@method("PUT")';

            document.getElementById('clientName').value = client.name;
            document.getElementById('clientCategory').value = client.category;
            document.getElementById('serviceType').value = client.service_type || '';
            document.getElementById('caseStatus').value = client.case_status || '';
            document.getElementById('picId').value = client.pic_id || '';
            document.getElementById('clientStatus').value = client.status;

            // Reset checkboxes
            document.querySelectorAll('.access-user-checkbox').forEach(cb => cb.checked = false);

            // Check included users
            if (client.users && client.users.length > 0) {
                const userIds = client.users.map(u => u.id);
                document.querySelectorAll('.access-user-checkbox').forEach(cb => {
                    if (userIds.includes(parseInt(cb.value))) {
                        cb.checked = true;
                    }
                });
            }

            if (client.retainer_contract_end) {
                // Format ISO date string to YYYY-MM-DD for input[type=date]
                const date = new Date(client.retainer_contract_end);
                document.getElementById('contractEnd').value = date.toISOString().split('T')[0];
            } else {
                document.getElementById('contractEnd').value = '';
            }

            document.getElementById('clientModal').classList.remove('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('clientModal');
            if (event.target == modal) {
                closeClientModal();
            }
        }
    </script>
@endsection