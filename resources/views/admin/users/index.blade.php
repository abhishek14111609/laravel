<x-admin.panel title="User Management">
    <div class="rounded-xl bg-white p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2">Name</th>
                    <th class="py-2">Email</th>
                    <th class="py-2">Role</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b">
                        <td class="py-2">{{ $user->name }}</td>
                        <td class="py-2">{{ $user->email }}</td>
                        <td class="py-2 uppercase">{{ $user->role }}</td>
                        <td class="py-2">{{ $user->is_blocked ? 'Blocked' : 'Active' }}</td>
                        <td class="py-2">
                            @if ($user->role !== 'admin')
                                <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        class="rounded-md px-3 py-1 text-xs font-semibold {{ $user->is_blocked ? 'bg-emerald-600 text-white' : 'border border-rose-300 text-rose-700' }}">{{ $user->is_blocked ? 'Unblock' : 'Block' }}</button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-admin.panel>
