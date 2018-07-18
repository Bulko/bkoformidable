(function ($) {
	$( ".bkoformidable " ).on( "submit", function( e )
	{
		e.preventDefault();
		jQuery.ajax({
			type:'POST',
			url : bkoFormidable.ajax_url + '?' + $( this ).serialize(),
			data : {
				'action': 'bkoFormidableSubmitForm'
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

})(jQuery);
