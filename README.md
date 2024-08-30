# Repost

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/shebaoting/repost.svg)](https://packagist.org/packages/shebaoting/repost) [![Total Downloads](https://img.shields.io/packagist/dt/shebaoting/repost.svg)](https://packagist.org/packages/shebaoting/repost)

**Repost** is a [Flarum](http://flarum.org) extension that enhances your forum by allowing posts that start with a URL to be treated as external links. When users click on such posts from the discussion list, they will be redirected to the external URL. However, users can still participate in discussions within the forum thread about the linked content.

## Installation

To install the extension via Composer, run the following command:

```sh
composer require shebaoting/repost:"*"
php flarum migrate
php flarum cache:clear
```
## Updating

To update the extension, use the following commands:

```sh
composer update shebaoting/repost:"*"
php flarum migrate
php flarum cache:clear
```

## Usage

1. When creating a new post, simply start the post content with a valid URL.
2. Once posted, the discussion will appear as an external link in the discussion list.
3. Clicking the discussion will redirect users to the specified URL, while still allowing for discussion within the forum.


## Links
* [My Community](https://wyz.xyz)
* [Packagist](https://packagist.org/packages/shebaoting/repost)
* [GitHub](https://github.com/shebaoting/repost)
* [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)

## License

This extension is open-sourced software licensed under the [MIT license](LICENSE).
