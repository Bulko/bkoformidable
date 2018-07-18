/**
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.1 ( 2018-04-19 )
 * @see https://stackoverflow.com/a/14966131
 *
 * @param Object table
 * @param Object oSettings
 * @return Void
 */
dataTableGenDropDown = ( table, oSettings ) => {
	const api = table.api();
	api.columns().flatten().each( ( colIdx ) => {
		const column = api.column( colIdx );
		const header = column.header();
		const data = column
				.column( colIdx )
				.data()
				.sort()
				.unique();
		const select = $('<select/>');
		if (
			header.className.indexOf( 'haveDropDown' ) !== -1
			&& header.className.indexOf( 'dropdownLoaded' ) === -1
			&& data[0] !== void 0
		)
		{

			select.appendTo(
					column.header()
				)
				.on( 'change', function () {
					column.search( $(this).val() )
						.draw();
				} )
				.append( $('<option value="" selected></option>') );
			header.className += " dropdownLoaded";
			data.each( function ( d )
			{
				if ( d !== "" )
				{
					d = cleanCsvStr( d );
					select.append( $('<option value="'+d+'">'+d+'</option>') );
				}
			} );
		}
	} );
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.1 ( 2018-04-19 )
 * @see https://stackoverflow.com/a/14966131
 *
 * @param Object table
 * @param Object oSettings
 * @return Void
 */
dataTableGenCSV = ( table, oSettings ) => {
	const $table = $(oSettings.nTableWrapper);
	let $link = $table.closest(".dataTableContainer").find(".downloadCsv")
	if ( $link.length )
	{
		const api = table.api();
		const elems = api.rows(
			{
				filter : 'applied',
				order : 'applied',
				search : 'applied',
				page : 'current'
			}
		).data();
		// need to retrive dataTable footer to buld csv header (best way to escape dropDown)
		const headers = api.columns().footer();
		let csvContent = "data:text/csv;charset=utf-8,\ufeff"; // utf8 bom in JS
		$.each( headers, ( key, value ) => {
			csvContent += '"' + cleanCsvStr( value.innerText ) + '";';
		});
		csvContent += "\r\n";
		$.each( elems, ( key, value ) => {
			$.each( value, ( k, v ) => {
				csvContent += '"' + cleanCsvStr( v ) + '";';
			});
			csvContent += "\r\n";
		});
		$link.attr("href", encodeURI(csvContent) );
		$link.attr("download", "renovart.csv");
		$("body").removeClass("ajaxLoading");
	}
}

/**
 * Disable pagination on small table
 *
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.1 ( 2018-04-19 )
 * @see https://stackoverflow.com/a/12393232
 *
 * @param Object oSettings
 * @return Void
 */
dataTableHidePaginate = ( oSettings ) => {
	if ( oSettings._iDisplayLength > oSettings.fnRecordsDisplay() || oSettings._iDisplayLength === -1 )
	{
		$(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
		$(oSettings.nTableWrapper).find('.dataTables_info').hide();
	}
	else
	{
		$(oSettings.nTableWrapper).find('.dataTables_paginate').show();
		$(oSettings.nTableWrapper).find('.dataTables_info').show();
	}
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.1 ( 2018-04-19 )
 *
 * @param String text
 * @return String
 */
cleanCsvStr = ( text ) => {
	let result = text.match( /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig );
	if ( !result )
	{
		result = text;
	}
	else
	{
		result = result[0];
	}
	if ( result.indexOf( '<pre class="comment">' ) !== -1 )
	{
		let split = result.split( '<pre class="comment">' );
		split = split[1].split( '</pre>' );
		result = split[0];
	}
	result = result.replace( RegExp( "\#", "g" ), "♯" );
	result = result.replace( RegExp( "<br />|<br/>|<br>", "g" ), "\n" );
	return result;
}


/**
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.3 ( 2018-05-09 )
 *
 * @param Object oSettings
 * @param Array aData
 * @param Inr iDataIndex
 * @return Bool
 */
$.fn.dataTableExt.afnFiltering.push(
	function( oSettings, aData, iDataIndex )
	{
		let curentDate = false;
		// Retrive first valid date value from data table
		$.each( aData, function( it, v ){
			if ( retriveDateObj(v) )
			{
				curentDate = retriveDateObj(v);
				// Returning true skips to the next iteration, equivalent to a continue in a normal loop.
				// To break a $.each loop, you have to return false in the loop callback.
				return false;
			}
		})
		//get table wrapper
		const $table = $(oSettings.nTableWrapper);
		const $wrapper = $table.closest(".dataTableContainer");
		let minDateFilter = retriveDateObj( $wrapper.find(".from").val() );
		let maxDateFilter = retriveDateObj( $wrapper.find(".to").val() );
		if ( !minDateFilter || !maxDateFilter || !curentDate )
		{
			if ( !curentDate )
			{
				$wrapper.find(".rangeSearch").remove();
			}
			return true;
		}

		minDateFilter = minDateFilter.getTime();
		maxDateFilter = maxDateFilter.getTime();
		curentDate = curentDate.getTime();
		if ( curentDate < minDateFilter )
		{
			return false;
		}
		if ( curentDate > maxDateFilter )
		{
			return false;
		}
		return true;
	}
);

/**
 * Convert string (boby format) into Date obj
 *
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.3 ( 2018-05-09 )
 *
 * @param String date
 * @return Date
 */
retriveDateObj = ( date ) => {
	if ( date !== void 0 )
	{
		let dateParts = date.split("/");
		let dateObj;
		if (
			dateParts[1] !== void 0
			&& dateParts[2] !== void 0
		)
		{
			let timeParts = dateParts[2].split(" ");
			dateParts[2] = timeParts[0];
			dateParts[1] = dateParts[1] - 1;
			if (
				timeParts[1] !== void 0
			)
			{
				timeParts = timeParts[1].split(":");
				if (
					timeParts[1] !== void 0
					&& timeParts[2] !== void 0
				)
				{
					dateObj = new Date( dateParts[2], dateParts[1], dateParts[0], timeParts[0], timeParts[1], timeParts[2] );
				}
				else
				{
					dateObj = new Date( dateParts[2], dateParts[1], dateParts[0] );
				}
			}
			else
			{
				dateObj = new Date( dateParts[2], dateParts[1], dateParts[0] );
			}
			if (
				Object.prototype.toString.call( dateObj ) === "[object Date]"
				&& !isNaN( dateObj.getTime() )
			)
			{
				return dateObj;
			}
		}
	}
	return false;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.4 ( 2018-05-23 )
 * @see https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Objets_globaux/Math/round
 *
 * @param Float number
 * @param Int precision
 * @return Float
 */
precisionRound = ( number, precision ) => {
	const factor = Math.pow(10, precision);
	return Math.round(number * factor) / factor;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since BoBy 0.0.3 ( 2018-05-21 )
 *
 * @param Float val
 * @param Float total
 * @param Float percent
 * @return Void
 */
$.fn.headerCard = function ( val, total, percent )
{
	$(this).find(".info-box-number").html( val );
	$(this).find(".progress-bar").animate( { "width": percent + "%" } );
	$(this).find(".progress-description").html( precisionRound( percent, 2) + "% (de " + total + ")" );
}

/**
 * @see  https://datatables.net/forums/discussion/28726/how-to-disable-the-warning-message#Comment_77378
 */
$.fn.dataTable.ext.errMode = 'none';

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.1 ( 2018-07-18 )
 *
 * @return Void
 */
$.fn.toggleForm = function ()
{
	let $parent = $(this).parent().parent();
	$parent.find(".placeholder").slideToggle();
	$parent.find(".form").slideToggle();
}

$(function () {
	window.configDataTable = {
		paging      : true,
		pageLength  : 50,
		ordering    : true,
		info        : true,
		autoWidth   : true,
		order       : [[ 0, "desc" ]],
		lengthMenu: [
			[10, 50, 100, 200, 500, -1],
			[10, 50, 100, 200, 500, "Tout"]
		],
		responsive:{
			details: true
		},
		language: {
			processing:     "Traitement en cours...",
			search:         "Rechercher&nbsp;:",
			lengthMenu:     "Nombre d'&eacute;l&eacute;ments à afficher _MENU_ ",
			info:           "Affichage des &eacute;l&eacute;ments _START_ &agrave; _END_ sur un total de _TOTAL_",
			infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
			infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
			infoPostFix:    "",
			loadingRecords: "Chargement en cours...",
			zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
			emptyTable:     "Aucune donnée disponible dans le tableau",
			paginate: {
				first:      "Premier",
				previous:   "Pr&eacute;c&eacute;dent",
				next:       "Suivant",
				last:       "Dernier"
			},
			aria: {
				sortAscending:  ": activer pour trier la colonne par ordre croissant",
				sortDescending: ": activer pour trier la colonne par ordre décroissant"
			}
		},
		/**
		 * @author Golga <r-ro@bulko.net>
		 * @since BoBy 0.0.1 ( 2018-04-19 )
		 * @see https://stackoverflow.com/a/14966131 & https://stackoverflow.com/a/12393232
		 *
		 * @param Object oSettings
		 * @return Void
		 */
		drawCallback: function ( oSettings ) {
			dataTableHidePaginate( oSettings );
			dataTableGenCSV( this, oSettings );
			dataTableGenDropDown( this, oSettings );
		}
	};

	window.dataTable = $('table.datatables').DataTable( window.configDataTable );

	window.dataTable.on( 'error.dt', function ( e, settings, techNote, message ) {
		console.log( 'dataTable: ', message );
	});

	$('.bkoforminable .toggle-form').on('click', function(){
		$(this).toggleForm();
	});

	$('.bkoforminable .form').on('submit', function(e){
		e.preventDefault();
		let $elem = $(this);
		let $parent = $elem.parent().parent();
		$elem.toggleForm();
		$parent.find(".comment").html( $parent.find(".comment-field").val() );
		console.log( $parent.find(".comment-field").val() );
		jQuery.ajax({
			type:'POST',
			url : ajaxurl + '?' + $elem.serialize(),
			data : {
				'action': 'bkoFormidableSubmitCommentForm'
			},
			success : function ( resp )
			{
				console.log( resp );
			},
			error: function ( resp )
			{
				console.log( "error bkoFormidableSubmitForm", resp );
			}
		});
	});
});
