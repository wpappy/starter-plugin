import { validateComponentInit } from '../../helpers/component';

export default function initControlNumber() {
	if ( validateComponentInit( 'control-number' ) ) {
		return;
	}

	$( '.customize-control-number input' ).on( 'blur', function() {
		const $this = $( this );
		const val = $this.val();
		const num = Number( val );
		const min = Number( $this.attr( 'min' ) );
		const max = Number( $this.attr( 'max' ) );

		if ( '' !== val ) {
			if ( num < min ) {
				$this.val( min );
			}

			if ( num > max ) {
				$this.val( max );
			}

			$this.trigger( 'change' );
		}
	});
}
