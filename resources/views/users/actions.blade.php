<div class="flex gap-2">
    {{-- Toggle Active --}}
    @can('edit users', $user)
        @switch($user->status)
            @case(\App\Models\User::STATUS_INVITED)
                <button
                    wire:click="revokeInvite({{ $user->id }})"
                    class="px-2 py-1 text-xs bg-blue-500 text-white rounded"
                >
                    Revoke Invite
                </button>
                @break
                @case(\App\Models\User::STATUS_PENDING)
                <button
                    wire:click="acceptUser({{ $user->id }})"
                    class="px-2 py-1 text-xs bg-blue-500 text-white rounded"
                >
                    Accept
                </button>

                <button
                    wire:click="rejectUser({{ $user->id }})"
                    class="px-2 py-1 text-xs bg-blue-500 text-white rounded"
                >
                    Decline
                </button>
                @break
                @case(\App\Models\User::STATUS_ACTIVE)
                    <button
                        wire:click="disableUser({{ $user->id }})"
                        class="px-2 py-1 text-xs bg-blue-500 text-white rounded"
                    >
                        Disable
                    </button>
                @break
            @case(\App\Models\User::STATUS_INACTIVE)
                <button
                    wire:click="enableUser({{ $user->id }})"
                    class="px-2 py-1 text-xs bg-blue-500 text-white rounded"
                >
                    Enable
                </button>
                @break
            @default
        @endswitch
    @endcan

    {{-- Copy --}}
    {{-- TODO: Move to username column if possible --}}
    <button
        id="copy-username-{{ $user->id }}"
        wire:click="$dispatch('copy-to-clipboard', { text: '{{ $user->username }}', element: 'copy-username-{{ $user->id }}' })"
        class="px-2 py-1 text-xs bg-gray-600 text-white rounded hover:cursor-pointer hover:scale-110"
    >
        Copy username
    </button>
    <button
        id="copy-username-{{ $user->id }}"
        wire:click="$dispatch('copy-to-clipboard', { text: '{{ $user->username }}', element: 'copy-username-{{ $user->id }}' })"
        class="px-2 py-1 text-xs bg-gray-600 text-white rounded hover:cursor-pointer hover:scale-110"
    >
        Edit
    </button>
</div>
