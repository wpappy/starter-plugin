# Wpappy

<a href="https://packagist.org/packages/wpappy/wpappy">
  <img src="https://img.shields.io/packagist/v/wpappy/wpappy" alt="Packagist version"/>
</a>

The library that introduces a smart layer between the WordPress environment and your plugin or theme (hereinafter referred to as the application).

- [System Requirements](#system-requirements)
- [Boilerplates](#boilerplates)
- [Manual Installation](#manual-installation)
- [Documentation](#documentation)
- [Simple Use Case](#simple-use-case)
- [License](#license)

## System Requirements
- PHP: ^7.2.0
- WordPress: ^5.0.0
- Composer (optional)

## Boilerplates
The following template repositories are the best point to start developing a new WordPress application:
- [Starter Theme](https://github.com/wpappy/starter-theme)
- [Starter Plugin](https://github.com/wpappy/starter-plugin)

## Manual Installation
Run the following command in root directory of your application to install using Composer:
``` bash
composer require wpappy/wpappy
```
Alternatively, you can [download the latest release](https://github.com/wpappy/wpappy/releases/latest) directly and place unarchived folder in root directory of the application.

## Documentation
The latest documentation is published on [wpappy.dpripa.com](https://wpappy.dpripa.com).\
For convenience, it's better to start from [the entry point](https://wpappy.dpripa.com/classes/Wpappy-1-0-6-App.html) of the library.

If you need documentation for previous versions, follow these instructions:
- Install [phpDocumentor](https://www.phpdoc.org) into your system.
- Download the version you need from [the release list](https://github.com/wpappy/wpappy/releases) and unzip the downloaded archive.
- Run the following command in the unarchived folder:
``` bash
phpDocumentor
```
- After phpDocumentor has reported success, you can find the generated documentation in the `./docs/api` directory.

## Simple Use Case
The following is a simple example when Wpappy is used. It doesn't matter which environment (plugin or theme) you run this code in, Wpappy automatically detects your app's environment and provides a universal API.

#### Root File (index.php / functions.php)
```php
namespace My_App;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/wpappy/wpappy/index.php';
require_once __DIR__ . '/vendor/autoload.php';

// Always be sure that the Wpappy namespace matches the installed version of the library.
// This is because other plugin and theme may use a different version.
// For example, where 'Wpappy_x_x_x' version is x.x.x.
use Wpappy_1_0_6\App as App;

// Define a function that returns the singleton instance of Wpappy for your application.
function app(): App {
  return App::get( __NAMESPACE__, __FILE__ );
}

new Setup();
```
You can see an example of simpleton usage here. It's a structural pattern provided by Wpappy for the WordPress based applications. Read more about [simpleton](https://wpappy.dpripa.com/classes/Wpappy-1-0-6-Simpleton.html).

#### Setup.php
```php
namespace My_App;

defined( 'ABSPATH' ) || exit;

final class Setup {

  public function __construct() {
    if ( app()->simpleton()->validate( self::class ) ) {
      return;
    }

    new Config();

    app()->setup( array( $this, 'setup' ) );
  }

  public function setup(): void {
    new Setting();
    new Singular();

    app()->hook()->do_action( 'setup_complete' );
  }
}
```

#### Config.php
```php
namespace My_App;

defined( 'ABSPATH' ) || exit;

final class Config {

  public function __construct() {
    if ( app()->simpleton()->validate( self::class ) ) {
      return;
    }

    app()->i18n()->setup();

    app()->setting()->set_submit_btn( app()->i18n()->__( 'Save changes' ) )
      ->setting()->set_error_notice( app()->i18n()->__( 'Something went wrong.' ) )
      ->setting()->set_success_notice( app()->i18n()->__( 'Changes saved.' ) )
      ->setting()->setup();
  }
}
```

#### Setting.php
```php
namespace My_App;

defined( 'ABSPATH' ) || exit;

final class Setting {

  public function __construct() {
    if ( app()->simpleton()->validate( self::class ) ) {
      return;
    }

    app()->setting()->add_page(
      'general',
      'edit.php',
      app()->i18n()->__( 'My App' ),
      app()->i18n()->__( 'My App Settings' )
    );

    app()->setting()->add_tab(
      'single',
      app()->i18n()->__( 'Single Post' )
    );

    app()->setting()->add_box(
      'labels'
    )->setting()->add(
      'hello_text_label',
      'textarea',
      app()->i18n()->__( '"Hello" text label' ),
      array(
        'default' => app()->i18n()->__( 'Hello from the app!' ),
      )
    );
  }
}
```

#### Post.php
```php
namespace My_App;

defined( 'ABSPATH' ) || exit;

final class Singular {

  public function __construct() {
    if ( app()->simpleton()->validate( self::class ) ) {
      return;
    }

    add_filter( 'the_content', array( $this, 'render_hello_text_label' ) );
  }

  public function render_hello_text_label( string $content ): string {
    if ( ! is_single() ) {
      return $content;
    }

    app()->setting()->context()->add( 'general', 'single' );

    $label = app()->setting()->get( 'hello_text_label', 'labels' );

    if ( empty( $label ) ) {
      return $content;
    }

    $content .= '<div style="margin: 0 0 30px;">';
    $content .= '<div style="display: inline-block; padding: 3px 12px; font-weight: 700; font-size: 14px; color: #969696; background: #f1f1f1; border-radius: 5px;">';
    $content .= esc_html( $label );
    $content .= '</div></div>';

    return $content;
  }
}
```

## License
Wpappy is free software, and is released under the terms of the GPL (GNU General Public License) version 2 or (at your option) any later version. See [LICENSE](https://github.com/wpappy/wpappy/blob/main/LICENSE).
