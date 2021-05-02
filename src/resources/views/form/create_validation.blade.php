<script>
    $("#{{$type}}_form_{{$definition->getNameDefinition()}}").validate({
        rules: {
            @if (isset($def))
                @foreach ($def['fields'] as $ident => $options)
                @php
                    $field = $controller->getField($ident);
                @endphp

                {!! $field->getClientsideValidatorRules() !!}
                @endforeach
            @endif
        },
        messages: {
            @if (isset($def))
                @foreach ($def['fields'] as $ident => $options)
                @php
                    $field = $controller->getField($ident);
                @endphp

                {!! $field->getClientsideValidatorMessages() !!}
                @endforeach
            @endif
        },
        submitHandler: function(form) {

            @if ($type == 'edit')
                TableBuilder.doEdit(
                {{request()->id}},
                "{{$definition->getNameDefinition()}}",
                '{{request('foreign_field_id')}}',
                '{!! request('foreign_attributes')!!}'
            );
            @else
                TableBuilder.doCreate("#{{$type}}_form_{{$definition->getNameDefinition()}}", '{{request('foreign_field_id')}}', '{!! request('foreign_attributes') !!}');
            @endif
        }
    });
</script>
