=== Analytics for OpenPanel ===
Contributors: steffenkt
Tags: analytics, tracking, openpanel, privacy, self-hosted
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Privacy-friendly web analytics with OpenPanel – Self-Hosted and Cloud, fully configurable inside WordPress.

== Description ==

**Analytics for OpenPanel** seamlessly integrates [OpenPanel Analytics](https://openpanel.dev) into WordPress – as a standalone plugin, independent of the active theme.

OpenPanel is an open-source alternative to Google Analytics that can be fully self-hosted. This plugin supports both self-hosted instances and the official cloud service and is fully configurable via a dedicated admin settings page.

= Features =

* **Self-Hosted & Cloud** – Enter your API URL or leave empty for the cloud service
* **Local Script Delivery** – `op1.js` is served from the plugin folder (no external CDN request)
* **Flexible Tracking** – Page views, outgoing links and HTML attribute tracking individually configurable
* **Session Replay** – Record mouse movements, scroll behaviour and clicks (optional, with form masking)
* **Privacy** – Do Not Track (DNT), exclude logged-in users, WP_DEBUG guard
* **Multilingual** – German and English included, fully translatable

= Settings =

All options are available under **Settings → OpenPanel**:

| Setting | Default | Description |
|---|---|---|
| Enable OpenPanel | ☐ | Enables/disables the tracking script |
| Client ID | – | The Client ID of your OpenPanel application |
| API URL (Self-Hosted) | – | e.g. `https://openpanel.example.com/api`. Leave empty for cloud. |
| Serve op1.js locally | ✅ | Load script from the plugin folder |
| trackScreenViews | ✅ | Automatically track page views |
| trackOutgoingLinks | ✅ | Track outgoing links |
| trackAttributes | ✅ | Track via `data-track` attributes |
| Enable Session Replay | ☐ | Record mouse movements, scroll behaviour and clicks |
| Mask form fields | ✅ | Prevent plain-text recording of input fields |
| Do not track logged-in users | ✅ | Exclude admins and logged-in users |
| Respect Do Not Track (DNT) | ✅ | Honour the DNT browser signal |
| Disable on WP_DEBUG | ✅ | No tracking on local/staging environments |

== Installation ==

1. Unzip the plugin archive and upload the folder to `/wp-content/plugins/analytics-for-openpanel/`
2. Activate the plugin via **Plugins → Installed Plugins**
3. Enter your Client ID under **Settings → OpenPanel** and enable the plugin

= Requirements =

* WordPress 6.0 or later
* PHP 8.0 or later
* A running OpenPanel instance (Self-Hosted or Cloud) with a created Client ID

= Updating op1.js Files =

The files `assets/js/op1.js` and `assets/js/op1-replay.js` are bundled with the plugin. Replace them manually when new OpenPanel releases are available:

```
Invoke-WebRequest -Uri "https://openpanel.dev/op1.js" -OutFile "assets/js/op1.js"
Invoke-WebRequest -Uri "https://openpanel.dev/op1-replay.js" -OutFile "assets/js/op1-replay.js"
```

== Frequently Asked Questions ==

= Does the plugin support the OpenPanel Cloud service? =

Yes – simply leave the API URL field empty. The plugin will then connect to the official OpenPanel Cloud at `https://openpanel.dev`.

= What happens when WP_DEBUG = true? =

If the option "Disable on WP_DEBUG" is active (default), no tracking script is output. This prevents tracking on local and staging environments.

= Are logged-in WordPress users tracked? =

Not by default. The option "Do not track logged-in users" is enabled and excludes all logged-in users server-side before anything is rendered.

= Where is the script injected in the HTML? =

The init script is output at priority 5 directly in `<head>` (very early, before other scripts). `op1.js` is enqueued with `defer`.

= How do I disable Session Replay for specific elements? =

Use the HTML attributes `data-openpanel-replay-mask` (mask text) and `data-openpanel-replay-block` (replace element) to protect sensitive areas.

= How do I track custom events? =

Via JavaScript: `window.op('track', 'my_event', { foo: 'bar' });`

Via HTML attribute (when trackAttributes is active): `<button data-track="ticket_purchase">Buy</button>`

= How do I add more languages? =

The file `languages/analytics-for-openpanel.pot` serves as the translation template. Create a new `.po` file for the desired locale (e.g. `analytics-for-openpanel-fr_FR.po`), translate it, and compile it to a `.mo` file using `msgfmt`.

== Third-Party Services ==

This plugin integrates with **OpenPanel Analytics**, an open-source web analytics platform.

**When using the Cloud service** (API URL field left empty), visitor data — including page views, custom events, and optionally session recordings — is transmitted to servers operated by OpenPanel at https://openpanel.dev.

**When using a Self-Hosted instance**, all data is transmitted exclusively to your own server and never reaches openpanel.dev.

* OpenPanel website: https://openpanel.dev
* OpenPanel source code (MIT licence): https://github.com/openpanel-dev/openpanel
* OpenPanel privacy information: https://openpanel.dev/privacy
* Plugin repository: https://github.com/Steffenkt/Analytics-for-OpenPanel-WordPress

= JavaScript Source Files =

The files `assets/js/op1.js` and `assets/js/op1-replay.js` are local copies of the official OpenPanel tracking scripts in minified form. The non-minified source code is available at:

https://github.com/openpanel-dev/openpanel

== Changelog ==

= 1.0.0 =
* Initial release
* Admin settings page under Settings → OpenPanel
* Support for Self-Hosted and Cloud instances
* Local delivery of op1.js and op1-replay.js
* Session Replay with maskAllInputs
* DNT, WP_DEBUG guard, exclude logged-in users
* Multilingual: German (source language) and English (en_US)

== Upgrade Notice ==

= 1.0.0 =
Initial release – no upgrade notes.
