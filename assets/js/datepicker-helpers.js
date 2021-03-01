function initializeDatePicker(selector, searchQuery, elementId, url, columnDefs, dbAdditionalOptions, tableName) {
    $(selector).daterangepicker({
        opens: 'left',
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        maxDate: new Date(),
        locale: {
            format: 'DD/MM/YYYY',
        }
    }, function(start, end, label) {
        searchQuery.startDate = start.format('YYYY-MM-DD');
        searchQuery.endDate = end.format('YYYY-MM-DD');
        WebApp.CreateDatatableServerside(tableName, elementId, url, columnDefs, searchQuery, dbAdditionalOptions);
    });

    return searchQuery;
}