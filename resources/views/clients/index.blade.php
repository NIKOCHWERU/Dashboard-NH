@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">Manajemen Klien</h2>

@if(auth()->user()->isAdmin())
<div class="bg-white rounded-lg shadow mb-8 p-6">
    <h3 class="text-lg font-medium mb-4">Tambah Klien Baru</h3>
    <form action="{{ route('clients.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Klien *</label>
            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Kategori *</label>
            <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
                <option value="retainer">Retainer</option>
                <option value="perorangan">Perorangan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
            <input type="text" name="service_type" placeholder="e.g., Litigasi, Konsultasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status Perkara</label>
            <input type="text" name="case_status" placeholder="e.g., Berjalan, Selesai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">PIC (Karyawan)</label>
            <select name="pic_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
                <option value="">-- Pilih PIC --</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Kontrak Berakhir (Retainer)</label>
            <input type="date" name="retainer_contract_end" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status *</label>
            <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded">
                Simpan Klien
            </button>
        </div>
    </form>
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PIC</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layanan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Perkara</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontrak</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($clients as $client)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $client->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap capitalize">
                    <span class="px-2 py-1 text-xs rounded {{ $client->category === 'retainer' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $client->category }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $client->pic->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $client->service_type ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $client->case_status ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    @if($client->retainer_contract_end)
                        {{ $client->retainer_contract_end->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded 
                        {{ $client->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $client->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $client->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                    @else
                    <span class="text-gray-400">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
