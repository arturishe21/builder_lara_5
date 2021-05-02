<select name="filter[{{ $field->getNameField() }}]" class="form-control input-small">
    <option value="">{{__cms('Выбрать')}}</option>
    @foreach ($field->getOptions() as $value => $caption)
        <option value="{{ $value }}" {{$value == $filterValue && $filterValue != '' ? 'selected' : ''}} >{{ $caption }}</option>
    @endforeach
</select>

