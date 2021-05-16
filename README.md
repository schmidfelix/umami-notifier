![](https://banners.beyondco.de/Umami%20Notifier.png?theme=light&packageManager=&packageName=&pattern=bankNote&style=style_1&description=Get+your+site%27s+statistics+directly+to+your+slack+inbox&md=1&showWatermark=0&fontSize=100px&images=paper-airplane)

# Umami Notifier

Inspired by [BeyondCode's fathom-notifier](https://github.com/beyondcode/fathom-notifier), `umami-notifier` is a stand-alone CLI app to get
your [umami analytics insights](https://umami.is/) directly to your slack inbox.

# Documentation

## Installation

To install the umami-notifier, simply pull the repository from GitHub and install its composer dependencies:

```shell
git clone git@github.com:schmidfelix/umami-notifier.git
cd umami-notifier
composer install
```

That's it. You've successfully installed the notifier.

## Configuration

Next you have to tell the application where your umami server lives and how to connect to it.

To do so, copy the `.env.example` file to `.env` and fill out all the settings:

```dotenv
UMAMI_URL= # API endpoint for your umami instance. E.g.: https://umami.mysite.com/api
UMAMI_USER= # Your umami login username
UMAMI_PASSWORD= # You umami login password
INTERVAL=weeks # The interval the analytics should be fetched in. E.g.: 'week' to fetch the data from the last week, 'day' to get all data for the current day 
```

## Managing sites

### Adding a site

Before the system can notify you about your statistics you have to tell the app which sites should get notified.

To do so, go into your application directory and run

```shell
php umami sites:add
```

The app will ask you for the site id on your umami instance, a display name and a url for the incoming slack webhook.

> You have to add an [incoming webhook](https://slack.com/apps/A0F7XDUAZ-incoming-webhooks) to your Slack account.

### Removing a site

To stop notifying you about a site's statistics simply run

```shell
php umami sites:delete {site}
```

where `site` must be the umami site id.

### List all sites

To display all sites run:

```shell
php umami sites:list {--show-webhook}
```

It will show you all sites, and if the `--show-webhook` option is given, it will also show you the incoming webhook url
which is used to notify you.

## Notifying the sites statistics

To notify all the site's statistics simply run

```shell
php umami sites:notify
```

This will notify the latest statistics for all sites.

You may want to add this to your server's crontab, so that you'll get notified every X days, weeks, months, etc.

---

# Security

If you discover any security related issues, please email hey@felix-schmid.de instead of using the issue tracker.

## Credits

- [Felix Schmid](https://github.com/schmidfelix)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
