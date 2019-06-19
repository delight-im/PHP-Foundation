# PHP-Foundation

**Writing modern PHP applications efficiently**

Build *better* applications *faster*, while still using “plain old PHP” only.

There are no DSLs or pseudo-languages that you need to learn (except [Twig](views/welcome.html)!), nor any “magical” command-line utilities.

## Requirements

 * Apache HTTP Server 2.2.0+
   * `mod_rewrite`
   * `mod_headers`
 * PHP 5.6.0+
   * Multibyte String extension (`mbstring`)
   * PDO (PHP Data Objects) extension (`pdo`)
   * OpenSSL extension (`openssl`)
   * GMP (GNU Multiple Precision) extension (`gmp`)
 * MySQL 5.5.3+ **or** MariaDB 5.5.23+ **or** PostgreSQL 9.5.10+ **or** SQLite 3.14.1+

## Installation

 1. Copy all files from this repository to your development path or web server.

    **Note:** This framework does currently work in the web root (at `/` under your domain) only.

 1. Copy the configuration template `config/.env.example` to `config/.env`. This new file is where your private configuration will be stored. Fill in the correct settings for your environment. The most important setting is `APP_PUBLIC_URL`, which is required for your application to work correctly in the first place.
 1. If you want to use the built-in authentication component, create the database tables for [MariaDB](https://github.com/delight-im/PHP-Auth/blob/master/Database/MySQL.sql), [MySQL](https://github.com/delight-im/PHP-Auth/blob/master/Database/MySQL.sql), [PostgreSQL](https://github.com/delight-im/PHP-Auth/blob/master/Database/PostgreSQL.sql) or [SQLite](https://github.com/delight-im/PHP-Auth/blob/master/Database/SQLite.sql) in the database that you specified in `config/.env`.
 1. Get Composer [[?]](https://github.com/delight-im/Knowledge/blob/master/Composer%20(PHP).md) and run

    ```
    # composer check-platform-reqs
    $ composer install
    ```

    in this directory (containing the `README.md`) to set up the framework and its dependencies.
 1. Make sure that the content of the `storage/` directory is writable by the web server, e.g. using

    ```
    $ sudo chown -R www-data:www-data /var/www/html/storage/*
    ```

    on Linux if this directory (containing the `README.md`) is in `/var/www/html/` on the server.
 1. Ensure that the configuration in the `config/` directory – which usually contains sensitive information such as database credentials, SMTP passwords or API tokens – can be read only by the web server. For example, execute

    ```
    $ sudo chown -R www-data:www-data /var/www/html/config
    $ sudo chmod 0555 /var/www/html/config
    $ sudo chmod 0400 /var/www/html/config/.env
    ```

    on Linux if this directory (containing the `README.md`) is in `/var/www/html/` on the server.
 1. If you want to enable automatic backups in compressed and encrypted form, please refer to the [“Backups” section](#backups) further below.

## Usage

### Application structure

 * `app/`: This is the most important directory: It’s where you’ll write all your PHP code. It’s entirely up to you how you structure your application in this directory. Create as many files and subdirectories here as you wish. The `index.php` file is the main entry point to your application. The complete folder is exclusively for you.
 * `backups/`: If you make use of the convenient `backup.sh` script, this is where the compressed and encrypted backups will be stored. See the [“Backups” section](#backups) further down.
 * `config/`: Store your local configuration, which should include all confidential information, keys and secrets, here. Put everything in `config/.env` while keeping an up-to-date copy of that file in `config/.env.example` which should have exactly the same keys but all the confidential values removed. The first file will be your private configuration, the second file can be checked in to version control for others to see what configuration keys they need. Always remember to securely back up the private configuration file (`config/.env`) somewhere outside of your VCS. Do *not* store it in version control.
 * `public/`: This is where you can store your static assets, such as CSS files, JavaScript files, images, your `robots.txt` and so on. The files will be available at the root URL of your application, i.e. `public/favicon.ico` can simply be accessed at `favicon.ico`. The complete folder is exclusively for you.
 * `storage/app/`: Storage that you can use in your app to store files temporarily or permanently. This space is private to your application. Feel free to add any number of subfolders and files here.
 * `storage/framework/`: Storage used by the framework, e.g. for caching. Do *not* add, modify or delete anything here.
 * `vendor/`: This directory is where Composer will install all dependencies. Do *not* add, modify or delete anything here.
 * `views/`: If you use templates for HTML, XML, CSV or LaTeX (or anything else) that should be rendered by the built-in template engine, this is where you should store them.
 * `.htaccess`: Rules and optimizations for Apache HTTP Server. You may add your own rules here, but only in the `CUSTOM` section at the bottom.
 * `backup.sh`: Script that can create compressed and encrypted backups in the `backups/` directory for you. See the [“Backups” section](#backups) further below.
 * `composer.json`: The dependencies of your application. This framework must *always* be included in the `require` section as `delight-im/framework`, so please don’t remove that entry. But otherwise, feel free to add or modify any of your own entries.
 * `index.php`: This is the main controller of this framework. It sets everything up correctly and passes on control to your application code. Do *not* change or delete this.

### Routing

Your application will be available at the URL where this root folder is accessible on your web server.

**Note:** You should have added this URL to the configuration in `config/.env` as `APP_PUBLIC_URL` already. This is required for your application to work correctly.

Sometimes a single application is expected to respond to *multiple* hostnames with *different* content. Examples include the use of language-specific subdomains (such as `fr.example.com` and `id.example.com`), country-specific TLDs (such as `example.com` and `example.org`), separate storefronts (such as `seller-a.example.com` and `seller-b.example.com`), or user-specific content (such as `jane.example.org` and `john.example.org`). For those cases, you can define multiple supported URLs in `APP_PUBLIC_URL` by separating the individual URLs using the vertical bar or pipe character (`U+007C VERTICAL LINE`). In your application code, you should then decide which content to show based on the value of `$app->getCanonicalHost()`.

However, if you just want to serve the *same* content at *multiple* hostnames or schemes, you should consider using redirects instead, which can forward from different hostnames or schemes to one canonical form. In most of these cases, you’ll want to send HTTP status `301` for *permanent* redirects.

The single pages or endpoints of your application will be served at the routes that you define in the `app/` directory. A few sample routes have already been added.

For more detailed information on routes, please refer to the [documentation of the router](https://github.com/delight-im/PHP-Router).

### Charsets and encodings

Everything is UTF-8 all the way through! Just remember to save your own source files as UTF-8, too.

### Storage

You may store files private to your application in `storage/app/`. Feel free to store the files either in the root directory or in any number of subfolders that you create.

The storage path can be retrieved in your application code via `$app->getStoragePath('/path/to/subfolder/file.txt')`, for example.

Files inside `storage/app/` are private to your application. You may use PHP’s file handling utilities to work with the files or offer them to the user as a download (see "File downloads" below).

### Database access

Did you set your database credentials in `config/.env`? If the configuration is valid, getting a database connection is as simple as this:

```php
$app->db();
```

For information on how to read and write data using this instance, please refer to the [documentation of the database library](https://github.com/delight-im/PHP-DB).

**Note:** You don’t have to construct the database instance or establish the connection yourself. This will be done for you automatically. Just call the methods to read and write data on the instance provided.

### String handling

Convenient string handling in an object-oriented way with full Unicode support is available on any string after wrapping it in the `s(...)` helper function.

For information on all the methods available with strings wrapped in `s(...)`, please refer to the [documentation of the string library](https://github.com/delight-im/PHP-Str).

**Note:** There is no need for you to set up the `s(...)` shorthand anymore. This has been done already.

### Input validation

This framework comes with easy and safe input validation for `GET`, `POST` and cookie data as well as for any existing variable.

Both validation and filtering are performed automatically and *correctly typecast* values are returned for you.

If a value does not exist or if it’s invalid, you’re guaranteed to receive `null` as the result. This way, you don’t have to check for empty strings, `null`, `false` or invalid formats separately.

```php
$app->input()->get('username'); // equivalent to TYPE_STRING
$app->input()->get('profileId', TYPE_INT);
$app->input()->get('disabled', TYPE_BOOL);
$app->input()->get('weight', TYPE_FLOAT);
$app->input()->get('message', TYPE_TEXT);
$app->input()->get('recipientEmail', TYPE_EMAIL);
$app->input()->get('linkTo', TYPE_URL);
$app->input()->get('whoisIp', TYPE_IP);
$app->input()->get('transmission', TYPE_RAW);

$app->input()->post('country'); // equivalent to TYPE_STRING
$app->input()->post('zipCode', TYPE_INT);
$app->input()->post('subscribe', TYPE_BOOL);
$app->input()->post('price', TYPE_FLOAT);
$app->input()->post('csv', TYPE_TEXT);
$app->input()->post('newsletterEmail', TYPE_EMAIL);
$app->input()->post('referringSite', TYPE_URL);
$app->input()->post('signUpIp', TYPE_IP);
$app->input()->post('print', TYPE_RAW);

$app->input()->cookie('realName'); // equivalent to TYPE_STRING
$app->input()->cookie('lastLoginTimestamp', TYPE_INT);
$app->input()->cookie('hideAds', TYPE_BOOL);
$app->input()->cookie('cartTotalAmount', TYPE_FLOAT);
$app->input()->cookie('draft', TYPE_TEXT);
$app->input()->cookie('friend', TYPE_EMAIL);
$app->input()->cookie('social', TYPE_URL);
$app->input()->cookie('antiFraud', TYPE_IP);
$app->input()->cookie('display', TYPE_RAW);

$app->input()->value($username); // equivalent to TYPE_STRING
$app->input()->value($profileId, TYPE_INT);
$app->input()->value($disabled, TYPE_BOOL);
$app->input()->value($weight, TYPE_FLOAT);
$app->input()->value($message, TYPE_TEXT);
$app->input()->value($recipientEmail, TYPE_EMAIL);
$app->input()->value($linkTo, TYPE_URL);
$app->input()->value($whoisIp, TYPE_IP);
$app->input()->value($transmission, TYPE_RAW);
```

### HTML escaping

In order to escape any string for safe use in HTML, just wrap the string in the `e(...)` helper function:

```php
echo e('Bob <b>says</b> "Hello world"');
// => Bob &lt;b&gt;says&lt;/b&gt; &quot;Hello world&quot;

// or

echo '<img src="'.e($profilePictureUrl).'" alt="Bob" width="96" height="96">';
```

```html
<!-- or -->

<img src="<?= e($profilePictureUrl) ?>" alt="Bob" width="96" height="96">
```

If you use templates stored in the `views/` directory (see below), you don’t need this, as templates come with automatic escaping by default.

### Templates

Place your HTML, XML, CSV or LaTeX (or anything else) templates in the `views/` folder. Make them powerful and re-usable with the [Twig language](http://twig.sensiolabs.org/doc/templates.html). A few examples for such templates have already been added.

In order to render a template, just load it in your PHP code:

```php
echo $app->view('welcome.html.twig');
```

Usually, you’ll want to pass data to the templates as well:

```php
echo $app->view('welcome.html.twig', [
    'userId' => $id
    'name' => $name
    'messages' => $messages
]);
```

The data that you passed in the second parameter of the `$app->view(...)` method will be available in your template:

```html
<h1>Hello, {{ name }}!</h1>
```

All output is escaped (and thus safe) by default! If you need to disable escaping for a variable, you can do so using the `raw` filter:

```html
<span>Goodbye, {{ name | raw }}!</span>
```

Conditional expressions and loops are available as control structures:

```html
{% if messages|length > 0 %}
    <p>You have <strong>{{ messages|length }}</strong> new messages!</p>
{% endif %}
```

```html
<div>
    {% for picture in pictures %}
        <img src="{{ picture.url }}" alt="{{ picture.description }}" width="100" height="100">
    {% endfor %}
</div>
```

For less code and more markup re-use, templates can be embedded inside each other:

```html
{% include 'includes/header.html.twig' %}
```

Embedding can even be combined with loops:

```html
<ul>
    {% for user in users %}
        <li>{{ user.username }}</li>
        {% for picture in user.pictures %}
            {% include 'picture_box.html.twig' %}
            <!-- `picture_box` has access to `picture` -->
        {% endfor %}
    {% endfor %}
</ul>
```

You can add custom filters in your PHP code which can then be used to modify data in the templates:

```php
$app->getTemplateManager()->addFilter('repeatAndReverse', function ($str) {
    return strrev($str.' '.$str);
});
```

```html
<p>We will repeat {{ myVariable | repeatAndReverse }} and reverse this!</p>
```

Moreover, you can add variables as globals in your PHP code which can then be accessed in the templates:

```php
$app->getTemplateManager()->addGlobal('googleAnalytics', $ga);
```

```html
{{ googleAnalytics.trackingCode | raw }}
</body>
```

The main application object is available as a global variable named `app` by default. This means that you can access all methods from the application object in your templates:

```html
<link rel="icon" type="image/png" href="{{ app.url('/favicon.ico') }}">
```

Finally, you can add functions from your PHP code which then become available for use in the templates:

```php
$app->getTemplateManager()->addFunction('translate', function ($value) {
    return gettext($value);
});
```

```html
<p>{{ translate('Thanks for joining our community!') }}</p>
```

Please refer to the [documentation of the template engine](http://twig.sensiolabs.org/doc/templates.html) for more information on how to write templates.

### Authentication

Convenient and secure authentication is always available via the `$app->auth()` helper method.

For information on how to register or log in users with a few lines of code, please refer to the [documentation of the authentication library](https://github.com/delight-im/PHP-Auth).

### Sessions and cookies

Convenient management of session data and cookies is available via the two classes `\Delight\Cookie\Cookie` and `\Delight\Cookie\Session`.

For information on how to use these two classes, please refer to [the documentation of the session and cookie library](https://github.com/delight-im/PHP-Cookie).

**Note:** You don’t have to include these two classes yourself anymore. They are included and loaded automatically.

### Flash messages

If you want to store short messages to be displayed right upon the next request, you can use the built-in flash message helpers:

```php
$app->flash()->success('Congratulations!');
// or
$app->flash()->info('Please note!');
// or
$app->flash()->warning('Watch out!');
// or
$app->flash()->danger('Oh snap!');
```

In order to *display* the messages, which you’ll probably want to do in your templates, use the methods available on the `$app->flash()` instance for retrieving the data. Alternatively, use one of the built-in partial templates for popular front-end solutions:

 * [Bootstrap 3](http://getbootstrap.com/)

   ```
   {% include 'includes/flash/bootstrap-v3.html.twig' %}
   ```

### Mail

The pre-configured mailing component is always available via the `$app->mail()` helper:

```php
$message = $app->mail()->createMessage();

$message->setSubject('This is the subject');
$message->setFrom([ 'john.doe@example.com' => 'John Doe' ]);
$message->setTo([ 'jane@example.org' => 'Jane Doe' ]);
$message->setBody('Here is the message');

if ($app->mail()->send($message)) {
    // Success
}
else {
    // Failure
}
```

Please refer to the [documentation of the mailing component](http://swiftmailer.org/docs/messages.html) for more information on how to build message instances.

### Obfuscation of IDs

Do you want to prevent your internal IDs from leaking to competitors or attackers when using them in URLs or forms?

Just use the following two methods to encode or decode your IDs, respectively:

```php
$app->ids()->encode(6); // => e.g. "43Vht7"
// and
$app->ids()->decode('43Vht7'); // => e.g. 6
```

If you want to configure the underlying transformation, you simply have to change the following configuration values in your `config/.env` file:

 * `SECURITY_IDS_ALPHABET`
 * `SECURITY_IDS_PRIME`
 * `SECURITY_IDS_INVERSE`
 * `SECURITY_IDS_RANDOM`

Please refer to the [documentation of the ID obfuscation library](https://github.com/delight-im/PHP-IDs) for information on what constitutes proper values and how to generate them.

### Uploading files

Whenever you want to let users upload files to your application, there’s a built-in component that does this in a convenient and safe way:

```php
$upload = $app->upload('/uploads/users/' . $userId . '/avatars');
$upload->from('my-input-name');
$upload->save();
```

For more information on how to use the upload handler, including more advanced controls, please refer to the [documentation of the file upload library](https://github.com/delight-im/PHP-FileUpload#usage).

If you want to check for the presence of (optional) file uploads before actually processing them, you can use the following two helpers:

```php
$app->hasUploads();
// and/or
$app->hasUpload('my-input-name');
```

### Serving files

You can serve (static) files to the client from your PHP code, e.g. after performing access control:

```php
$app->serveFile($app->getStoragePath('/photos/314.png'), 'image/png');
```

Most commonly, this will be used to serve images. If certain files aren’t public or cannot be accessed directly for other reasons, thus requiring more dynamic behavior, the helper above will be useful.

If you want to offer files for download, however, e.g. documents or videos, there is another helper that is more suitable (see "File downloads").

### File downloads

In order to send strings or files to the client and prompt a download, use one of the following methods:

```php
$app->downloadContent('<html>...</html>', 'Bookmarks-Export.html', 'text/html');
// or
$app->downloadFile($app->getStoragePath('/photos/42.jpg'), 'My Photo.jpg', 'image/jpeg');
```

### Internationalization (I18N) and localization (L10N)

If you want to localize your application and translate strings in your application code and in your templates, the built-in internationalization component offers everything you need.

First, in order to enable the component, go to your configuration in `config/.env` and set `I18N_SUPPORTED_LOCALES` to a comma-separated list of languages or locales that you want to support. You can use the codes [listed here](https://github.com/delight-im/PHP-I18N/blob/master/src/Codes.php):

```
I18N_SUPPORTED_LOCALES=en-US,da-DK,es,es-AR,ko
```

Some of the locales you want to use may not be installed on your local machine or on the server that you deploy your application to. In that case, you will have to [install](https://github.com/delight-im/PHP-I18N#decide-on-your-initial-set-of-supported-locales) the locales first.

The best language for the client is usually detected and applied automatically (from the HTTP header `Accept-Language` that is sent by the client), but you may select a language manually by supplying it in a subdomain (e.g. `da-DK.example.com`), in a path prefix (e.g. `http://www.example.com/pt-BR/welcome.html`), or as a parameter in the query string (as `locale`, `language`, `lang` or `lc`).

Optionally, if you want to store the currently selected language in the session or in a cookie, you can define the key or name to use in your configuration in `config/.env`:

```
I18N_SESSION_FIELD=language
# and / or
I18N_COOKIE_NAME=lang
```

You can always check the currently active locale for a client using `$app->i18n()->getLocale()` in your application code or using `app.i18n().getLocale()` in your templates. Apart from that, there are many other [helpers and utilities](https://github.com/delight-im/PHP-I18N#information-about-locales) available that let you access names and other information for your set of locales.

Next, you need to identify and mark all strings in your code that can be translated. For an existing application, this might require some more effort, but you can start with only a few strings, of course.

This allows for the marked strings to be extracted automatically, so that they can be translated outside of the actual code, before being inserted again automatically during runtime.

 * [Basic strings](https://github.com/delight-im/PHP-I18N#basic-strings)

   * In your application code

     ```
     _('Welcome to our online store!');
     ```

   * In your templates

     ```
     {{ _('Welcome to our online store!') }}
     ```

 * [Strings with formatting](https://github.com/delight-im/PHP-I18N#strings-with-formatting)

   * In your application code

     ```
     _f('Hello %s!', 'Jane');
     ```

   * In your templates

     ```
     {{ _f('Hello %s!', 'Jane') }}
     ```

 * [Strings with extended formatting](https://github.com/delight-im/PHP-I18N#strings-with-extended-formatting)

   * In your application code

     ```
     _fe('{0} was born on {1, date}', 'John', -14182916);
     ```

   * In your templates

     ```
     {{ _fe('{0} was born on {1, date}', 'John', -14182916) }}
     ```

 * [Singular and plural forms](https://github.com/delight-im/PHP-I18N#singular-and-plural-forms)

   * In your application code

     ```
     _p('The file has been saved.', 'The files have been saved.', 3);
     ```

   * In your templates

     ```
     {{ _p('The file has been saved.', 'The files have been saved.', 3) }}
     ```

 * [Singular and plural forms with formatting](https://github.com/delight-im/PHP-I18N#singular-and-plural-forms-with-formatting)

   * In your application code

     ```
     _pf('You have %d new message', 'You have %d new messages', 32);
     ```

   * In your templates

     ```
     {{ _pf('You have %d new message', 'You have %d new messages', 32) }}
     ```

 * [Singular and plural forms with extended formatting](https://github.com/delight-im/PHP-I18N#singular-and-plural-forms-with-extended-formatting)

   * In your application code

     ```
     _pfe('There is {0, number} monkey in {1}.', 'There are {0, number} monkeys in {1}.', 5, 'Anytown');
     ```

   * In your templates

     ```
     {{ _pfe('There is {0, number} monkey in {1}.', 'There are {0, number} monkeys in {1}.', 5, 'Anytown') }}
     ```

 * [Strings with context](https://github.com/delight-im/PHP-I18N#strings-with-context)

   * In your application code

     ```
     _c('Select order:', 'sorting');
     ```

   * In your templates

     ```
     {{ _c('Select order:', 'sorting') }}
     ```

 * [Strings marked for later translation](https://github.com/delight-im/PHP-I18N#strings-marked-for-later-translation)

   * In your application code

     ```
     $title = _m('Profile picture');
     ```

Finally, after having identified and marked some (or all) strings for translation in your application code and in your templates, you can use the built-in tools to extract the strings automatically.

To allow for the extraction of the strings from your templates, refresh and replace the cached versions of your template files by running the following two commands in the root directory of your project:

```bash
$ sudo -u www-data php ./index.php clear-template-cache
$ sudo -u www-data php ./index.php precompile-templates
```

Then, if you want to create or update a PO (Portable Object) file for a specific language, along with its MO (Machine Object) version, which is usually what you want, you need to run the following command in the root directory of the project (using the locale `mr-IN` as an example):

```bash
$ bash ./i18n.sh mr-IN
```

If you want a generic POT (Portable Object Template) file instead, just drop the locale code from the end:

```bash
$ bash ./i18n.sh
```

Now you can [translate](https://github.com/delight-im/PHP-I18N#translating-the-extracted-strings) the exported PO or POT files, either manually or using any tool or service you want. By running the commands listed above again, you can [compile](https://github.com/delight-im/PHP-I18N#exporting-translations-to-binary-format) the translated files to an updated binary version, after which you only need to restart your web server for the translations to appear in your application.

### Helpers and utilities

The following helpers and utilities are available throughout your application code and in all templates:

 * `$app->url($toPath)` (`app.url($toPath)` in templates)
 * `$app->redirect($toPath)`
 * `$app->setStatus($code)`
 * `$app->setContentType($type)`
 * `$app->flash()` (`app.flash()` in templates)
 * `$app->currentRoute()` (`app.currentRoute()` in templates)
 * `$app->currentUrl()` (`app.currentUrl()` in templates)
 * `$app->getCanonicalHost()` (`app.getCanonicalHost()` in templates)
 * `$app->getHost()` (`app.getHost()` in templates)
 * `$app->getClientIp()` (`app.getClientIp()` in templates)
 * `$app->isClientLoopback()` (`app.isClientLoopback()` in templates)
 * `$app->isClientCli()`
 * `$app->hasCliArgument()`
 * `$app->hasCliArgument($position)`
 * `$app->getCliArgument()`
 * `$app->getCliArgument($position)`
 * `$app->isHttp()`
 * `$app->isHttps()`
 * `$app->getProtocol()`
 * `$app->getPort()`
 * `$app->getQueryString()`
 * `$app->getRequestMethod()`

## Backups

This package includes support for automatic backups of your database, app storage and log files in compressed and encrypted form. These backups are not enabled by default. For information on how to enable them and make good use of them, please see below.

### Enabling backups

 1. Execute the following commands on Linux if this directory (containing the `README.md`) is in `/var/www/html/` on the server:

    ```
    # Make the backup script executable while preventing modifications to its content
    $ sudo chmod 0500 /var/www/html/backup.sh
    # Assign ownership of the backup script to the superuser
    $ sudo chown root:root /var/www/html/backup.sh
    # Generate a public/private keypair and store the private key in the `backups` directory
    $ openssl genpkey -algorithm RSA -out /var/www/html/backups/asymmetric-key.private.pem -pkeyopt rsa_keygen_bits:4096
    # Extract the public key from the new private key in the `backups` directory
    $ openssl rsa -in /var/www/html/backups/asymmetric-key.private.pem -outform PEM -out /var/www/html/backups/asymmetric-key.public.pem -pubout
    # Allow only read access by its owner for the private key
    $ sudo chmod 0400 /var/www/html/backups/asymmetric-key.private.pem
    # Grant everybody read access to the public key but no other rights
    $ sudo chmod 0444 /var/www/html/backups/asymmetric-key.public.pem
    # Make the `backups` directory writable only by its owner
    $ sudo chmod 0755 /var/www/html/backups
    # Assign ownership of the `backups` directory and its contents to the superuser
    $ sudo chown -R root:root /var/www/html/backups
    ```

 1. Copy – or perhaps *move* – the private key that has been generated above (`backups/asymmetric-key.private.pem`) to a *secure* location on *another machine*. It is important that you choose a location where *you won’t lose* the key and *nobody else will be able to view* the key. Without the private key, you won’t be able to restore your backups later.

 1. In order to set up the periodic execution of the backup script, run

    ```
    $ sudo crontab -e
    ```

    and add a new line with the following content:

    ```
    30 4 * * * /bin/bash /var/www/html/backup.sh > /var/www/html/backups/last.log 2>&1
    ```

    This will create a new backup every night at 04:30. While daily backups should usually be the minimum, you *may* create backups more frequently, if required, e.g. by separating multiple hours with a comma in the crontab entry, as in `4,10,16,22`.

    If you don’t want your backups to be encrypted, pass `unencrypted` as the only argument to `backup.sh`. Otherwise, you may pass `encrypted`, which is also the default.

### Moving backups offsite

For a truly effective backup strategy, of course, you should move the archives created in the `backups/` directory *offsite*, i.e. to a remote location such as another server that you maintain specifically for backups.

Thus, you should either set up a periodic task on a remote machine to *pull* the contents of the `backups/` directory regularly, or set up a periodic task on the local machine to *push* the backups to a remote location with *append-only* privileges (so that a compromised server could not destroy all your backups).

## Security

 * **SQL injections** are prevented if you write to the database using prepared statements only. That is, just follow the examples from the "Database access" section above.
 * **Cross-site scripting (XSS)** protection is available via the convenient escaping and templating features (see sections "HTML escaping" and "Templates" above).
 * **Clickjacking** prevention is built-in and applied automatically.
 * **Content sniffing (MIME sniffing)** protection is built-in and applied automatically.
 * **Cross-site request forgery (CSRF)** mitigation is built-in as well using [same-site cookies](https://github.com/delight-im/PHP-Cookie). For this automatic protection to be sufficient, however, you must prevent XSS attacks and you must not use the HTTP method `GET` for "dangerous" operations.
 * Safe and convenient input validation is built-in as a solid basis for handling untrusted input.

## Contributing

All contributions are welcome! If you wish to contribute, please create an issue first so that your feature, problem or question can be discussed.

## License

This project is licensed under the terms of the [MIT License](https://opensource.org/licenses/MIT).
