<script src="/packages/vis/builder/js/footer_all.js"></script>
<script src='/packages/vis/builder/js/plugin/editor_floala/js/languages/{{str_replace('uk', 'ua', $thisLang) }}.js'></script>

<script type="text/javascript">

    langCms = "{{$thisLang}}";


    function doAjaxLoadContent(url) {

        $(".load_page").show();
        $.get( url, { })
            .done(function( data ) {
                $("#content_admin").html(data);
                window.history.pushState(url, '', url);
                $(".load_page").hide();

                $(window).scrollTop(50);

                TableBuilder.afterLoadPage();
                Cards.init();

            }).fail(function(xhr, ajaxOptions, thrownError) {
				var errorResult = jQuery.parseJSON(xhr.responseText);
				TableBuilder.showErrorNotification(errorResult.message);
				TableBuilder.hidePreloader();

				if (errorResult.code == 401) {
				    location.href = '/login';
				}
			});
    }

    window.addEventListener('popstate', function (e) {
        $(".load_page").show();

        $.get( window.location.pathname, { })
            .done(function( data ) {
                $("#content_admin").html(data);
                $(".load_page").hide();

                $(window).scrollTop(50);
                TableBuilder.afterLoadPage();

            }).fail(function(xhr, ajaxOptions, thrownError) {
            var errorResult = jQuery.parseJSON(xhr.responseText);
            TableBuilder.showErrorNotification(errorResult.message);
            TableBuilder.hidePreloader();
        });
    });

    $(document).on('click', 'nav a', function (e) {

        var href = $(this).attr('href');

        if (href && !$(this).parents('div').hasClass('paginator_pictures')) {

            $("nav li").removeClass("active");
            $(this).parent().addClass("active");

            doAjaxLoadContent(href);
            e.preventDefault();
        }
    });

    $(document).on('click', '.pagination a', function (e) {
        if (!$(this).parents('div').hasClass('paginator_pictures') && $(this).parents('div').hasClass('paging_bootstrap_full')) {
            var href = $(this).attr('href');
            doAjaxLoadContent(href);
            e.preventDefault();
        }

    });

    $(document).ready(function() {
        pageSetUp();
        TableBuilder.afterLoadPage();

        $.timepicker.regional['ru'] = {
            timeOnlyTitle: '{{__cms('Выберите время')}}',
            timeText: '{{__cms('Время')}}',
            hourText: '{{__cms('Часы')}}',
            minuteText: '{{__cms('Минуты')}}',
            secondText: '{{__cms('Секунды')}}',
            millisecText: '{{__cms('Миллисекунды')}}',
            timezoneText: '{{__cms('Часовой пояс')}}',
            currentText: '{{__cms('Сейчас')}}',
            closeText: '{{__cms('Закрыть')}}',
            timeFormat: 'HH:mm',
            amNames: ['AM', 'A'],
            pmNames: ['PM', 'P'],
            isRTL: false
        };
        $.timepicker.setDefaults($.timepicker.regional['ru']);
        TableBuilder.doActiveMenu();

    });

</script>
