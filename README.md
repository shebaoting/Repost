# Repost

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/shebaoting/flarum-repost.svg)](https://packagist.org/packages/shebaoting/flarum-repost) [![Total Downloads](https://img.shields.io/packagist/dt/shebaoting/flarum-repost.svg)](https://packagist.org/packages/shebaoting/flarum-repost)

**Repost** is a Flarum 2.x extension for publishing link-based discussions. If a new discussion starts with an HTTP or HTTPS URL, the extension stores that URL as the discussion's original source and turns the discussion list title into an external link. The local discussion page remains available, so community members can still reply, moderate, and discuss the linked content inside Flarum.

[中文介绍](README.zh-CN.md)

## Features

- Detects URLs at the beginning of new discussion content.
- Stores the detected URL on the discussion as `originalUrl`.
- Shows the source domain in the discussion list metadata.
- Opens the original source URL when users click the discussion title/list main area.
- Keeps the reply/comment count linked to the local Flarum discussion page.
- Displays a notice on the discussion page to show that the topic is a reposted source.
- Adds an admin permission for controlling who can extract and save original URLs.
- Supports existing 1.x data stored in the `discussions.original_url` column.
- Ships English and Chinese translation files.

## Requirements

- Flarum `^2.0.0-beta`
- PHP `^8.3`

This extension is intended for Flarum 2.x. Use the `v0.x` series for old Flarum 1.x installations.

## Installation

Install the extension with Composer:

```sh
composer require shebaoting/flarum-repost:"^2.0"
php flarum migrate
php flarum cache:clear
```

If you are using a local path repository during development, require the same 2.x constraint from your Flarum app:

```sh
composer require shebaoting/flarum-repost:"^2.0" -W
php flarum migrate
php flarum cache:clear
```

## Updating

```sh
composer update shebaoting/flarum-repost -W
php flarum migrate
php flarum cache:clear
```

If your forum still requires the old package name, switch the Composer requirement first:

```sh
composer remove shebaoting/repost --no-update
composer require shebaoting/flarum-repost:"^2.0" -W
```

After clearing the cache, refresh the forum page in the browser so Flarum regenerates the combined frontend assets.

## Configuration

Go to **Admin > Permissions** and configure the Repost permission:

- **Extract original URL**: users with this permission can create discussions whose first line starts with a URL and have that URL saved as the discussion's original source.

Users without this permission can still create normal discussions, but the extension will not save an `originalUrl` value for them.

## Usage

Create a discussion whose content starts with a full URL:

```text
https://example.com/article

This is a short note about why this article is worth discussing.
```

When the discussion is published:

1. The URL is stored as the discussion's original source.
2. The discussion list shows the source domain, for example `example.com`.
3. Clicking the discussion title opens the original source in a new tab.
4. Clicking the reply/comment icon or number opens the local Flarum discussion page.
5. Replies, moderation actions, tags, subscriptions, and other Flarum discussion features continue to work on the local page.

## Behavior Details

Only URLs that appear at the very beginning of the discussion content are extracted. The URL must start with `http://` or `https://`.

The extension does not fetch, scrape, or copy the remote page. It only stores the source URL and adjusts the discussion list link behavior.

The local discussion URL remains the canonical place for comments. This is useful for RSS imports, news sharing, bookmarks, link roundups, and communities where users want to discuss external content without duplicating the full article.

## Data And Migration Notes

Repost stores the original source URL in the `discussions.original_url` column.

For Flarum 1.x upgrades, existing values in this column are preserved. The migration is idempotent and will not recreate the column if it already exists.

## Development

Install JavaScript dependencies and build the frontend assets:

```sh
cd js
corepack yarn install
corepack yarn build
```

Useful checks:

```sh
composer validate --no-check-publish
find . -path './vendor' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Troubleshooting

If the discussion list still links every part of a reposted discussion to the external source, clear Flarum's cache and refresh the forum:

```sh
php flarum cache:clear
```

If the original URL is not saved, check that the actor has the **Extract original URL** permission and that the discussion content begins directly with an `http://` or `https://` URL.

## Links

- [My Community](https://wyz.xyz)
- [Packagist](https://packagist.org/packages/shebaoting/flarum-repost)
- [GitHub](https://github.com/shebaoting/flarum-repost)

## License

This extension is open-sourced software licensed under the [MIT license](LICENSE).
