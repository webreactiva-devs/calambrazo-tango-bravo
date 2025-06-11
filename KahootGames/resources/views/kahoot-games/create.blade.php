<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear nuevo Kahoot</h2>
    </x-slot>

    <div class="flex justify-center w-full mt-[35px]">
        <form method="POST" action="{{ route('kahoot-games.store') }}" class="w-1/2 bg-white p-8 rounded shadow space-y-4">
            @csrf

            <div>
                <label for="contest_name" class="block text-sm font-medium text-gray-900">Nombre del concurso</label>
                <input id="contest_name" name="contest_name" type="text" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="event_date" class="block text-sm font-medium text-gray-900">Fecha de celebración</label>
                <input id="event_date" name="event_date" type="date" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="participants" class="block text-sm font-medium text-gray-900">Número de participantes</label>
                <input id="participants" name="participants" type="number" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="btn-accion">
                    Guardar
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
