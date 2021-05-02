<span class="onoffswitch">
	<input onchange="Tree.activeToggle('{{$idRecord}}', this.checked, '{{$field}}');" type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" @if ($isChecked) checked="checked" @endif id="myonoffswitch{{$idRecord}}">
	<label class="onoffswitch-label" for="myonoffswitch{{$idRecord}}">
		<span class="onoffswitch-inner" data-swchon-text="{{__cms('ДА')}}" data-swchoff-text="{{__cms("НЕТ")}}"></span>
		<span class="onoffswitch-switch"></span>
	</label>
</span>