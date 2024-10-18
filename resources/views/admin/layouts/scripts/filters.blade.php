        $("#filters input:not(.no-filter), #filters select:not(.no-filter)").each(function (e) {
            var _name = $(this).attr('name');
            if (_name) {
                applyDatatableFilter(dataTable, _name, $(this).val());
            }
        });
        $("#filters input:not(.no-filter), #filters select:not(.no-filter)").on('change', function (e) {
            var _name = $(this).attr('name');
            if (_name) {
                applyDatatableFilter(dataTable, _name, $(this).val());
            }
        });

        var dtr = $('#daterange-filter').daterangepicker({
            //autoApply: true,
            ranges: {
                'Today': [moment().startOf('day'), moment().endOf('day')],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('month')],
                'Clear': ['', '']
            }
        }, function (start, end, lebel) {
            var value = {'from': '', 'to': ''};
            if (lebel && lebel.toLowerCase() === 'clear') {
                dtr.data().daterangepicker.startDate = moment();
                dtr.data().daterangepicker.endDate = moment();
                dtr.val('');
            } else if (start && end) {
                value = {
                    'from': start.format('YYYY-MM-DD'),
                    'to': end.format('YYYY-MM-DD')
                };
                $('#daterange-filter').val(start.format('MM-DD-YYYY') + ' - ' + end.format('MM-DD-YYYY'));
            } else {
                $('#daterange-filter').val('');
            }
            applyDatatableFilter(dataTable, 'dates', value);
        }).on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            $(this).data().daterangepicker.startDate = moment();
            $(this).data().daterangepicker.endDate = moment();
            applyDatatableFilter(dataTable, 'dates', {'from': '', 'to': ''});
        });
