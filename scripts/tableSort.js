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
	date: function(a, b) {  // data using yyyy-mm-dd
		// all tables contain only 1 month's data, so sort by day
		a = a.split("-");
		b = b.split("-");
		a = Number(a[2]);
		b = Number(b[2]);
		return a - b;
	},
	amt: function(a, b) {
		a = a.split(".");
		b = b.split(".");
		a = Number(a[0]) * 100 + Number(a[1]);
		b = Number(b[0]) * 100 + Number(b[1]);
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
	} 
};  // end of COMPARE object

$( function() {  // doc.ready

$('.sortable').each( function() {
	var $table = $(this);
	var $tbody = $table.find('tbody');
	var $controls = $table.find('th');
	var rows = $tbody.find('tr').toArray();
	
	$controls.on('click', function() {
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
				
				rows.sort( function(a,b) {
					a = $(a).find('td').eq(column).text();
					b = $(b).find('td').eq(column).text();
					return compare[order](a,b);
				});
				
				$tbody.append(rows);
			}  // end compare
		}  // end else
		return;
	});

});  // end sortable each loop

}); // end of page-loading wait statement