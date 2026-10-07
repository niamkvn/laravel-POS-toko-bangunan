@props(['label' => null, 'name', 'options' => [], 'selected' => null, 'placeholder' => '-- Pilih --', 'required' => false, 'autoSubmit' => false])
@php
    $optionList = collect($options)->map(fn ($optLabel, $optValue) => ['value' => (string) $optValue, 'label' => $optLabel])->values();
    $currentValue = old($name, $selected);
@endphp
<div
    x-data="{
        open: false,
        value: @js($currentValue !== null ? (string) $currentValue : ''),
        options: @js($optionList),
        placeholder: @js($placeholder),
        autoSubmit: @js((bool) $autoSubmit),
        get selectedLabel() {
            const found = this.options.find(o => o.value === String(this.value));
            return found ? found.label : this.placeholder;
        },
        choose(v) {
            this.value = v;
            this.open = false;
            if (this.autoSubmit) {
                this.$nextTick(() => this.$el.closest('form')?.submit());
            }
        },
    }"
    @click.outside="open = false"
    @keydown.escape="open = false"
    class="nb-select-wrap"
>
    @if($label)<label class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>@endif
    <input type="hidden" name="{{ $name }}" x-model="value" @if($required) required @endif>
    <button type="button" class="form-select nb-select-trigger" :class="{ 'nb-select-open': open }" @click="open = !open">
        <span class="text-truncate" x-text="selectedLabel"></span>
    </button>
    <div x-show="open" x-cloak x-transition.origin.top class="nb-select-menu">
        @if($placeholder)
        <div class="nb-select-option" :class="{ 'nb-select-option-selected': value === '' }" @click="choose('')">{{ $placeholder }}</div>
        @endif
        <template x-for="opt in options" :key="opt.value">
            <div class="nb-select-option" :class="{ 'nb-select-option-selected': value === opt.value }" @click="choose(opt.value)" x-text="opt.label"></div>
        </template>
        <div class="nb-select-empty" x-show="options.length === 0">Tidak ada opsi</div>
    </div>
    @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>
