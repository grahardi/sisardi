{{-- Partial: grid pemilihan ikon. Butuh $inputId (id hidden input tujuan) --}}
<label class="form-label d-block">Pilih Ikon</label>
<div class="d-flex flex-wrap gap-2 icon-picker" data-target="{{ $inputId }}">
    @foreach($iconOptions as $class => $label)
        <button type="button" class="btn btn-outline-secondary icon-option" data-icon="{{ $class }}" title="{{ $label }}">
            <i class="{{ $class }}"></i>
        </button>
    @endforeach
</div>
