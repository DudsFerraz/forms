{{-- Gerador de campo para disciplina-usp --}}

<div class="{{ $field['formGroupClass'] }}" id="uspdev-forms-disciplina-usp">

    <label for="{{ $field['id'] }}" class="form-label">{{ $field['label'] }} {!! $field['requiredLabel'] !!}</label>
    <select id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="{{ $field['controlClass'] }}" @required($field['required'])>
        <option value="">Selecione uma disciplina...</option>
        @if (isset($formSubmission) && isset($formSubmission->data[$field['name']]))
            <option value="{{ $formSubmission->data[$field['name']] }}" selected>
                {{ $formSubmission->data[$field['name']] }}
                {{ \Uspdev\Replicado\Graduacao::nomeDisciplina($formSubmission->data[$field['name']]) }}</option>
        @elseif ($field['old'])
            <option value="{{ $field['old'] }}" selected>
                {{ $field['old'] }} {{ \Uspdev\Replicado\Graduacao::nomeDisciplina($field['old']) }}
            </option>
        @endif
    </select>

</div>

<script>
    @include('uspdev-forms::partials.scripts.select2-usp-helper')

    window.uspdevFormsSelect2.initOnLoad({
        selector: '#{{ $field['id'] }}',
        url: '{{ route('form.find.disciplina') }}',
        placeholder: 'Selecione uma disciplina...',
        minimumInputLength: 1,
    });
</script>
