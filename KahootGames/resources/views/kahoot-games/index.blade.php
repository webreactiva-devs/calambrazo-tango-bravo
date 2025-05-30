<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Juegos Kahoot</h2>
    </x-slot>

    <div class="py-4 px-6 mt-4">

        <a href="{{ route('kahoot-games.create') }}" class="btn-accion">
            Crear nuevo Kahoot
        </a>

        <table class="min-w-full mt-6 bg-white border border-gray-200 rounded shadow-sm">
            <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-2 font-bold">Nombre</th>
                <th class="px-4 py-2 font-bold">Fecha</th>
                <th class="px-4 py-2 font-bold">Participantes</th>
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
    </div>
</x-app-layout>
