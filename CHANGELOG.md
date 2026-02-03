# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.11] - 03 February 2026
### Fixed
- Rewrite to composable AbstractComponentContext

## [1.0.10] - 23 January 2026
### Fixed
- Add new GitHub Action workflows
- Copy generic CI/CD files

## [1.0.9] - 21 November 2025
### Fixed
- Convert sort order in HTML hint to 0
- Debug sortOrder
- Update composer keywords
- Update composer keywords
- Update composer keywords

## [1.0.8] - 22 October 2025
### Fixed
- Do not escape `$css()` with `escapeHtmlAttr()` but `escapeHtml()`

## [1.0.7] - 01 October 2025
### Fixed
- Move HTML hints from LokiTheme_LumaComponents to Loki_Debugger
- Update README

## [1.0.6] - 23 September 2025
### Fixed
- Rename loki.script from container to block to allow caching
- Change containers into blocks to allow for caching
- Add `.prevent` modifier to `@click` event handler
- Rename Alpine store checkout to LokiCheckout, components to LokiComponents
- Rename loki-scripts to loki.script

## [1.0.5] - 28 August 2025
### Fixed
- Add CI files

## [1.0.4] - 21 August 2025
### Fixed
- Fix wrong Alpine component name
- Move JSON directive into separate template
- Declare used PHP namespaces
- Document latest version of template
- Add missing `strict_types` declaration

## [1.0.3] - 18 August 2025
### Fixed
- Lower requirements to PHP 8.1

## [1.0.2] - 18 August 2025
### Fixed
- A11Y issues
- Fix a11y tab attributes

## [1.0.1] - 06 August 2025
### Fixed
- Lower PHP requirement to PHP 8.2+

## [1.0.0] - 21 July 2025
### Fixed
- Rename PHP namespace from `Yireo_LokiComponents` to `Loki_Components`
- Update deps with `loki/magento2-components`

## [0.1.0] - 10 July 2025
### Refactor
- Rename `Yireo_LokiComponentsDebugger` to `Loki_Debugger`
- Rename `yireo/magento2-loki-components-debugger` to `loki/magento2-debugger`

## [0.0.7] - 13 May 2025
### Fixed
- Update MODULE.json
- Modules should NOT determine the page layout for reusable handles
- Allow PHP 8.4 in CI

## [0.0.6] - 25 April 2025
### Fixed
- Allow to upgrade to LokiComponents 1.0
- Fix CSP issue with new x-json directive
- Move scripts from "before.body.end" to new "loki-scripts" container

## [0.0.5] - 04 April 2025
### Fixed
- Add keywords to composer.json
- Ignore CICD file in PHPCS
- Add new MODULE.json with meta-information

## [0.0.4] - 11 March 2025
### Fixed
- Add module dependencies

## [0.0.3] - 24 February 2025
### Fixed
- Default target definitions were dropped when cache was refreshed from non-frontend

## [0.0.2] - 22 February 2025
### Fixed
-  Add proper README
-  Replace TODO.md with TODO.json
-  Add CHANGELOG and TODO

## [0.0.1] - 21 January 2025
- Initial release
