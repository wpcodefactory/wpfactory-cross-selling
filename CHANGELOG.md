# Changelog

**Types of changes**
* `Added` for new features.
* `Changed` for changes in existing functionality.
* `Deprecated` for soon-to-be removed features.
* `Removed` for now removed features.
* `Fixed` for any bug fixes.
* `Security` in case of vulnerabilities

## [1.2.3] - 2026-11-26
### Fixed
* My Plugins: a plugin whose free version is active next to an installed but inactive Pro showed as "Inactive". It now shows "Free" with an "Activate Pro" button.
* My Plugins: after Activate, the chip switches to "Free" or "Pro" right away instead of after a reload.
* My Plugins: after Activate, "Settings" only links to the plugin's own settings page, never to the Plugins screen.
* My Plugins top bar: labels no longer show the sidebar count bubbles.
### Changed
* My Plugins top bar shows the WPFactory logo.

## [1.2.2] - 2026-11-25
### Changed
* One stylesheet and one script for the whole library, `assets/css/admin(.min).css` and `assets/js/admin(.min).js`, built by webpack from `src/scss` and `src/js`.
* My Plugins classes, ids, tokens, AJAX actions and caches use the `wpfcs-` prefix. The "seen" flag is now `wpfcs_my_plugins_seen` and still reads the 1.2.0 key.
* My Plugins page HTML moved to `templates/my-plugins/`.
### Removed
* `assets/css/my-plugins-ui-kit.css`, `assets/css/my-plugins.css` and `assets/js/my-plugins.js`.

## [1.2.1] - 2026-11-25
### Changed
* "My Plugins" in the WPFactory sidebar: pinned last under a divider, tool blue text that turns white on hover and when open, and a grid icon.
* The new-plugins dot shows on the "My Plugins" top-bar tab only, not in the sidebar.

## [1.2.0] - 2026-11-25
### Added
* "My Plugins" page in the WPFactory menu: the WPFactory plugins on the site, 3 picks based on the store's settings, and every WPFactory plugin with search and tabs. Free plugins install and activate from the page.
* The newest copy of the page wins when several plugins ship the library.
### Changed
* The old Recommendations page now redirects to My Plugins.
### Removed
* Remote banners (`class-banners.php`) and their calls to wpfactory.com.

## [1.1.8] - 2026-11-23
### Fixed
* Dependabot alerts.

## [1.1.7] - 2026-10-06
### Fixed
* Dependabot alerts.

## [1.1.6] - 2026-09-17
### Fixed
* Dependabot alerts.

## [1.1.5] - 2026-09-03
### Fixed
* Dependabot alerts.

## [1.1.4] - 2026-08-19
### Fixed
* Dependabot alerts.

## [1.1.3] - 2026-07-08
### Fixed
* Dependabot alerts.

## [1.1.2] - 2026-05-29
### Fixed
* Dependabot alerts.

## [1.1.1] - 2026-05-20
### Fixed
* Dependabot alerts.

## [1.1.0] - 2026-04-14
### Fixed
* Dependabot alerts.

## [1.0.9] - 2025-12-22
### Fixed
* Minified CSS.

## [1.0.8] - 2025-12-18
### Fixed
* Dashboard banner position by adding it inside a wrap class.

## [1.0.7] - 2025-12-11
### Added
* Banners integrating with Advanced Ads plugin.

## [1.0.6] - 2025-08-19
### Changed
* Settings and Recommendations box width.

## [1.0.5] - 2025-08-18
### Fixed
* Errors related to missing parameters.

## [1.0.4] - 2025-08-18
### Added
* Recommendations Box.

## [1.0.3] - 2024-10-31
### Changed
* Some plugin names.

## [1.0.2] - 2024-10-14
### Fixed
* Capability by managing it later.

## [1.0.1] - 2024-10-14
### Fixed
* Capability to `manage_woocommerce` if woocommerce is enabled, or else `manage_options`.

### Removed
* SCSS files from archive files.

## [1.0.0] - 2024-09-27
### Added
* Initial release.