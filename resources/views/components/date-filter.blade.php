@props(['from' => null, 'to' => null])
<div class="d-flex flex-wrap gap-2 align-items-end">
    <div style="min-width:0;flex:1 1 130px;">
        <x-input type="date" label="Dari" name="date_from" :value="$from" />
    </div>
    <div style="min-width:0;flex:1 1 130px;">
        <x-input type="date" label="Sampai" name="date_to" :value="$to" />
    </div>
</div>
