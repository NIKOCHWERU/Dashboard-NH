@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">Manajemen User</h2>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Permission</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assign Klien</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($user->avatar)
                        <img class="h-8 w-8 rounded-full mr-3" src="{{ $user->avatar }}" alt="" referrerpolicy="no-referrer">
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="role" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm p-1 border text-sm">
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </form>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->role !== 'admin')
                    <form action="{{ route('users.toggle-event-permission', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1 rounded text-xs font-semibold {{ $user->can_manage_events ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $user->can_manage_events ? '✓ Enabled' : '✗ Disabled' }}
                        </button>
                    </form>
                    @else
                    <span class="text-xs text-gray-500 italic">Admin (Full Access)</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <form action="{{ route('users.assign-client', $user) }}" method="POST" class="flex gap-2">
                        @csrf
                        <select name="client_id" class="rounded-md border-gray-300 shadow-sm p-1 border text-sm w-40">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">Add</button>
                    </form>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach($user->clients as $client)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $client->name }}
                            </span>
                        @endforeach
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
