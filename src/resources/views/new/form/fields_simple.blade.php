@foreach($fields as $field)
    {!! $field->getFieldForm($definition) !!}
@endforeach

@if (request('foreign_field_id'))
    <input type="hidden" value="{{request('foreign_field_id')}}" name="article_id">
@endif
