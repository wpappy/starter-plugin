<?php

namespace Wpappy_1_0_4\Core;

defined( 'ABSPATH' ) || exit;

class Media extends Feature {

	protected $upload;

	public function upload(): Media\Upload {
		return $this->get_feature( null, $this->core, 'upload', Media\Upload::class );
	}
}
