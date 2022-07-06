import 'select2';

export default function initControlSelect() {
	const $multiple = $( '.wpappy-control-select select[multiple]' );

	if ( $multiple.length ) {
		$multiple.select2();
	}
}
