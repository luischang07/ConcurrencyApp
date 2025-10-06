@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Pedidos</h1>

        <table class="min-w-full bg-white dark:bg-gray-800 shadow rounded">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b border-gray-300/40">ID</th>
                    <th class="px-4 py-2 border-b border-gray-300/40">Sucursal</th>
                    <th class="px-4 py-2 border-b border-gray-300/40">Fecha</th>
                    <th class="px-4 py-2 border-b border-gray-300/40">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $p)
                    <tr>
                        <td class="px-4 py-2 border-r border-gray-300/40">{{ $p->id }}</td>
                        <td class="px-4 py-2 border-r border-gray-300/40">{{ optional($p->sucursal)->nombre }}</td>
                        <td class="px-4 py-2 border-r border-gray-300/40">{{ $p->fecha_hora }}</td>
                        <td class="px-4 py-2">{{ $p->estado }}</td>
                    </tr>
                @endforeach
                @if ($pedidos->isEmpty())
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">No hay pedidos
                            registrados.
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3" class="px-6 py-4 bg-white dark:bg-gray-800">
                        {{ $pedidos->links() }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
