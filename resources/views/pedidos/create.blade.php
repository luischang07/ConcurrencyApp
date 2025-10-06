@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Crear Pedido de Medicamentos</h1>

        {{-- Alertas personalizadas --}}
        <div x-show="alertas.length > 0" class="mb-6">
            <template x-for="alerta in alertas" :key="alerta.id">
                <div x-show="alerta.visible" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2" :class="getAlertClasses(alerta.type)">
                    <div class="flex items-center">
                        <div x-html="getAlertIcon(alerta.type)"></div>
                        <span x-text="alerta.message"></span>
                    </div>
                    <button @click="removeAlert(alerta.id)"
                        class="ml-4 text-current opacity-70 hover:opacity-100 transition-opacity">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

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
                    <select id="sucursal" name="sucursal" x-model="sucursal" @change="onChangeSucursal()" required
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
                                    <div class="flex justify-between items-center">
                                        <div class="flex-1">
                                            <div class="font-medium" x-text="sugerencia.nombre_comercial || sugerencia">
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Precio: $<span x-text="(sugerencia.precio_unitario || 0).toFixed(2)"></span>
                                            </div>
                                        </div>
                                        <span x-text="'Stock: ' + (sugerencia.stock || 0)" class="text-sm ml-2"
                                            :class="(sugerencia.stock || 0) > 0 ? 'text-green-600 dark:text-green-400' :
                                                'text-red-600 dark:text-red-400'"></span>
                                    </div>
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

                        {{-- Mostrar precio seleccionado --}}
                        <div x-show="medicamentoPrecio > 0" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Precio unitario:</span>
                                <span class="font-medium">$<span x-text="medicamentoPrecio.toFixed(2)"></span></span>
                            </div>
                            <div x-show="cantidad > 0"
                                class="flex justify-between mt-1 pt-1 border-t border-gray-200 dark:border-gray-600">
                                <span>Subtotal:</span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400">
                                    $<span x-text="(medicamentoPrecio * (cantidad || 0)).toFixed(2)"></span>
                                </span>
                            </div>
                        </div>
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
                                    Precio Unitario
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Cantidad
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Subtotal
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
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                                        $<span x-text="(item.precio_unitario || 0).toFixed(2)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                        <div class="flex items-center space-x-2">
                                            <button type="button" @click="decrementarCantidad(index)"
                                                class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                                                :disabled="item.cantidad <= 1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <input type="number" :value="item.cantidad"
                                                @input="actualizarCantidad(index, $event.target.value)" min="1"
                                                class="w-16 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-gray-200" />
                                            <button type="button" @click="incrementarCantidad(index)"
                                                class="w-8 h-8 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                        $<span x-text="(item.subtotal || 0).toFixed(2)"></span>
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

                {{-- Resumen del total de compra --}}
                <div
                    class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <span class="font-medium">Total de medicamentos:</span>
                            <span x-text="medicamentosItems.length"></span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-blue-600 dark:text-blue-300 font-medium">Total de la compra:</div>
                            <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                $<span x-text="totalCompra.toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
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
