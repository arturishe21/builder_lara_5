<script>
        @if ($field->getTraslationField())
    var runTrans = true;
    @if ($field->getTraslationOnlyEmpty() == true)
        runTrans = $('[name={{$field->getNameField()}}]').val() == '' ? true : false;
    @endif

    if (runTrans) {
        $('[name={{$field->getNameField()}}]').keyup(function(){
            $('[name={{ $field->getTraslationField() }}]').val(TableBuilder.urlRusLat($(this).val()));
        });
    }
    @endif
</script>
