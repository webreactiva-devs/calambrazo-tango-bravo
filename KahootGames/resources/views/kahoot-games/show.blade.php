<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del Juego</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6 space-y-4">
        <div>
            <span class="font-semibold text-gray-700">🎮 Nombre:</span>
            <span class="text-gray-900">{{ $kahoot_game->nombre_concurso }}</span>
        </div>

        <div>
            <span class="font-semibold text-gray-700">📅 Fecha:</span>
            <span class="text-gray-900">{{  \Carbon\Carbon::parse($kahoot_game->fecha_celebracion)->format('d/m/Y') }}</span>
        </div>

        <div>
            <span class="font-semibold text-gray-700">👥 Participantes:</span>
            <span class="text-gray-900">{{ $kahoot_game->numero_participantes }}</span>
        </div>

        <div class="flex justify-end pt-4">
            <a href="{{ route('kahoot-games.edit', $kahoot_game) }}"
               class="btn-accion">
                Editar
            </a>
        </div>
    </div>

</x-app-layout>
