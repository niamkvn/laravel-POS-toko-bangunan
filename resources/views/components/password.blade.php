@props(['label' => null, 'name', 'required' => false, 'placeholder' => null])
<div x-data="{ show: false }">
    @if($label)<label class="form-label" for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>@endif
    <div class="position-relative">
        <input :type="show ? 'text' : 'password'" name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'form-control']) }} style="padding-right:2.4rem;" @if($required) required @endif @if($placeholder) placeholder="{{ $placeholder }}" @endif>
        <button type="button" class="btn border-0 bg-transparent position-absolute top-50 end-0 translate-middle-y p-0 me-2" style="line-height:0;" @click="show = !show" tabindex="-1">
            <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
        </button>
    </div>
    @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>
