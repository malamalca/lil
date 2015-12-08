function adjustBody(scrollBody) {
	var wrapper = $(scrollBody).closest('.dataTables_wrapper');
	var w_pos = $(wrapper).position();
	$(scrollBody).height($('#main').height() - w_pos.top - (wrapper.height() - $(scrollBody).height()))
	//$(scrollBody).width($(scrollBody).width() - 10);
}
	
function initDatatables() {
	var tables = $('div.index table, table.index').each(function() {
		var t_pos = $(this).position();
		var defaults = { 
			scrollY: ($('#main').height() - t_pos.top - 150) + "px",
			paging: false,
			autoWidth: true,
			responsive: true,
			language: {
                //"url": "../lil/pages/datatables"
			}
		};
		var layoutSettings = {};
		if (typeof dataTablesGlobals != "undefined") layoutSettings = dataTablesGlobals;
		
		var settings = jQuery.data(this, "settings");
		var oTable = $(this).DataTable($.extend(defaults, layoutSettings, settings));
		
        setTimeout( function () {
            oTable.columns.adjust().draw();;
        }, 500 );
		//$(window).resize(function() { oTable.columns.adjust().draw(); });
	});
	
	$(window).resize(function() {
	   var tables = $('.dataTables_scrollBody').each(function() {
	       adjustBody(this);
	   });
	});
}

jQuery.fn.dataTable.ext.type.order['lil-date-pre'] = function(data) {
    var dateSettings = {dateFormat : "MDY", dateSeparator : "-"};
    var layoutDateSettings = {};
    if (typeof dataTablesGlobals.dateSettings != "undefined") layoutDateSettings = dataTablesGlobals.dateSettings;
    
    dateSettings = $.extend(dateSettings, layoutDateSettings);
     
    var arDatea = data.split(dateSettings.dateSeparator);
    var dateFormat = dateSettings.dateFormat;
    
	return parseInt(arDatea[dateFormat.indexOf('Y')]+arDatea[dateFormat.indexOf('M')]+arDatea[dateFormat.indexOf('D')]);
};

jQuery.fn.dataTable.ext.type.order['lil-float-pre'] = function(data) {
    return LilFloatStringToFloat(data);
};