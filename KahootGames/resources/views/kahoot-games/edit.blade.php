
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Juego</h2>
    </x-slot>

    <form method="POST" action="{{ route('kahoot-games.update', $kahoot_game) }}" class="w-1/2 bg-white p-10 rounded shadow space-y-6 mx-auto mt-10">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-900">Nombre del concurso</label>
            <input name="nombre_concurso" type="text" value="{{ $kahoot_game->nombre_concurso }}" required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-900">Fecha de celebración</label>
            <input name="fecha_celebracion" type="date" value="{{ $kahoot_game->fecha_celebracion }}" required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-900">Número de participantes</label>
            <input name="numero_participantes" type="number" value="{{ $kahoot_game->numero_participantes }}" required
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
