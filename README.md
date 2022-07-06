# Wpappy Starter Plugin

Boilerplate for developing a WordPress plugin using [Wpappy library](https://github.com/wpappy/wpappy) and other helpful solutions.

- [System Requirements](#system-requirements)
- [Installation](#installation)
- [CLI Commands](#cli-commands)
- [Questions and Answers](#questions-and-answers)
- [License](#license)

## System Requirements
- PHP: ^7.2.0
- WordPress: ^5.0.0
- Composer
- Node.js: ~14.0.0
- NPM

## CLI Commands
Use following commands in the application root directory.
- Start the WebPack watcher and the BrowserSync:
```bash
composer run-script webpack-start
```
- Build the files from `source` to `assets` directory:
```bash
composer run-script webpack-build
```
- Run the `composer install` command with `--no-dev` argument:
```bash
composer run-script install-no-dev
```
- Optimize Composer autoloader:
```bash
composer run-script optimize-autoloader
```
- Get a report from the PHP Code Sniffer:
```bash
composer run-script lint-php
```

## Questions and Answers

### What do I need to do to start development?
- Create `sources/config.json` file and fill it with the following data:
```json
{
  "browsersync": {
    "proxy": "http://mydomain.loc", // Domain from the local server.
    "port": 9999 // Port for the BrowserSync server.
  }
}
```
- Then run following command in the application root directory to install Composer and Node.js dependencies:
```bash
composer install && cd sources && npm install
```

### How I can add entry points for JS or CSS to the WebPack process?
Just add an entry to the `sources/entry.json` file, using the existing example.

### Do I need to somehow declare the image or font files that I've added to the `sources/images` or `sources/fonts` directories?
No, WebPack automatically deploys it to the `assets` directory with the same directory hierarchy. In addition, the images will be auto-optimized without quality loss.

### Why aren't the `assets` and partially `vendor` directories ignored?
We store an application ready for deployment in a repository. If this is not what you need in your case, just add these directories to the `.gitignore` file.

## License
Wpappy Starter Plugin is free software, and is released under the terms of the GPL (GNU General Public License) version 2 or (at your option) any later version. See [LICENSE](https://github.com/wpappy/starter-plugin/blob/main/LICENSE).
