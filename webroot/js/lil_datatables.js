var lilDateSetup = {
	dateFormat   : "DMY",
	dateSeparator : ".",
};

function adjustBody(scrollBody) {
	var wrapper = $(scrollBody).closest('.dataTables_wrapper');
	var w_pos = $(wrapper).position();
	$(scrollBody).height($('#main')
        .height() - w_pos.top - (wrapper.height() - $(scrollBody).height()))
		.width($(scrollBody).width() - 2);
}
	
$(document).ready(function() {
	var tables = $('div.index table, table.index').each(function() {
		var t_pos = $(this).position();
		var defaults = { 
			jQueryUI:  true,
			scrollY:   ($('#main').height() - t_pos.top - 100) + "px",
			paging:    false,
			autoWidth: true,
			drawCallback: function( settings ) { adjustBody($(this).closest('.dataTables_scrollBody')); },
			language: {
                //"url": "../lil/pages/datatables"
			}
		};
		var layoutSettings = {};
		if (typeof dataTablesGlobals != "undefined") layoutSettings = dataTablesGlobals;
		
		var settings = jQuery.data(this, "settings");
		var oTable = $(this).DataTable($.extend(defaults, layoutSettings, settings));
		
		$(window).resize(function() { oTable.columns.adjust().draw(); });
	});
});

//jQuery.fn.dataTable.ext.order['lil-date'] = function(settings, col) {
//	return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
//        var arDatea = $(td).text().split(lilDateSetup.dateSeparator);
//        var dateFormat = lilDateSetup.dateFormat;
//		return parseInt(arDatea[dateFormat.indexOf('Y')]+arDatea[dateFormat.indexOf('M')]+arDatea[dateFormat.indexOf('D')]);
//	});
//};

//jQuery.fn.dataTable.ext.order['lil-float'] = function(settings, col) {
//	return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
//        var nr = LilFloatStringToFloat($(td).text());
//        return arDatea;
//    });
//};

jQuery.fn.dataTable.ext.type.order['lil-date-pre'] = function(data) {
    var arDatea = data.split(lilDateSetup.dateSeparator);
    var dateFormat = lilDateSetup.dateFormat;
	return parseInt(arDatea[dateFormat.indexOf('Y')]+arDatea[dateFormat.indexOf('M')]+arDatea[dateFormat.indexOf('D')]);
};

jQuery.fn.dataTable.ext.type.order['lil-float-pre'] = function(data) {
    return LilFloatStringToFloat(data);
};