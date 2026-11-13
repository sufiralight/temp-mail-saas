# Temp Mail

Temp Mail is a Laravel 13 workspace application with a browser-based MySQL setup flow, account registration, sign-in, password recovery, and a dashboard. The frontend uses Blade, Tailwind CSS 4, and JavaScript bundled by Vite.

## Authentication design

The approved design is **Pure Zinc Monochrome**. Sign-in, registration, and password recovery share a single-column form layout capped at 400 px. The minimal header uses a 32 px Lucide mail mark and semibold Geist Sans. Geist Mono is available for technical values and commands. Both fonts are bundled locally.

| Role | Light | Dark |
| --- | --- | --- |
| Page | Zinc 50 | Zinc 950 |
| Header and elevated surfaces | White | Zinc 900 |
| Primary text and button fill | Zinc 950 | Zinc 50 |
| Surface borders | Zinc 200 | Zinc 800 |
| Brand mark | Zinc 950 fill and white icon | Zinc 900 fill, Zinc 800 outline, and Zinc 50 icon |

Input borders and focus rings use stronger Zinc tones where needed for visibility. Feedback uses explicit messages and icons, with field-level validation associated with its inputs. Shared styles cover buttons, inputs, links, surfaces, menus, hover, focus, disabled, and loading states.

## Appearance menu

The header shows a single icon for the selected preference. The dropdown lists Light (sun), Dark (moon), and System (monitor), with a Zinc highlight and checkmark on the selected option. Use the arrow keys to move, Enter or Space to select, and Escape to close and return focus to the trigger. Tab or an outside click dismisses the menu.

Explicit Light and Dark preferences are saved under `temp-mail-theme` in localStorage. Selecting System clears that override and follows live changes to `prefers-color-scheme`. The shared shell resolves the theme before loading the Vite styles.

## Frontend entry points

| File | Responsibility |
| --- | --- |
| [app.css](resources/css/app.css) | Semantic tokens, typography, and shared control states |
| [shell.blade.php](resources/views/components/shell.blade.php) | Header, pre-paint theme initialization, and layout |
| [brand.blade.php](resources/views/components/brand.blade.php) | Shared Temp Mail brand |
| [theme-control.blade.php](resources/views/components/theme-control.blade.php) | Appearance trigger and dropdown |
| [account-shell.blade.php](resources/views/components/account-shell.blade.php) | Shared account layout |
| [app.js](resources/js/app.js) | Theme, password visibility, and submission feedback |

## Build and verify

Run `npm install` and `npm run build` on a development machine or build runner. For shared hosting, deploy the generated `public/build` directory with the application and serve the Laravel `public` directory as the document root. Node.js and npm are build-time tools. The application serves the compiled assets through Laravel.

Run `vendor/bin/phpunit` for application tests. For changes to the account UI, inspect Login, Register, and Forgot Password in Chrome or another browser at desktop, tablet, and mobile widths. Check Light, Dark, and System, including live System changes and persistence across navigation. Verify keyboard focus, dropdown dismissal, password visibility, form validation, and submission feedback.

See [AGENTS.md](AGENTS.md#temp-mail-frontend-design-rules) for project-specific implementation rules.
