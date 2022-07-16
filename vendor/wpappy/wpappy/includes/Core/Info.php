<?php

namespace Wpappy_1_0_5\Core;

use Wpappy_1_0_5\Core;

defined( 'ABSPATH' ) || exit;

class Info extends Feature {

	protected $name;
	protected $url;
	protected $version;
	protected $description;
	protected $author;
	protected $author_url;
	protected $textdomain;
	protected $domain_path;
	protected $requires_php;
	protected $requires_wp;

	protected $headers = array(
		'version'      => 'Version',
		'description'  => 'Description',
		'author'       => 'Author',
		'author_url'   => 'Author URI',
		'textdomain'   => 'Text Domain',
		'domain_path'  => 'Domain Path',
		'requires_wp'  => 'Requires at least',
		'requires_php' => 'Requires PHP',
	);

	public function __construct( Core $core ) {
		parent::__construct( $core );

		if ( $this->is_theme() ) {
			$this->headers['name'] = 'Theme Name';
			$this->headers['url']  = 'Theme URI';

			$info = $this->get_data( 'style.css' );

		} else {
			$this->headers['name'] = 'Plugin Name';
			$this->headers['url']  = 'Plugin URI';

			$info = $this->get_data();
		}

		$this->name         = $info['name'];
		$this->url          = $info['url'];
		$this->version      = $info['version'];
		$this->description  = $info['description'];
		$this->author       = $info['author'];
		$this->author_url   = $info['author_url'];
		$this->textdomain   = $info['textdomain'];
		$this->domain_path  = $info['domain_path'];
		$this->requires_wp  = $info['requires_wp'];
		$this->requires_php = $info['requires_php'];
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_url(): string {
		return $this->url;
	}

	public function get_version(): string {
		return $this->version;
	}

	public function get_description(): string {
		return $this->description;
	}

	public function get_author(): string {
		return $this->author;
	}

	public function get_author_url(): string {
		return $this->author_url;
	}

	public function get_textdomain(): string {
		return $this->textdomain;
	}

	public function get_domain_path(): string {
		return $this->domain_path;
	}

	public function get_requires_php(): string {
		return $this->requires_php;
	}

	public function get_requires_wp(): string {
		return $this->requires_wp;
	}

	protected function get_data( ?string $file = null ): array {
		return get_file_data(
			$file ?
				$this->core->fs()->get_app_path( $file ) :
				$this->core->get_app_root_file(),
			$this->headers
		);
	}
}
