@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Editar Cliente</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('clientes.update', $cliente) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Usuario <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="usuario" 
                        value="{{ old('usuario', $cliente->usuario) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('usuario') border-red-500 @enderror"
                        required
                    >
                    @error('usuario')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nombre" 
                        value="{{ old('nombre', $cliente->nombre) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror"
                        required
                    >
                    @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $cliente->email) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                        required
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono
                    </label>
                    <input 
                        type="text" 
                        name="telefono" 
                        value="{{ old('telefono', $cliente->telefono) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nueva Contraseña
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        autocomplete="new-password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                        placeholder="Dejar en blanco para mantener la actual"
                    >
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Deja este campo vacío si no deseas cambiar la contraseña</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Nueva Contraseña
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Confirma la nueva contraseña"
                    >
                    <p class="text-xs text-gray-500 mt-1">Solo necesario si cambias la contraseña</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        CUIL
                    </label>
                    <input 
                        type="text" 
                        name="cuil" 
                        value="{{ old('cuil', $cliente->cuil) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        CUIT
                    </label>
                    <input 
                        type="text" 
                        name="cuit" 
                        value="{{ old('cuit', $cliente->cuit) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Domicilio
                    </label>
                    <input 
                        type="text" 
                        name="domicilio" 
                        value="{{ old('domicilio', $cliente->domicilio) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Localidad
                    </label>
                    <input 
                        type="text" 
                        name="localidad" 
                        value="{{ old('localidad', $cliente->localidad) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Provincia
                    </label>
                    <input 
                        type="text" 
                        name="provincia" 
                        value="{{ old('provincia', $cliente->provincia) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="md:col-span-2 grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descuento 1 (%)</label>
                        <input
                            type="number"
                            name="descuento"
                            value="{{ old('descuento', $cliente->descuento ?? 0) }}"
                            min="0"
                            max="100"
                            step="0.01"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('descuento') border-red-500 @enderror"
                        >
                        @error('descuento')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descuento 2 (%)</label>
                        <input
                            type="number"
                            name="descuento2"
                            value="{{ old('descuento2', $cliente->descuento2 ?? 0) }}"
                            min="0"
                            max="100"
                            step="0.01"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('descuento2') border-red-500 @enderror"
                        >
                        @error('descuento2')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descuento 3 (%)</label>
                        <input
                            type="number"
                            name="descuento3"
                            value="{{ old('descuento3', $cliente->descuento3 ?? 0) }}"
                            min="0"
                            max="100"
                            step="0.01"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('descuento3') border-red-500 @enderror"
                        >
                        @error('descuento3')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>



                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">Cliente Activo</span>
                        <div class="relative inline-block">
                            <input type="hidden" name="activo" value="0">
                            <input 
                                type="checkbox" 
                                name="activo" 
                                value="1"
                                id="toggle-activo"
                                {{ old('activo', $cliente->activo) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <label 
                                for="toggle-activo"
                                class="block w-14 h-8 bg-gray-300 rounded-full cursor-pointer peer-checked:bg-green-500 transition-colors duration-300 relative"
                            >
                                <span class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-6"></span>
                            </label>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('clientes.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Actualizar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
<style>
    #toggle-activo:checked + label span {
        transform: translateX(1.5rem);
    }
</style>
@endsection