<?php
$selected = $field->getOptionsSelected($definition);
?>

<style>
	.ui-multiselect .ui-widget-header,.ui-multiselect  .ui-widget-header  a {
		color: #fff;
	}
</style>
<section class="{{$field->getClassName()}}">
	<label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
	<select class="multiselect"  multiple="multiple" name="{{ $field->getNameField()}}[]" id="{{ $field->getNameField()}}">
		@if (isset($selected) && count($selected))
			@foreach($selected as $id => $selectOption)
				<option value="{{$id}}" selected>{{$selectOption}}</option>
			@endforeach
		@endif
		@foreach ($field->getOptions($definition) as $key => $title)
			@if (!isset($selected[$key]))
				<option value="{{$key}}">{{ trim($title) }}</option>
			@endif
		@endforeach
	</select>
</section>

<script type="text/javascript">
	$(document).ready(function () {
		$.extend($.ui.multiselect.locale, {
			addAll:'{{__cms('Добавить все')}}',
			removeAll:'{{__cms('Удалить все')}}',
			itemsCount:'{{__cms('элементов выбрано')}}'
		});

		$(".multiselect").multiselect();
	});
</script>
