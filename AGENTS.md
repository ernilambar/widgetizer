# Widgetizer — Agent Guide

## Overview

`widgetizer` is a **PHP library** (not a WordPress plugin) that builds admin dashboard widgets from a field-schema array. Requires PHP 7.4+. Frontend JS/CSS is compiled with Vite + PostCSS and committed under `assets/`.

## Setup

```bash
composer install   # PHP dev deps (PHPCS, parallel-lint)
pnpm install       # JS/CSS deps (Node >= 22, pnpm >= 12)
```

## Commands

```
composer lint     # parallel-lint syntax check + PHPCS
composer format   # phpcbf auto-fix
pnpm build        # compile resources/ -> assets/
pnpm dev          # watch mode
pnpm format       # Prettier (uses @wordpress/prettier-config)
```

## Architecture

- `src/Widgetizer.php` — abstract base class. Extend it, implement `widget()`, and pass `widget_id`, `widget_name`, `fields`, `extra_args` to the constructor.
- `register()` hooks `wp_dashboard_setup` → `wp_add_dashboard_widget()`. When fields exist, the settings callback is registered too.
- Settings persist in a single option keyed by `widget_id`; `get_setting()` falls back to the field `default`.
- `render_form_field()` dispatches to `callback_{type}()` methods. Add a field type by adding a `callback_*` method **and** its sanitization in `update_form()`.
- Field types: `text`, `email`, `url`, `password`, `number`, `textarea`, `select`, `radio`, `checkbox`, `toggle`, `multicheckbox`, `buttonset`, `sortable`.
- `resources/` holds source JS/CSS; `assets/` holds the Vite build output. Never edit `assets/` by hand.

## Conventions

- **Formatting**: Prettier uses `@wordpress/prettier-config` (declared via the `prettier` key in `package.json`) — do not add a custom Prettier config file (`.prettierrc*`).
- **Package manager**: pnpm only — see `packageManager` and `engines` in `package.json`.
- Follow WordPress coding standards; escape all output (`esc_attr`, `esc_html`, `esc_url`); return `WP_Error` for error conditions.
- Stays PHP 7.4 compatible (Composer `platform.php` is pinned to `7.4`).

## Quality gate

Run `composer lint && pnpm build && pnpm format` before declaring a task complete.
