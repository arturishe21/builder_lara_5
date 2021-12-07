@if(isset($def['custom_js']) && is_array($def['custom_js']))
	@foreach($def['custom_js'] as $js)
		<script src="{{$js}}"></script>
	@endforeach
@endif

<script>
    $('.nav-tabs a').click(function () {
        var classTab = $(this).attr('class');
        var collectionsClassTab = $('.' + classTab);

        collectionsClassTab.each(function() {
            $( this ).parents('.nav-tabs').find('li').removeClass('active');
            $( this ).parents('.tab-pane.active').find('.tab-pane').removeClass('active');
            $( this ).parents('.tab-pane.active').find('.tab-pane.section_' + classTab).addClass('active');
            $( this ).parent().addClass('active');
        });
    })
</script>