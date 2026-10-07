@props(['name' => 'search', 'value' => null, 'placeholder' => 'Cari...', 'label' => null])
<div>
    @if($label)<label class="form-label">{{ $label }}</label>@endif
    <div class="position-relative">
        <i class="bi bi-search position-absolute" style="left:.75rem;top:50%;transform:translateY(-50%);opacity:.5;"></i>
        <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}" class="form-control" style="padding-left:2.2rem;">
    </div>
</div>
