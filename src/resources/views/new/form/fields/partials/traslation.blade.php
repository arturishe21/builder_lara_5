<script>
    @if ($field->getTraslationField())

		var runTrans = true;

		@if ($field->getTraslationOnlyEmpty() == true)
			runTrans = $('[data-name-input={{$definition->getNameDefinition().$field->getNameField()}}]').val() == '' ? true : false;
		@endif

		if (runTrans) {
            $('[data-name-input={{$definition->getNameDefinition().$field->getNameField()}}]').keyup(function(){
                $('[data-name-input={{ $definition->getNameDefinition().$field->getTraslationField() }}]').val(TableBuilder.urlRusLat($(this).val()));
            });
		}
    @endif
</script>
