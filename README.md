# KD Tracking Connector for OpenPanel – WordPress Plugin

A WordPress plugin for privacy-friendly web analytics integration with [OpenPanel](https://openpanel.dev) – as a standalone plugin, independent of the active theme.

---

## Overview

[OpenPanel](https://openpanel.dev) is a privacy-friendly, open-source alternative to Google Analytics. This plugin integrates OpenPanel via a dedicated admin settings page and supports both self-hosted instances and the official cloud service.

---

## Requirements

- WordPress 6.0 or later
- PHP 8.0 or later
- Access to the WordPress admin area (Settings → OpenPanel)
- A running OpenPanel instance (Self-Hosted or Cloud) with a created **Client ID**

---

## Installation

1. Upload the plugin folder to `/wp-content/plugins/kd-tracking-connector-openpanel/`
2. Activate the plugin via **Plugins → Installed Plugins**
3. Configure under **Settings → OpenPanel**

---

## Configuration

**Path:** WordPress Admin → **Settings → OpenPanel**

| Setting | Default | Description |
|---|---|---|
| **Enable OpenPanel** | ☐ | Enables/disables the tracking script on the frontend |
| **Client ID** | – | The Client ID of your OpenPanel application |
| **API URL (Self-Hosted)** | – | Only required for self-hosting, e.g. `https://openpanel.example.com/api`. Leave empty for the official cloud. |
| **Serve op1.js locally** | ✅ | Load script from the plugin folder (no external request to openpanel.dev) |
| **trackScreenViews** | ✅ | Automatically track page views |
| **trackOutgoingLinks** | ✅ | Track clicks on outgoing external links |
| **trackAttributes** | ✅ | Track via `data-track` attributes on HTML elements |
| **Enable Session Replay** | ☐ | Record mouse movements, scroll behaviour and clicks |
| **Mask form fields** | ✅ | Prevent plain-text recording of input fields in replay |
| **Do not track logged-in users** | ✅ | Exclude admins and logged-in users server-side |
| **Respect Do Not Track (DNT)** | ✅ | Honour the DNT browser header |
| **Disable on WP_DEBUG** | ✅ | No tracking on local/staging environments when `WP_DEBUG` is active |

---

## Generated Script Output

```html
<script>
  window.op=window.op||function(){...}();
  window.op('init', {
    "clientId": "YOUR-CLIENT-ID",
    "trackScreenViews": true,
    "trackOutgoingLinks": true,
    "trackAttributes": true,
    "apiUrl": "https://openpanel.example.com/api",
    "sessionReplay": { "enabled": true, "maskAllInputs": true }
  });
</script>
<!-- local: -->
<script src="/wp-content/plugins/kd-tracking-connector-openpanel/assets/js/op1.js" defer></script>
<!-- CDN: -->
<script src="https://openpanel.dev/op1.js" defer async></script>
```

---

## Updating Local Script Files

The files `assets/js/op1.js` and `assets/js/op1-replay.js` are bundled with the plugin and are local copies of the official OpenPanel tracking scripts.
They can be updated manually when new OpenPanel releases are published:

```powershell
# PowerShell – run from the plugin directory
Invoke-WebRequest -Uri "https://openpanel.dev/op1.js"        -OutFile "assets/js/op1.js"
Invoke-WebRequest -Uri "https://openpanel.dev/op1-replay.js" -OutFile "assets/js/op1-replay.js"
```

or with cURL:

```bash
curl -o assets/js/op1.js        https://openpanel.dev/op1.js
curl -o assets/js/op1-replay.js https://openpanel.dev/op1-replay.js
```

---

## Technical Details

### Files

| File | Purpose |
|---|---|
| `kd-tracking-connector-openpanel.php` | Main plugin file: admin page, settings, script output |
| `uninstall.php` | Removes all plugin options on uninstall |
| `assets/js/op1.js` | Local copy of the OpenPanel tracking script |
| `assets/js/op1-replay.js` | Local copy of the session replay module (dynamically loaded by op1.js when replay is enabled) |
| `languages/kd-tracking-connector-openpanel.pot` | Translation template |
| `languages/kd-tracking-connector-openpanel-en_US.po` | English translation (source language: German) |
| `languages/kd-tracking-connector-openpanel-en_US.mo` | Compiled English translation |

### Implementation Details

- The init script is injected at priority `5` in `wp_head` (very early, before other scripts).
- `op1.js` is enqueued via `wp_enqueue_script` with `defer`.
- **Local delivery** appends `OPSH_VERSION` as a cache-buster to the URL.
- **CDN delivery** uses no version parameter (CDN always serves the latest version).
- All settings are stored as individual WordPress options (`wp_options`) with the prefix `opsh_`.
- If the plugin is disabled or the Client ID is empty, **no script is output**.
- If `WP_DEBUG` is active and the corresponding option is enabled, the script is suppressed entirely server-side.
- Logged-in users are excluded server-side via `is_user_logged_in()` before anything is rendered.

### Session Replay

- Only active when "Enable Session Replay" is checked – otherwise no `sessionReplay` block in the init script.
- `maskAllInputs: true` (default) prevents plain-text recording of form fields.
- For sensitive content, use `data-openpanel-replay-mask` (mask text) and `data-openpanel-replay-block` (replace element) as HTML attributes.

### Do Not Track

- The `filter` callback is a JavaScript function and cannot be output via `wp_json_encode()`.
- It is therefore assembled directly in the inline script using `Object.assign()`.

### Security

- All option values are sanitised on save (`sanitize_text_field`, `esc_url_raw`, bool cast).
- `apiUrl` is escaped with `esc_url()` on output.
- The generated JSON for `window.op('init', ...)` is produced via `wp_json_encode()` (XSS-safe).
- The uninstall routine cleanly removes all `opsh_*` options from the database.

---

## Multilingual Support

The plugin is fully translatable. Included languages:

| Language | Locale | Status |
|---|---|---|
| German | *(source language)* | ✅ |
| English | `en_US` | ✅ |

New translations can be created from the template `languages/kd-tracking-connector-openpanel.pot`:

```bash
# Create a new .po file based on the template, translate it,
# then compile to a .mo file:
msgfmt languages/kd-tracking-connector-openpanel-fr_FR.po -o languages/kd-tracking-connector-openpanel-fr_FR.mo
```

---

## Trigger Tracking Events Manually

### Via JavaScript

```js
window.op('track', 'my_event', { foo: 'bar' });
```

### Via HTML Attribute (when `trackAttributes` is active)

```html
<button type="button" data-track="ticket_purchase" data-product="Home-Game-Ticket">
  Buy Ticket
</button>
```

---

## Identify Users

```js
window.op('identify', {
  profileId: '123',       // required
  firstName: 'John',
  lastName: 'Doe',
  email: 'john@example.com',
  properties: {
    plan: 'premium',
  },
});
```

---

## Set Global Properties

Automatically appended to every event:

```js
window.op('setGlobalProperties', {
  plugin_version: '1.0.0',
  environment: 'production',
});
```

---

## CI/CD & Release Process

### GitHub Actions Workflows

| Workflow | Trigger | Aufgabe |
|---|---|---|
| `build-zip.yml` | Push auf `main` | Erstellt GitHub Release + Plugin-ZIP |
| `deploy-wporg.yml` | Push auf `main` + manuell | Deployed auf WordPress.org SVN |

Beide Workflows laufen bei jedem Push auf `main` parallel.

### WordPress.org SVN-Struktur

Das WordPress.org-Plugin-Verzeichnis nutzt SVN mit drei festen Ordnern:

```
https://plugins.svn.wordpress.org/kd-tracking-connector-openpanel/
├── trunk/          ← aktueller Stand (= Inhalt dieses Repos, ohne export-ignore-Dateien)
├── tags/
│   ├── 1.0.0/      ← automatisch via svn cp trunk → tags/VERSION angelegt
│   └── 1.0.2/
└── assets/         ← Icons, Banner, Screenshots (aus .wordpress-org/)
```

**Kein Plugin-Slug-Unterordner nötig** – die Dateien liegen direkt in `trunk/`, nicht in
`trunk/kd-tracking-connector-openpanel/`. Der GitHub Action `10up/action-wordpress-plugin-deploy`
handhabt die gesamte SVN-Struktur automatisch.

**Versionstags** werden durch die Action via `svn cp trunk tags/$VERSION` angelegt –
nie manuell anlegen. Tags sind idempotent: ein bereits existierender Tag wird nicht überschrieben.

### Dateien und ihr SVN-Ziel

| Quelle (GitHub) | SVN-Ziel | Gesteuert durch |
|---|---|---|
| Alle Dateien im Root (außer excluded) | `trunk/` | `.gitattributes` |
| `.wordpress-org/*.png` | `assets/` | `ASSETS_DIR` in Workflow |
| `README.md`, `.github/`, `.gitattributes`, `.wordpress-org/` | *excluded* | `.gitattributes export-ignore` |

### Neue Version veröffentlichen

1. Version in `kd-tracking-connector-openpanel.php` und `readme.txt` (`Stable tag:`) erhöhen
2. Changelog in `readme.txt` ergänzen
3. Auf `main` pushen → beide Workflows starten automatisch:
   - GitHub Release wird erstellt/aktualisiert
   - SVN `trunk` wird aktualisiert, neuer Tag `tags/X.Y.Z` wird angelegt

### Required Secrets

Einmalig unter **Repository → Settings → Secrets and variables → Actions** eintragen:

| Secret | Wert |
|---|---|
| `SVN_USERNAME` | WordPress.org-Benutzername (Groß-/Kleinschreibung beachten) |
| `SVN_PASSWORD` | SVN-Passwort von [profiles.wordpress.org](https://profiles.wordpress.org/me/profile/edit/group/3/) |

### Dry Run

Vor dem ersten echten Deploy empfiehlt sich ein Dry Run:
**Actions → Deploy to WordPress.org → Run workflow → Dry Run: ✅**

Dabei wird der gesamte SVN-Ablauf simuliert, ohne dass etwas committed wird.

---

## References

- [OpenPanel Documentation – Script Tag](https://openpanel.dev/docs/sdks/script)
- [OpenPanel Self-Hosting Guide](https://openpanel.dev/docs/get-started/install-openpanel)
- [Session Replay](https://openpanel.dev/docs/session-replay)
- [GitHub Repository](https://github.com/Steffenkt/KD-Tracking-Connector-for-OpenPanel-WordPress)
- [WordPress.org Plugin Directory](https://wordpress.org/plugins/kd-tracking-connector-openpanel/)
- [WordPress SVN Guide](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
- [10up/action-wordpress-plugin-deploy](https://github.com/10up/action-wordpress-plugin-deploy)
