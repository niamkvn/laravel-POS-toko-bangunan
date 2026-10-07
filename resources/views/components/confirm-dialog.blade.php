@props(['message' => 'Yakin ingin melakukan aksi ini?'])
{{ $attributes->merge(['onsubmit' => "return confirm('{$message}')"]) }}
