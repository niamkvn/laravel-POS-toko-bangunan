@props(['variant' => 'primary', 'type' => 'button'])
<button type="{{ $type }}" {{ $attributes->merge(['class' => "btn btn-{$variant} btn-nb"]) }}>
    {{ $slot }}
</button>
