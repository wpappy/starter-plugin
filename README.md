# Wpappy Starter Plugin

Boilerplate for developing a WordPress plugin (hereinafter referred to as the application) using WebPack, [Wpappy library](https://github.com/wpappy/wpappy) and other helpful solutions.

- [System Requirements](#system-requirements)
- [Wpappy Library](#wpappy-library)
- [CLI Commands (Composer)](#cli-commands-composer)
- [Questions and Answers](#questions-and-answers)
- [License](#license)

## System Requirements
- PHP: ^7.2.0
- WordPress: ^5.0.0
- Composer
- Node.js: ~14.0.0
- NPM

## Wpappy Library
Read [the "Documentation" section](https://github.com/wpappy/wpappy/blob/main/README.md#documentation) in the `README.md` file of the Wpappy repository to get started with the library.

## CLI Commands (Composer)
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

### What do I need to do before starting development?
- Create `sources/config.json` file (or edit `sources/config-default.json` for the default repository configuration) and fill it with the following data:
```json
{
  "browsersync": {
    "proxy": "http://mydomain.loc", // Domain from the local server.
    "port": 9999 // Port for the BrowserSync server.
  }
}
```
- Search for `My_Plugin` in the application root directory to capture default PHP namespace and replace it with your own.
- Also search for `my-plugin` to replace the default CSS class prefixes.
- Then run following command in the application root directory to install Composer and Node.js dependencies:
```bash
composer install && cd sources && npm install
```
- Now everything is ready, let's create. 😉

### How I can add entry points for JS or CSS to the WebPack process?
Just add an entry to the `sources/entry.json` file, using the existing example.

### Do I need to somehow declare the image or font files that I've added to the `sources/images` or `sources/fonts` directories?
No, WebPack automatically deploys it to the `assets` directory with the same directory hierarchy. In addition, the images will be auto-optimized without quality loss.

### Why aren't the `assets` and partially `vendor` directories ignored?
We store an application in a repository as ready to use. If this is not what you need in your case, just add these directories to the `.gitignore` file.

## License
Wpappy Starter Plugin is free software, and is released under the terms of the GPL (GNU General Public License) version 2 or (at your option) any later version. See [LICENSE](https://github.com/wpappy/starter-plugin/blob/main/LICENSE).
