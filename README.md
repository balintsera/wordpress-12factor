# Twelve-Factor WordPress

WordPress, the [Twelve-Factor](http://12factor.net/) way: fully managed using Composer and configured using environment variables.

## General Concepts and Considerations

The WordPress installation is fully contained in a `wordpress` subfolder upon `composer install`. A `wp-config.php` resides in the root of the project, and uses several different environment variables to control behavior.

Automatic updates for WordPress or plugins, and theme editing, are disabled intentionally. What you deploy is what gets executed, which makes setups simple to deploy, and, most importantly, reproducible. See further below for information on how to update WordPress versions.

[WP-CLI](http://wp-cli.org) is used for easier (or automated) handling of tasks such as enabling plugins or storing configuration options. After a deploy, a set of pre-configured [Composer scripts](https://getcomposer.org/doc/articles/scripts.md) can run several administrative functions using WP-CLI, such as initially configuring the blog, and enabling plugins (this happens either automatically when using a Heroku button deploy, or manually). This means that the installation of plugins and their configuration can be part of your version controlled code, so you can easily re-create a blog installation without any manual steps that need separate documentation.


The assumption is that this installation runs behind a load balancer whose `X-Forwarded-Proto` header value can be trusted; it is used to determine whether the request protocol is HTTPS or not.

HTTPS is forced for Login and Admin functions. `WP_DEBUG` is on; errors do not get displayed, but should get logged to PHP's default error log, accessible e.g. using `heroku logs`.


## Manual Install / Deploy

### Setup Database 

`export DATABASE_URL=mysql://evistawp:8979h8ef67@127.0.0.1:32770/wpdb`

See [here](https://httpd.apache.org/docs/current/mod/mod_env.html#setenv) how to set up Apahce Enviroment variables (`/wordpress/.htaccess`)

### Clone

Clone this repo (we're naming the Git remote "`upstream`" since you'll likely want to have "`origin`" be your actual site - you can [sync](https://help.github.com/articles/syncing-a-fork) changes from this repository later):

```
$ git clone -o upstream https://github.com/dzuelke/wordpress-12factor
$ cd wordpress-12factor
```

If you like, you can locally install dependencies with [Composer](https://getcomposer.org):

```
$ composer install
```


### Finalize Installation

This will create tables and set up an admin user:

```
$ composer wordpress-setup-core-install -- --title="Evista WordPress" --admin_user=admin --admin_password=admin --admin_email=admin@example.com --url="http://exampleapp.com/"
```

If you'd like to interactively provide info instead (use a format like "`http://exampleapp.com/`" with your app name for the URL), you can run:

Finally, the following command will configure and enable plugins and set a reasonable structure for Permalinks:

```
$ composer wordpress-setup-finalize
```


## Installing a new Plugin or Theme

1. Search for your plugin or theme on [WordPress Packagist](http://wpackagist.org);
1. Click the latest version and check the version selector string in the text box that appears - it will look like `"wpackagist-theme/hueman": "1.5.7"` or `"wpackagist-plugin/akismet": "3.1.7"`;
1. You don't want such an exact version, but instead a more lenient selector like (in the case above) `^1.5.7` or at least `~1.5.7` (see the [Composer docs](https://getcomposer.org/doc/articles/versions.md#next-significant-release-operators) for details);
1. Run `composer require wpackagist-$type/name:^$version`, for example:

    ```
    composer require wpackagist-plugin/akismet:^3.1.7
    ```
    
    or
    
    ```
    composer require wpackagist-plugin/hueman:^1.5.7
    ```

1. Run `git add composer.json composer.lock` and `git commit`;
1. `git push heroku master`

### Activating a Plugin or Theme once

Run `heroku run 'vendor/bin/wp plugin activate` or `vendor/bin/wp theme activate` and pass the name of the plugin or theme (e.g. `wp theme activate hueman`).

However, if you're working on an actual project, you will also want to ensure that this step can be run as part of the installation - see the next section for info.

### Activating a Plugin or Theme, the repeatable way

See the `scripts` section in `composer.json` for inspiration. A [Composer script](https://getcomposer.org/doc/articles/scripts.md) named `wordpress-setup-enable-plugins` (which gets in turn called by another script) enables three plugins by default using WP-CLI's `wp` command, and you can just add yours to the list. You'll notice that a separate step before also configures one of the plugins; you can do the same for your customizations.

After you adjusted the scripts section, run `composer update --lock`, then `git add composer.json composer.lock`, and `git commit` the changes.

## Updating WordPress and Plugins

To update all dependencies:

```
$ composer update
```

Alternatively, run `composer update johnpbloch/wordpress` to only update WordPress, or e.g. `composer update wpackagist-plugin/sendgrid-email-delivery-simplified` to only update that plugin.

Afterwards, add, commit and push the changes:

```
$ git add composer.json composer.lock
$ git commit -m "new WordPress and Plugins"
$ git push [branch]
```

## Web Servers

This project runs Apache by default, using the `apache2-wordpress.conf` [configuration include](/articles/custom-php-settings#web-server-settings) and with the `wordpress/` directory [defined as the document root](/articles/deploying-php#configuring-the-document-root).

To use Nginx instead, [change](/articles/deploying-php#selecting-a-web-server) `Procfile` to the following:

```
web: vendor/bin/heroku-php-nginx -C nginx-wordpress.conf wordpress/
```

## WordPress Cron

Instead of having WordPress check on each page load if Cron Jobs need to be run (thus potentially slowing down the site for some users), you can invoke Cron externally:

1. Run `heroku config:set DISABLE_WP_CRON=true` (or set it using the [Heroku Dashboard](https://dasboard.heroku.com)) to disable built-in cron jobs;
1. Add [Heroku Scheduler](https://elements.heroku.com/addons/scheduler) to your application;
1. Add a job that, every 30 minutes, runs `vendor/bin/wp cron event run --all`.

## Environment Variables

`wp-config.php` will use the following environment variables (if multiple are listed, in order of precedence):

### Database Connection

`DATABASE_URL` (format `mysql://user:pass@host:port/dbname`) for database connections.

### AWS/S3

* `AWS_ACCESS_KEY_ID` or `BUCKETEER_AWS_ACCESS_KEY_ID` for the AWS Access Key ID;
* `AWS_SECRET_ACCESS_KEY` `BUCKETEER_AWS_SECRET_ACCESS_KEY` for the AWS Secret Access Key;


### WordPress Secrets

`WORDPRESS_AUTH_KEY`, `WORDPRESS_SECURE_AUTH_KEY`, `WORDPRESS_LOGGED_IN_KEY`, `WORDPRESS_NONCE_KEY`, `WORDPRESS_AUTH_SALT`, `WORDPRESS_SECURE_AUTH_SALT`, `WORDPRESS_LOGGED_IN_SALT`, `WORDPRESS_NONCE_SALT` should contain random secret keys for various WordPress functions; values can be obtained from https://api.wordpress.org/secret-key/1.1/salt/ (also see Manual Deploy instructions further above).

### Miscellaneous

`DISABLE_WP_CRON` set to "1" or "true" will disable automatic cron execution through browsers (see further above).
