<?php
$selected = $field->getOptionsSelected($definition);

?>

<style>
	.ui-multiselect .ui-widget-header,.ui-multiselect  .ui-widget-header  a {
		color: #fff;
	}
</style>

<select class="multiselect" multiple="multiple" name="{{ $field->getNameField()}}[]" id="{{ $field->getNameField()}}">
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

<script type="text/javascript">
    $(document).ready(function () {
        //  $.localise('ui-multiselect', {language: 'ru', path: '/packages/vis/builder/js/multiselect_master/js/locale/'});
        $(".multiselect").multiselect();
    });
</script>
