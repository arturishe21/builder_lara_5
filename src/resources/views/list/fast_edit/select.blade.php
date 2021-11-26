<select onchange="TableBuilder.editFastSetField($(this))" data-name="{{$field}}" data-id="{{$idRecord}}">
	@foreach($optionsArray as $key => $option)
		<option value="{{$key}}" {{$value == $key ? 'selected' : ''}}>{{$option}}</option>
	@endforeach
</select>