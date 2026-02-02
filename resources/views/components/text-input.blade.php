@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-700 bg-gray-800 text-gray-100 focus:border-cyan-500 focus:ring-cyan-500 rounded-lg shadow-sm placeholder-gray-500']) }}>
