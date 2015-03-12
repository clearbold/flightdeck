# FlightDeck Email Platform

An HTML email development toolset with bigger aspirations from [Clearbold](http://clearbold.com).

FlightDeck supports HTML email development workflow. It runs as a PHP website on a localhost URL.

### Features

* Test emails via Mandrill
* Inlining of CSS styles via Premailer
* Basic personalization tag swapping - More to come!
* Reusable code snippets
* Use your desktop editor/IDE of choice

### How It Works

Any templates that reside in the `public_html/templates/email` folder, directly or within 1 level of organizational folders, are available to build & test. You can view that list of templates at your website's `/console` URL.

All templates support YAML front matter, for per-template configuration and testing values. YAML front matter resides between the `---` delimeters at the top of each template file, like so:

```
---
variable: value
---
```

Those delimeters are required in every template, even if empty.

![Screenshot of FlightDeck](http://clearbold.com/ui/img/flightdeck.png)

When you hit the **Build** link for any of your templates, the following steps are run:

1. Snippets are swapped in to your live & preview templates.
2. The HTML template is run through the [premailer.dialect.ca](http://premailer.dialect.ca/) API to inline your styles.
3. Any tags you've defined in `_tags_field_value` are swapped in your template Preview, which is the version that gets sent as a test email.
4. If `_email_test` is set to `true`, a test email is sent to your specified `_test_addresses` via your [Mandrill](https://mandrillapp.com) API key.
5. The Preview version of the email, inlined and with tag values swapped, is stored in your `preview` directory, and a Live version, with personalization tags intact, is stored in your `live` directory.

This makes it easy to:

1. Refresh your Preview URL locally as you work on your email templates
2. Push your FlightDeck codebase, along with your Preview templates, to a remote URL to share those with your team for review
3. Easily fire off test emails to your inbox or your [Litmus](https://litmus.com) account address
3. And provides a Live email ready to post to your Email Service Provider.

### Configuration

I aim to support default global values for all configurable settings. Presently the only value stored in the global config is your Mandrill API Key.

To set your API key value, copy `flightdeck/config/sample-general.yaml` to `flightdeck/config/general.yaml` and enter your API key between the empty single quotes.

### Installation

If you don't have Composer, you can [install it within the root project directory](https://getcomposer.org/doc/00-intro.md#locally), at the same level as `flightdeck`, `public_html`, and `composer.json`. Once you've done so, run:

```
php composer.phar install
```

This will install all of FlightDeck's dependencies locally, within the `flightdeck/app/vendor` directory.

### Usage

Refer to `public_html/templates/email/sample.html` for examples.

### Image Assets

If you include image files in a folder that matches your template's name, that folder will be copied to your `preview` and `live` folders each time you build your template. How you name your matching folder is important.

* `templates/email/sample.html` matches `templates/email/_sample`
* `templates/client-1/template-1.html` matches `templates/email/_template-1`

This will allow to reference image files in your template using relative paths, such as `src="_sample/image.png"` and rely on those paths when you deploy your email templates.

### Snippets

Any tags in the format `{{ snippets.snippet-name }}`, where `snippet-name` matches the non-`.html` portion of the name of a file in the `snippets` folder will be replaced by the contents of the corresponding file. Unmatched snippets tags will not be replaced and will be rendered in the live and preview templates.

### To Do

Lots! This is just a starting point with functional code. It needs organization, error handling, and further work on additional features.

#### Planned Features

* [x] <strike>Move everything into FlightDeck classes</strike>
* [x] <strike>Hidden files and folders: Templates and template directories hidden from the website list</strike>
* [x] <strike>Snippets: Chunks of code that can be included in any of your templates</strike>
* [ ] Single template test URLs: A single template view with a test button and responsive viewports to load the preview in
* [ ] Environment variables: Per-server/computer values in the global config
* [x] <strike>Last built timestamp on each template</strike>
* [x] <strike>Preview link on each template: In the meantime, just refresh the template's `preview/email` URLs</strike>
* [x] <strike>"Build & Test" links where `_email_test: true`</strike>
* [ ] List `_pin_to_top: true` templates at the top of the list for quick access
* [ ] Error logging for builds
* [ ] Install templates in ESPs via API
* [ ] Handle small volume email blasts
* [ ] Inliner and TestEmail as interfaces so that other providers can be swapped in
* [x] Support per-template image/asset directories