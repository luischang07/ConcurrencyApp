@extends('layouts.app')

@section('title', 'Medicamentos')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold dark:text-white">Medicamentos</h1>
        </div>

        {{-- Filtro de Sucursal --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('medicamentos.index') }}" class="flex items-center gap-4">
                <div class="flex-1">
                    <label for="sucursal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Filtrar por Sucursal
                    </label>
                    <select id="sucursal" name="sucursal" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                        <option value="">Todas las sucursales (Stock total)</option>
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}"
                                {{ $sucursalSeleccionada == $sucursal->id ? 'selected' : '' }}>
                                {{ $sucursal->nombre }} - {{ $sucursal->cadena->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if ($sucursalSeleccionada)
                    <div class="pt-7">
                        <a href="{{ route('medicamentos.index') }}"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Limpiar filtro
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nombre Medicamento
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Sustancia Activa
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Precio
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Stock
                            @if ($sucursalSeleccionada)
                                <span class="text-blue-600 dark:text-blue-400">(Sucursal seleccionada)</span>
                            @else
                                <span class="text-green-600 dark:text-green-400">(Total)</span>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($medicamentos as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap dark:text-gray-200 font-medium">
                                {{ $m->nombre_comercial }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                {{ $m->sustancia_activa ?? '--' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">
                                ${{ number_format($m->precio_unitario ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $stock = $m->stock ?? 0;
                                    $stockClass =
                                        $stock > 0
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400';
                                @endphp
                                <span class="font-semibold {{ $stockClass }}">
                                    {{ $stock }} unidades
                                </span>
                                @if ($stock == 0)
                                    <span
                                        class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                        Sin stock
                                    </span>
                                @elseif ($stock < 10)
                                    <span
                                        class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                        Stock bajo
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m7-7v2m0 8v2">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">No hay medicamentos registrados</p>
                                    <p class="text-sm">Los medicamentos aparecerán aquí una vez que sean agregados al
                                        sistema.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginación --}}
            @if ($medicamentos->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    {{ $medicamentos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- Información adicional --}}
        <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">
            <p>
                <strong>Total de medicamentos:</strong> {{ $medicamentos->total() }}
                @if ($sucursalSeleccionada)
                    | <strong>Mostrando stock para:</strong>
                    @php
                        $sucursalActual = $sucursales->find($sucursalSeleccionada);
                    @endphp
                    {{ $sucursalActual ? $sucursalActual->nombre . ' - ' . $sucursalActual->cadena->nombre : 'Sucursal no encontrada' }}
                @else
                    | <strong>Mostrando:</strong> Stock total (suma de todas las sucursales)
                @endif
            </p>
        </div>
    </div>
@endsection
