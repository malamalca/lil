// constants for scripts
var popupTitle = null;
var popupUrl = null;
var popupDialog = null;
	
var activePopup = null;
var activeTrigger = null;

function HidePopups(event) {
	var e = event || window.event;
	var el = e.target != null ? e.target : e.srcElement;
	
	// do not hide popup when clicking on it's blank area or on scrollbars
	if ($(el).hasClass('popup')) return false
	
	if(el.className.substr(0,7).toUpperCase() == "HITAREA") return false;
	$(".popup").each(function(){ $(this).hide(); });
	$("body").unbind('mouseup', HidePopups).unbind('keyup', HidePopups);
	$(activeTrigger).removeClass("popup_active");
	activePopup = null;
	activeTrigger = null;
	return false;
};

function popup(title, url, h, w) {
	var $this = this;
	var default_options = {
		w: 400,
		h: 500,
		modal: true,
		autoOpen: true,
		onOpen: function(e) {},
		onClose: function(e) {},
		onData: function(data) { return true; }
	};
	var options = [];
	
	if (typeof title == "string") {
		options = jQuery().extend(true, {}, default_options);
		options.title = title;
		options.url = url;
		options.w = w ? w : 400;
		options.h = h ? h : 500;
	} else {
		options = jQuery().extend(true, {}, default_options, title);
	}
	
	if ($("#dialog-form").length == 0) {
		$('<div />', {id: 'dialog-form'}).appendTo('body');
	}
	
	popupUrl = options.url;
	
	$this.submitToIframe = function() {
		// fetch form
		var post_form = $('form', popupDialog);
		
		if (post_form.length > 0) {
			// check if hidden iframe exists
			var post_iframe = $('#lil_post_iframe');
			if ($(post_iframe).length > 0) {
				var url = $(post_form).attr('action');
				url =  url + (url.indexOf('?') != -1 ? "&lil_submit=dialog" : "?lil_submit=dialog");
				
				// modify form to post to iframe
				$(post_form)
					.unbind('submit', popupFormSubmit)
					.attr('action', url)
					.attr('target', 'lil_post_iframe')
					.submit();
				
				$(post_iframe).load(function() {
					var data = $(post_iframe).contents().find('body').html();
					var contents = jQuery.parseJSON(data.substring(data.indexOf("{"), data.lastIndexOf("}") + 1));
					
					if (options.onData(contents) && typeof contents['data'] != 'undefined') {
						$(popupDialog).html(decodeURIComponent(contents['data']));
						
						// check if popup is still open
						if (typeof $(popupDialog).get(0) != 'undefined') {
							$('form', popupDialog).submit(popupFormSubmit);
						}
					}
				});
			}
		}
	}
	
	$this.popupFormSubmit = function(e) {
		var post_iframe = $('#lil_post_iframe');
		
		// do a ajax submit if frame does not exist
		// otherwise do a real submit to iframe (for file uploads)
		if (typeof $(post_iframe).get(0) == 'undefined') {
			$.post(
				popupUrl,
				$('form', popupDialog).serialize(),
				function(data) {
					if (data.substr(0,1) == '{') {
						var contents = jQuery.parseJSON(data);
						if (options.onData(contents) && typeof contents['data'] != 'undefined') {
							$(popupDialog).html(decodeURIComponent(contents['data']));
						}
					} else {
						$(popupDialog).html(data);
					}
					$('form', popupDialog).submit(popupFormSubmit);
				}
			);
		} else {
			var to = window.setTimeout($this.submitToIframe, 10);
		}
		e.preventDefault();		
		return false;
	}

	
	$("#dialog-form").html('').dialog({
		title: options.title,
		autoOpen: options.autoOpen,
		height: options.h,
		width: options.w,
		modal: options.modal,
		close: function (e) {
			popupDialog = null;
			options.onClose(e);
		},
		open: function () {
			$("#dialog-form").load(options.url,
				function(responseText, textStatus, XMLHttpRequest) {
					$("#dialog-form").dialog('option', 'position', { my: "center", at: "center", of: window });
					$('form', popupDialog).submit($this.popupFormSubmit);
				}
			);
			
			popupDialog = $(this);
			options.onOpen();
		}
	});
	
}




function popupLinkClick(element, popupId) {
	var pos = $(element).position();
	
	if (typeof popupId == "undefined") popupId = $(element).attr("id");
	
    activePopup = $("." + popupId);
	activeTrigger = element;
	
	// jqeury ui position plugin
	$("." + popupId).show().position({
		my: "left top",
		at: "left bottom",
		of: $(element),
		offset: "0 2"
		//collision: "none"
	});
	
	$(element).addClass("popup_active");

	$("body").bind('mouseup', HidePopups).bind('keyup', HidePopups);
	return false;
}

$(document).ready(function() { 

	$(".popup_link").each(function() {
		$(this).click(function() {
			return popupLinkClick(this);
		});
	});
	
	$(".inplace_link").each(function() {
		$(this).click(function() {
			var inplaceElement = $('.' + $(this).attr('id'));
			if (typeof inplaceElement != 'undefined') {
				$(this).after($(inplaceElement).show()).hide();
				$('input:first', $(inplaceElement)).focus();
			}
			
			return false;
		});
	});
	
	$('.popup').each(function(){ $(this).hide(); });
	$('.inplace').each(function(){ $(this).hide(); });
});

$(window).resize(function(){
	if (activePopup) {
		$(activePopup).position({
			my: "left top",
			at: "left bottom",
			of: $(activeTrigger),
			offset: "0 5"
		});
	}
});