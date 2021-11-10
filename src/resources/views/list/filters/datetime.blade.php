<div style="text-align: center">
    <nobr>
    <input type="text"
           id="f-from-{{$field->getNameField() }}"
           value="{{$filterValue['from'] ?? ''}}"
           name="filter[{{ $field->getNameField()  }}][from]"
           class="form-control input-small datepicker datepicker_range" style="padding-right: 0; width: 90px; float: none; display: inline-block">

    <input type="text"
                   id="f-to-{{$field->getNameField() }}"
                   value="{{$filterValue['to'] ?? ''}}"
                   name="filter[{{ $field->getNameField()  }}][to]"
                   class="form-control input-small datepicker datepicker_range" style="padding-right: 0; width: 90px; float: none; display: inline-block">
    </nobr>
</div>

<script>

    $( function() {
        var dateFormat = "yy-mm-dd",
            from = $( "#f-from-{{$field->getNameField() }}" )
                .datepicker({
                    defaultDate: "+1w",
                    prevText: '<i class="fa fa-chevron-left"></i>',
                    nextText: '<i class="fa fa-chevron-right"></i>',
                    changeMonth: true,
                    numberOfMonths: 2,
                    dateFormat: dateFormat,
                    timeFormat: 'HH:mm:ss',
                })
                .on( "change", function() {
                    to.datepicker( "option", "minDate", getDate( this ) );
                }),
            to = $( "#f-to-{{$field->getNameField() }}" ).datepicker({
                defaultDate: "+1w",
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>',
                changeMonth: true,
                numberOfMonths: 2,
                dateFormat: dateFormat,
                timeFormat: 'HH:mm:ss',
            })
                .on( "change", function() {
                    from.datepicker( "option", "maxDate", getDate( this ) );
                });

        function getDate( element ) {
            var date;
            try {
                date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
                date = null;
            }

            return date;
        }
    } );

</script>
