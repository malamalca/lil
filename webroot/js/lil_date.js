jQuery.fn.LilDate = function(options)
{
	var default_options = {
		months: { 
			1:"Jan", 2:"Feb", 3:"Mar", 4:"Apr", 5:"May", 6:"Jun", 
			7:"Jul", 8:"Aug", 9:"Sep", 10:"Oct", 11:"Nov", 12:"Dec"
		},
		days: {	
			1:1, 2:2, 3:3, 4:4, 5:5, 6:6, 7:7, 8:8, 9:9, 10:10,
			11:11, 12:12, 13:13, 14:14, 15:15, 16:16, 17:17, 18:18, 19:19, 20:20,
			21:21, 22:22, 23:23, 24:24, 25:25, 26:26, 27:27, 28:28, 29:29, 30:30, 31:31
		},
		minYear: 2000,
		maxYear: 2020,
		//dateFormat: "MDY",
		//dateSeparator: "-"
		dateFormat: "DMY",
		dateSeparator: "."
	};
	var options = jQuery().extend(default_options, options);
	var $this = this;
	
	$(this).attr('readonly', 'readonly');
	
	var years = {};
	for (i = options.minYear; i < options.maxYear; i++) {
		years[i] = i;
	}

	this.getDateRegex = function() {
		var parts = {
			'Y': '([1-9][0-9]{0,3})',
			'M': '((0[1-9])|(1[0-2]))',
			'D': '((0[1-9])|([1-2][0-9])|(3[0-1]))'
		};
		var order = options.dateFormat.split("");
		
		var regex = '^' + parts[order[0]] + options.dateSeparator +
			parts[order[1]] + options.dateSeparator +
			parts[order[2]] + options.dateSeparator +
			'$';
		
		return regex;
	}
	
	this.click(
		function(e) {
			if (SpinningWheel.slotData.length > 0) SpinningWheel.destroy();
			
			var dateFormatArray = options.dateFormat.split("");
			var dateParts = {};
			
			var today = new Date();
			for (kind in dateFormatArray) {
				if (dateFormatArray[kind] == "M") {
					dateParts[kind] = today.getMonth()+1;
				} else if (dateFormatArray[kind] == "D") {
					dateParts[kind] = today.getDate();
				} else if (dateFormatArray[kind] == "Y") {
					dateParts[kind] = today.getFullYear();
				}
			}
			
			var TestDate = new RegExp($this.getDateRegex());
			if (TestDate.test($($this).val())) {
				dateParts = $($this).val().split(options.dateSeparator);
			}
			
			for (kind in dateFormatArray) {
				if (dateFormatArray[kind] == "M") {
					
					SpinningWheel.addSlot(options.months, 'right', parseInt(dateParts[kind],10));
				} else if (dateFormatArray[kind] == "D") {
					SpinningWheel.addSlot(options.days, 'right', parseInt(dateParts[kind],10));
				} else if (dateFormatArray[kind] == "Y") {
					SpinningWheel.addSlot(years, 'right', parseInt(dateParts[kind],10));
				}
			}
			
			SpinningWheel.setDoneAction($this.swDone);
			
			SpinningWheel.open();
			e.preventDefault();
		}
	);
	
	this.lPad = function(number) {
		if (number < 10) return '0'+number;
		else return ''+number;
	}
	
	this.swDone = function() {
		var values = SpinningWheel.getSelectedValues();
		values = values['keys'];
		
		var date = '';
		for (value_key in values) {
			if (date != '') date = date + options.dateSeparator;
			date = date + $this.lPad(values[value_key]);
		}
		$($this).val(date);
	}
}