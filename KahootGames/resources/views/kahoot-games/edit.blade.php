
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Juego</h2>
    </x-slot>

    <form method="POST" action="{{ route('kahoot-games.update', $kahoot_game) }}" class="w-1/2 bg-white p-10 rounded shadow space-y-6 mx-auto mt-10">
        @csrf @method('PUT')

        <div>
            <label for="contest_name" class="block text-sm font-medium text-gray-900">Nombre del concurso</label>
            <input id="contest_name" name="contest_name" type="text" value="{{ $kahoot_game->nombre_concurso }}" required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="event_date" class="block text-sm font-medium text-gray-900">Fecha de celebración</label>
            <input id="event_date" name="event_date" type="date" value="{{ $kahoot_game->fecha_celebracion }}" required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="participants" class="block text-sm font-medium text-gray-900">Número de participantes</label>
            <input id="participants" name="participants" type="number" value="{{ $kahoot_game->numero_participantes }}" required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="btn-accion">
                Actualizar
            </button>
        </div>
    </form>
</x-app-layout>
