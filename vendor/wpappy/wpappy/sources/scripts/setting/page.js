import 'jquery.are-you-sure';

export default function initPage() {
	$( '.wpappy-page' ).areYouSure({
		'change': function() {
			if ( $( this ).hasClass( 'dirty' ) ) {
				$( '.wpappy-submit-btn' ).prop( 'disabled', false );

			} else {
				$( '.wpappy-submit-btn' ).prop( 'disabled', true );
			}
		}
	});
}
