<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Juegos Kahoot</h2>
    </x-slot>

    <div class="py-4 px-6 mt-4">

        <a href="{{ route('kahoot-games.create') }}" class="btn-accion">
            Crear nuevo Kahoot
        </a>

        {{-- Formulario oculto para orden y paginación --}}
        <form id="order_form" method="POST" action="{{ route('kahoot-games.filter') }}">
            @csrf
            <input type="hidden" name="order_by" id="order_by" value="{{ $order_by }}">
            <input type="hidden" name="order" id="order" value="{{ $order }}">
            <input type="hidden" name="page" id="pageInput" value="1">

            <div class="mb-4 flex gap-2 mt-8">
                <input type="text" name="search_by_name" placeholder="Buscar por nombre..." value="{{ $search_by_name }}"
                       class="border rounded px-3 py-1 w-full" />
                <button type="submit" class="btn-accion">Buscar</button>
            </div>
        </form>

        <table class="min-w-full mt-6 bg-white border border-gray-200 rounded shadow-sm">
            <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-2 font-bold">
                    <button type="button" onclick="orderedBy('nombre_concurso')" class="flex items-center gap-1">
                        Nombre
                        @if ($order_by == 'nombre_concurso')
                            <span class="text-sm">{{ $order == 'asc' ? '↑' : '↓' }}</span>
                        @else
                            <span class="text-gray-400">⇅</span>
                        @endif
                    </button>
                </th>
                <th class="px-4 py-2 font-bold">
                    <button type="button" onclick="orderedBy('fecha_celebracion')" class="flex items-center gap-1">
                        Fecha
                        @if ($order_by == 'fecha_celebracion')
                            <span class="text-sm">{{ $order == 'asc' ? '↑' : '↓' }}</span>
                        @else
                            <span class="text-gray-400">⇅</span>
                        @endif
                    </button>
                </th>
                <th class="px-4 py-2 font-bold">
                    <button type="button" onclick="orderedBy('numero_participantes')" class="flex items-center gap-1">
                        Participantes
                        @if ($order_by == 'numero_participantes')
                            <span class="text-sm">{{ $order == 'asc' ? '↑' : '↓' }}</span>
                        @else
                            <span class="text-gray-400">⇅</span>
                        @endif
                    </button>
                </th>
                <th class="px-4 py-2 font-bold">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($kahoot_games as $game)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $game->nombre_concurso }}</td>
                    <td class="px-4 py-2">{{ Carbon\Carbon::parse($game->fecha_celebracion)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $game->numero_participantes }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('kahoot-games.edit', $game) }}" class="text-blue-600 hover:underline">Editar</a>
                        <a href="{{ route('kahoot-games.show', $game) }}" class="text-blue-600 hover:underline">Mostrar</a>
                        <form method="POST" action="{{ route('kahoot-games.destroy', $game) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4 pagination-links">
            {!! $kahoot_games->links() !!}
        </div>
    </div>
</x-app-layout>
