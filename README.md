# DispatchWire Email Platform

An HTML email development toolset with bigger aspirations.

DispatchWire supports HTML email development workflow. It runs as a PHP website on a localhost URL.

### Features

* Test emails via Mandrill
* Inlining of CSS styles via Preflight
* Basic personalization tag swapping - More to come!
* Reusable code snippets
* Use your desktop editor/IDE of choice

### How It Works

Any templates that reside in the `public_html/templates/email` folder, directly or within 1 level of organizational folders, are available to build & test. You can view that list of templates at your root website URL.

All templates support YAML front matter, for per-template configuration and testing values. YAML front matter resides between the `---` delimeters at the top of each template file, like so:

```
---
variable: value
---
```

Those delimeters are required in every template, even if empty.

When you hit the **Build** link for any of your templates, the following steps are run:

1. The HTML template is run through the [premailer.dialect.ca](http://premailer.dialect.ca/) API to inline your styles.
2. Any tags you've defined in `_tags_field_value` are swapped in your template Preview, which is the version that gets sent as a test email.
3. If `_email_test` is set to `true`, a test email is sent to your specified `_test_addresses` via your [Mandrill](https://mandrillapp.com) API key.
4. The Preview version of the email, inlined and with tag values swapped, is stored in your `preview` directory, and a Live version, with personalization tags intact, is stored in your `live` directory.

This makes it easy to:

1. Refresh your Preview URL locally as you work on your email templates
2. Push your DispatchWire codebase, along with your Preview templates, to a remote URL to share those with your team for review
3. Easily fire off test emails to your inbox or your [Litmus](https://litmus.com) account address
3. And provides a Live email ready to post to your Email Service Provider.

### Configuration

I aim to support default global values for all configurable settings. Presently the only value stored in the global config is your Mandrill API Key.

To set your API key value, copy `dispatchwire/config/sample-general.yaml` to `dispatchwire/config/general.yaml` and enter your API key between the empty single quotes.

### Installation

If you don't have Composer, you can [install it within the root `dispatchwire` directory](https://getcomposer.org/doc/00-intro.md#locally), at the same level as `dispatchwire`, `public_html`, and `composer.json`. Once you've done so, run:

```
php composer.phar install
```

This will install all of DispatchWire's dependencies locally, within the `dispatchwire/app/vendor` directory.

### Usage

Refer to `public_html/templates/email/sample.html` for examples.

### Snippets

Any tags in the format `{{ snippets.snippet-name }}`, where `snippet-name` matches the non-`.html` portion of the name of a file in the `snippets` folder will be replaced by the contents of the corresponding file. Unmatched snippets tags will not be replaced and will be rendered in the live and preview templates.

### To Do

Lots! This is just a starting point with functional code. It needs organization, error handling, and further work on additional features.

#### Planned Features

* [ ] Move everything into DispatchWire classes
* [x] <strike>Hidden files and folders: Templates and template directories hidden from the website list</strike>
* [x] <strike>Snippets: Chunks of code that can be included in any of your templates</strike>
* [ ] Single template test URLs: A single template view with a test button and responsive viewports to load the preview in
* [ ] Environment variables: Per-server/computer values in the global config
* [x] <strike>Last built timestamp on each template</strike>
* [x] <strike>Preview link on each template: In the meantime, just refresh the template's `preview/email` URLs</strike>
* [x] <strike>"Build & Test" links where `_email_test: true`</strike>
* [ ] List `_pin_to_top: true` templates at the top of the list for quick access
* [ ] Error logging for builds