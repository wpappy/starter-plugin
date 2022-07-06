import 'jquery';
import initControlAlphaColor from './customizer/controls/alpha-color';
import initControlNumber from './customizer/controls/number';
import initSectionLink from './customizer/sections/link';

$( document ).on( 'ready', function() {
	initControlAlphaColor();
	initControlNumber();
	initSectionLink();
});
