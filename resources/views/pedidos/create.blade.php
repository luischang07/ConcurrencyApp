@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Crear Pedido de Medicamentos</h1>

        {{-- Mostrar errores de validación --}}
        @if ($errors->any())
            <div
                class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400 px-4 py-3 rounded mb-6">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                        </svg>
                    </div>
                    <div>
                        <strong>¡Error!</strong>
                        <ul class="mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('pedidos.store') }}" method="POST" x-data="pedidoForm({{ $cadenasJson ?? '[]' }}, {{ $medicamentosJson ?? '[]' }})">
            @csrf

            {{-- Hidden inputs for preserving form data --}}
            <input type="hidden" name="cadena_old" value="{{ old('cadena', '') }}">
            <input type="hidden" name="sucursal_old" value="{{ old('sucursal', '') }}">
            <input type="hidden" name="medicamento_busqueda_old" value="{{ old('medicamento_busqueda', '') }}">
            <input type="hidden" name="cantidad_temp_old" value="{{ old('cantidad_temp', '') }}">
            <input type="hidden" name="medicamentos_old" value="{{ old('medicamentos', '[]') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="cadena" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cadena Farmacéutica *
                    </label>
                    <select id="cadena" name="cadena" x-model="cadena" @change="onChangeCadena()" required
                        class="w-full px-4 py-2 border @error('cadena') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                        <option value="">Seleccione una cadena</option>
                        <template x-for="c in cadenas" :key="c.id">
                            <option :value="c.id" x-text="c.nombre" :selected="c.id == cadena"></option>
                        </template>
                    </select>
                    @error('cadena')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sucursal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sucursal *
                    </label>
                    <select id="sucursal" name="sucursal" x-model="sucursal" required
                        class="w-full px-4 py-2 border @error('sucursal') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                        <option value="">Seleccione una sucursal</option>
                        <template x-for="s in sucursales" :key="s.id">
                            <option :value="s.id" x-text="s.nombre" :selected="s.id == sucursal"></option>
                        </template>
                    </select>
                    @error('sucursal')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Agregar Medicamentos</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="md:col-span-2 relative">
                        <label for="medicamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Medicamento
                        </label>
                        <input type="text" id="medicamento" x-model="busqueda" @input="mostrarSugerencias = true"
                            @focus="mostrarSugerencias = true" @click.away="mostrarSugerencias = false"
                            placeholder="Escriba el nombre del medicamento"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400">

                        <div x-show="mostrarSugerencias && sugerenciasFiltradas.length > 0"
                            class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <template x-for="(sugerencia, index) in sugerenciasFiltradas" :key="index">
                                <div @click="seleccionarMedicamento(sugerencia)"
                                    class="px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-600 cursor-pointer text-gray-700 dark:text-gray-200">
                                    <span x-text="sugerencia.nombre_comercial || sugerencia"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label for="cantidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cantidad
                        </label>
                        <input type="number" id="cantidad" x-model="cantidad" min="1" placeholder="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400">
                    </div>
                </div>

                <button type="button" @click="agregarMedicamento()"
                    class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors dark:bg-blue-500 dark:hover:bg-blue-600">
                    Agregar Medicamento
                </button>
                @error('medicamentos')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div x-show="medicamentosItems.length > 0" class="border-t border-gray-200 dark:border-gray-700 pt-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Medicamentos Agregados</h2>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Medicamento
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Cantidad
                                </th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            <template x-for="(item, index) in medicamentosItems" :key="index">
                                <tr class="bg-white dark:bg-gray-700">
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200" x-text="item.nombre">
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200" x-text="item.cantidad">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="eliminarMedicamento(index)"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <input type="hidden" name="medicamentos" :value="JSON.stringify(medicamentosItems)">
            </div>

            <div class="flex justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                <a href="{{ route('pedidos.index') }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit" @click="if (!validarEnvio()) $event.preventDefault()"
                    class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors dark:bg-green-500 dark:hover:bg-green-600">
                    Enviar Pedido
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
@endpush
