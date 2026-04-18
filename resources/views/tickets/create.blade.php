<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="text-xl font-semibold text-white">Abrir Chamado</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">Título</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                               placeholder="Descreva brevemente o problema"
                               class="w-full bg-gray-800 border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-700' }} rounded-lg px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-cyan-500 transition-colors">
                        @error('title')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-300 mb-1.5">Categoria</label>
                        <select id="category" name="category"
                                class="w-full bg-gray-800 border {{ $errors->has('category') ? 'border-red-500' : 'border-gray-700' }} rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-cyan-500 transition-colors">
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->value }}" {{ old('category') === $cat->value ? 'selected' : '' }}>
                                    {{ $cat->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="body" class="block text-sm font-medium text-gray-300 mb-1.5">Descrição</label>
                        <textarea id="body" name="body" rows="6"
                                  placeholder="Descreva detalhadamente o seu problema ou dúvida..."
                                  class="w-full bg-gray-800 border {{ $errors->has('body') ? 'border-red-500' : 'border-gray-700' }} rounded-lg px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-cyan-500 transition-colors resize-none">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('tickets.index') }}"
                           class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg transition-colors">
                            Abrir Chamado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
