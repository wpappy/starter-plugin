import initNotices from './setting/notices';
import initPage from './setting/page';
import initControlSelect from './setting/control/select';

$( document ).on( 'ready', function() {
	initNotices();
	initPage();
	initControlSelect();
});
