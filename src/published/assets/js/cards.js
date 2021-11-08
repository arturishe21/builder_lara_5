var Cards =
    {
        myLineChart : '',

        init: function()
        {
            $(".datepicker_trend").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>',
                dateFormat: "yy-mm-dd",
                //showButtonPanel: true,
                regional: ["ru"],
                onClose: function (selectedDate) {}
            });

            $('.datepicker_trend').change(function () {
                var contentTrend = $(this).parents('article');
                Cards.updateData(contentTrend);
            });

            $('.trends').each(function( index ) {
                var contentTrend = $(this).parents('article');

                Cards.updateData(contentTrend);
            });

            if ($( ".trends" ).size()) {

                var ctx = document.querySelector('canvas').getContext('2d');

                Cards.myLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Count',
                            data: [],
                            backgroundColor: 'rgb(255, 99, 132)',
                            borderColor: 'rgb(255, 99, 132)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            }
                        }
                    }
                });
            }
        },
        updateData : function (contentTrend) {
            $.ajax({
                url: '/admin/change-range-trend',
                method: 'POST',
                data: {
                    'from' : contentTrend.find('[name=trend_from]').val(),
                    'to' : contentTrend.find('[name=trend_to]').val(),
                    'model' : contentTrend.find('[name=trend_model]').val()
                },
                dataType: 'json',
                success: function (d) {

                    // assign programmatically the datasets again, otherwise data changes won't show
                    Cards.myLineChart.data.labels = d.labels;
                    Cards.myLineChart.data.datasets[0].data = d.values;

                    Cards.myLineChart.update();
                }
            });
        }
    };
Cards.init();