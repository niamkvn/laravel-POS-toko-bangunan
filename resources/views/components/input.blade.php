@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'required' => false])
@if($type === 'date')
    @php $currentValue = old($name, $value); @endphp
    <div x-data="nbDatePicker(@js($currentValue))" @click.outside="open = false" @keydown.escape="open = false" class="nb-date-wrap">
        @if($label)<label class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>@endif
        <input type="hidden" name="{{ $name }}" x-model="value" @if($required) required @endif>
        <button type="button" class="form-select nb-date-trigger" :class="{ 'nb-select-open': open }" @click="toggle()">
            <span class="text-truncate" x-text="label"></span>
            <i class="bi bi-calendar3" style="flex-shrink:0;"></i>
        </button>
        <div x-show="open" x-cloak x-transition.origin.top class="nb-date-panel" :class="{ 'nb-date-panel-right': alignRight }">
            <div class="nb-date-header">
                <div class="nb-date-nav" @click="prevMonth()"><i class="bi bi-chevron-left"></i></div>
                <span x-text="monthLabel"></span>
                <div class="nb-date-nav" @click="nextMonth()"><i class="bi bi-chevron-right"></i></div>
            </div>
            <div class="nb-date-grid">
                <template x-for="dn in dayNames" :key="dn">
                    <div class="nb-date-dayname" x-text="dn"></div>
                </template>
                <template x-for="(d, i) in days" :key="i">
                    <div
                        class="nb-date-cell"
                        :class="{ 'nb-date-cell-today': isToday(d), 'nb-date-cell-selected': isSelected(d), 'invisible': d === null }"
                        x-text="d ?? ''"
                        @click="selectDay(d)"
                    ></div>
                </template>
            </div>
            <div class="nb-date-footer">
                <button type="button" class="btn btn-sm btn-outline btn-nb" @click="clear()">Hapus</button>
                <button type="button" class="btn btn-sm btn-deep btn-nb" @click="goToday()">Hari Ini</button>
            </div>
        </div>
        @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
@else
    <div>
        @if($label)<label class="form-label" for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>@endif
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'form-control']) }} @if($required) required @endif>
        @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
@endif
