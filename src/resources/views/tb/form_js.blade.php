@if(isset($def['custom_js']) && is_array($def['custom_js']))
	@foreach($def['custom_js'] as $js)
		<script src="{{$js}}"></script>
	@endforeach
@endif