<section class="{{$field->getClassName()}}">
	<label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
	<div style="position: relative;">
		<div class="div_input">
			<div class="input_content">
				{{$field->getValue()}}
			</div>
		</div>
	</div>
</section>
