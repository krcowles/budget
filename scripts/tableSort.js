// global object used to define how table items get compared in a sort:
var noPart1;
var noPart2;
var compare = {
	std: function(a, b) {	// standard sort - literal
		if ( a < b ) {
			return -1;
		} else {
			return a > b ? 1 : 0;
		}
	},
	date: function(a, b) {
		// incoming data value is a string "yyyy-mm-dd"
		a = a.split("-");
		b = b.split("-");
		/**
		 * [0] is the year (this can be ignored as all reports generated
		 * are for the same year); [1] is month, [2] is day, so first, 
		 * sort by month:
		 */
		let ret_val = 0;
		let moa = Number(a[1]);
		let mob = Number(b[1]);
		// if moa > mob, no need to check day
		if (moa > mob) {
			ret_val = 1;
		} else if (moa < mob) {
			ret_val = -1;
		} else { // month values are identical
			let daya = Number(a[2]);
			let dayb = Number(b[2]);
			if (daya > dayb) {
				ret_val = 1;
			} else if (daya < dayb) {
				ret_val = -1;
			} // else default value of  0
		}
		return ret_val;
	},
	amt: function(a, b) {
		// number (a string) may not have a decimal point, so:
		if (a.indexOf(".") === -1) {
			a += ".00";
		}
		if (b.indexOf(".") === -1) {
			b += ".00";
		}
		let ret_val = 0;
		a = a.split(".");
		b = b.split(".");
		a = Number(a[0]) * 100 + Number(a[1]);
		b = Number(b[0]) * 100 + Number(b[1]);
		if (a > b) {
			ret_val = 1;
		} else if (a < b) {
			ret_val = -1;
		} else { // the characteristics are equal, so compare 'cents' values
			let mana = Number(a[1]);
			let manb = Number(b[1]);
			if (mana > manb) {
				ret_val = 1;
			} else if (mana < manb) {
				ret_val = -1
			} // otherwise they are the same - ret_val = 0
		}
		return a - b;
	},
	// 'lan' will need work if commas are allowed for amts (e.g. see 'amt')
	lan: function(a, b) {    // "Like A Number": extract numeric portion for sort
		// commas allowed in numbers, so;
		var indx = a.indexOf(',');
		if ( indx < 0 ) {
			a = parseFloat(a);
		} else {
			noPart1 = parseFloat(a);
			msg = a.substring(indx + 1, indx + 4);
			noPart2 = msg.valueOf();
			a = noPart1 + noPart2;
		}
		indx = b.indexOf(',');
		if ( indx < 0 ) {
			b = parseFloat(b);
		} else {
			noPart1 = parseFloat(b);
			msg = b.substring(indx + 1, indx + 4);
			noPart2 = msg.valueOf();
			b = noPart1 + noPart2;
		}
		return a - b;
	},
	sel: function(a, b) {	// standard sort - literal
		if ( a < b ) {
			return -1;
		} else {
			return a > b ? 1 : 0;
		}
	},
	inp: function(a, b) {	// standard sort - literal
		if ( a < b ) {
			return -1;
		} else {
			return a > b ? 1 : 0;
		}
	}
};  // end of COMPARE object

$( function() {  // doc.ready

$('.sortable').each( function() {
	var $table = $(this);
	var $tbody = $table.find('tbody');
	var $controls = $table.find('th');
	var rows = $tbody.find('tr').toArray();
	
	$controls.off('click').on('click', function() {
		$header = $(this);
		var order = $header.data('sort');
		var column;
		
		if ($header.is('.ascending') || $header.is('descending')) {
			$header.toggleClass('ascending descending');
			$tbody.append(rows.reverse());
		} else {
			$header.addClass('ascending');
			$header.siblings().removeClass('ascending descending');
			if (compare.hasOwnProperty(order)) {  // compare object needs method for var order
				column =$controls.index(this);
				if (order === 'sel') {
					rows.sort(function(a, b) {
						a = $(a).find('td').eq(column).children().eq(0).attr('id');
						b = $(b).find('td').eq(column).children().eq(0).attr('id');
						let x = '#' + a + ' option:selected';
						let y = '#' + b + ' option:selected'; 
						a = $(x).text();
						b = $(y).text();
						return compare[order](a, b);
					});
				} else if (order === 'inp') {
						rows.sort(function(a, b) {
							a = $(a).find('td').eq(column).children().eq(0).val();
							b = $(b).find('td').eq(column).children().eq(0).val();
							return compare[order](a, b);

						});
				} else {
					rows.sort( function(a, b) {
						a = $(a).find('td').eq(column).text();
						b = $(b).find('td').eq(column).text();
						return compare[order](a, b);
					});
				}
				$tbody.append(rows);
			}  // end compare
		}  // end else
		return;
	});

});  // end sortable each loop

}); // end of page-loading wait statement