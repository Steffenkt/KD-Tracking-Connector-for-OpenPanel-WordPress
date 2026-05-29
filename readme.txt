=== KD Tracking Connector for OpenPanel ===
Contributors: steffenka
Tags: analytics, statistics, privacy, gdpr, openpanel
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.0.3
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Privacy-first web analytics for WordPress via OpenPanel – the open-source Google Analytics alternative. Self-hosted or cloud, GDPR-friendly.

== Description ==

**KD Tracking Connector for OpenPanel** connects your WordPress site to [OpenPanel Analytics](https://openpanel.dev) – a powerful, open-source alternative to Google Analytics that you can fully self-host.

No data sent to Google. No surprise tracking. Full control over where your visitor data lives.

This plugin works completely **independent of your active theme** and is configured entirely via **Settings → OpenPanel** in the WordPress admin.

= Why OpenPanel? =

* **Open source** – MIT-licensed, fully auditable source code
* **Self-hostable** – Your analytics data stays on your own server
* **Cloud option** – Use the official OpenPanel Cloud with zero setup
* **Cookie-friendly** – Basic page-view tracking works without cookies
* **Google Analytics alternative** – Real-time stats, custom events, session replay

= Plugin Features =

* **Self-Hosted & Cloud** – Enter your API URL for self-hosted, or leave empty for the official cloud
* **Local Script Delivery** – `op1.js` is served from the plugin folder, no external CDN request on page load
* **Flexible Tracking** – Page views, outgoing links and `data-track` HTML attribute tracking individually configurable
* **Session Replay** – Record mouse movements, scroll behaviour and clicks (optional, with input field masking)
* **Privacy Controls** – Honour Do Not Track (DNT), exclude logged-in users, disable tracking when WP_DEBUG is active
* **Multilingual** – Ships with German and English, fully translatable via `.pot` template

= Settings =

All options are available under **Settings → OpenPanel**:

* **Enable OpenPanel** – Enable or disable the tracking script globally. (Default: off)
* **Client ID** – The Client ID of your OpenPanel application.
* **API URL (Self-Hosted)** – Your self-hosted API endpoint, e.g. `https://analytics.example.com/api`. Leave empty to use the official cloud.
* **Serve op1.js locally** – Load the tracking script from the plugin folder instead of an external CDN. (Default: on)
* **Track page views** – Automatically track every page view. (Default: on)
* **Track outgoing links** – Track clicks on external links. (Default: on)
* **Track data attributes** – Track events via `data-track` HTML attributes. (Default: on)
* **Enable Session Replay** – Record mouse movements, scroll behaviour and clicks. (Default: off)
* **Mask form fields** – Prevent plain-text capture of input fields in session replays. (Default: on)
* **Skip logged-in users** – Exclude admins and logged-in users from tracking. (Default: on)
* **Respect Do Not Track** – Honour the DNT browser signal. (Default: on)
* **Disable on WP_DEBUG** – No tracking on local or staging environments when `WP_DEBUG` is active. (Default: on)

== Installation ==

1. Install the plugin via **Plugins → Add New Plugin** and search for "KD Tracking Connector for OpenPanel" – or upload the ZIP manually.
2. Activate the plugin via **Plugins → Installed Plugins**.
3. Go to **Settings → OpenPanel**.
4. Enter your **Client ID** (and optionally your self-hosted API URL).
5. Check **Enable OpenPanel** and save.

= Requirements =

* WordPress 6.0 or later
* PHP 8.0 or later
* An OpenPanel account – [cloud](https://openpanel.dev) or self-hosted

= Keeping op1.js Up to Date =

`op1.js` and `op1-replay.js` are bundled with the plugin. To update them manually when a new OpenPanel version is released:

**Linux / macOS:**

    curl -o assets/js/op1.js https://openpanel.dev/op1.js
    curl -o assets/js/op1-replay.js https://openpanel.dev/op1-replay.js

**Windows (PowerShell):**

    Invoke-WebRequest -Uri "https://openpanel.dev/op1.js" -OutFile "assets/js/op1.js"
    Invoke-WebRequest -Uri "https://openpanel.dev/op1-replay.js" -OutFile "assets/js/op1-replay.js"

== Frequently Asked Questions ==

= Do I need an OpenPanel account? =

Yes. You need a Client ID from either the [OpenPanel Cloud](https://openpanel.dev) (free to sign up) or your own self-hosted OpenPanel instance.

= Does the plugin support the OpenPanel Cloud service? =

Yes – simply leave the API URL field empty. The plugin will connect to `https://openpanel.dev` automatically.

= Is this plugin GDPR-compliant? =

OpenPanel is designed with privacy in mind. Basic page-view tracking works without cookies. You can additionally enable Do Not Track support and exclude logged-in users. Consult your legal advisor for your specific situation and jurisdiction.

= What happens when WP_DEBUG = true? =

When "Disable on WP_DEBUG" is active (default), no tracking script is rendered. This prevents accidental data collection on development or staging environments.

= Are logged-in WordPress users tracked? =

Not by default. The "Skip logged-in users" option is enabled and excludes all authenticated users before the script is rendered.

= Where is the tracking script injected? =

The initialisation script is inserted at priority 5 in `<head>` (before most other scripts). `op1.js` is enqueued with `defer` so it does not block page rendering.

= How do I track custom events? =

**Via JavaScript:**

    window.op('track', 'my_event', { foo: 'bar' });

**Via HTML attribute** (when "Track data attributes" is active):

    <button data-track="ticket_purchase">Buy ticket</button>

= How do I protect specific elements from Session Replay? =

Use `data-openpanel-replay-mask` to mask text content or `data-openpanel-replay-block` to replace an element entirely in the recording.

= How do I translate the plugin? =

Use `languages/kd-tracking-connector-openpanel.pot` as the translation template. Create a `.po` file for your locale (e.g. `kd-tracking-connector-openpanel-fr_FR.po`), translate it, and compile it to a `.mo` file with `msgfmt` or Poedit.

== Screenshots ==

1. Settings page – General settings with Client ID and API URL
2. Settings page – Tracking options and Session Replay configuration
3. Settings page – Privacy controls (DNT, logged-in users, WP_DEBUG)

== Third-Party Services ==

This plugin integrates with **OpenPanel Analytics** ([openpanel.dev](https://openpanel.dev)), an open-source web analytics platform.

**Cloud service** (API URL left empty): Visitor data including page views, custom events and optional session recordings is transmitted to servers operated by OpenPanel at `https://openpanel.dev`.

**Self-hosted**: All data goes exclusively to your own server. Nothing is sent to `openpanel.dev`.

* [OpenPanel website](https://openpanel.dev)
* [Source code on GitHub (MIT licence)](https://github.com/openpanel-dev/openpanel)
* [Privacy policy](https://openpanel.dev/privacy)
* [Plugin repository](https://github.com/Steffenkt/KD-Tracking-Connector-for-OpenPanel-WordPress)

= Bundled JavaScript Files =

`assets/js/op1.js` and `assets/js/op1-replay.js` are local copies of the official OpenPanel tracking scripts in minified form. The non-minified source code is available at [github.com/openpanel-dev/openpanel](https://github.com/openpanel-dev/openpanel).

== Changelog ==

= 1.0.3 =
* Code improvements and general housekeeping
* Added GitHub Actions workflows for automated validation and WordPress.org SVN deployment
* Added plugin icon for WordPress.org plugin directory

= 1.0.2 =
* Plugin display name set to "KD Tracking Connector for OpenPanel", slug `kd-tracking-connector-openpanel`
* Replaced inline script output with `wp_add_inline_script()` via `wp_enqueue_scripts`

= 1.0.0 =
* Initial release
* Admin settings page under Settings → OpenPanel
* Support for self-hosted and cloud instances
* Local delivery of `op1.js` and `op1-replay.js`
* Session Replay with input field masking
* DNT support, WP_DEBUG guard, exclude logged-in users
* Multilingual: German (source language) and English (en_US)

== Upgrade Notice ==

= 1.0.3 =
Maintenance update with code improvements. No action required after updating.
