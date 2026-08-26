<div class="p-6">

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <label class="flex items-center gap-2 text-zinc-700 dark:text-zinc-200">
            <input type="checkbox" wire:model.live="soloNoLeidas" class="rounded border-gray-300">
            Solo no leídas
        </label>

        <button wire:click="marcarTodasLeidas"
            class="rounded bg-blue-600 px-3 py-2 text-sm text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
            Marcar todas como leídas
        </button>
    </div>

    <div class="space-y-2">
        @forelse ($notificaciones as $item)
            <div wire:click="marcarLeida({{ $item->id }})"
                 class="cursor-pointer rounded-lg border border-gray-200 p-4 shadow-sm transition dark:border-zinc-700 {{ $item->leido ? 'bg-white dark:bg-zinc-900' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-zinc-800 dark:text-zinc-100">
                            @unless($item->leido)
                                <span class="mr-1 inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                            @endunless
                            {{ $item->titulo }}
                        </p>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $item->mensaje }}</p>
                    </div>
                    <span class="whitespace-nowrap text-xs text-zinc-400 dark:text-zinc-500">{{ $item->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <p class="py-8 text-center text-gray-500 dark:text-zinc-400">No tienes notificaciones.</p>
        @endforelse
    </div>

    <div class="mt-4 dark:text-zinc-200">
        {{ $notificaciones->links() }}
    </div>

</div>
