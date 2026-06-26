# Repost 中文介绍

**Repost** 是一个适配 Flarum 2.x 的转载/外链讨论扩展。它会在新主题内容以 `http://` 或 `https://` 链接开头时，把这个链接保存为主题的原始来源，并让主题列表里的标题区域跳转到外部原文。与此同时，Flarum 本地主题页仍然保留，用户可以继续在站内回复、讨论、订阅和管理这个主题。

[English README](README.md)

## 功能特性

- 自动识别新主题正文开头的 HTTP/HTTPS 链接。
- 将识别到的链接保存到主题字段 `originalUrl`。
- 在主题列表信息里显示来源域名。
- 点击转载主题的标题/主体区域时，在新标签页打开原始来源链接。
- 点击右侧评论图标或评论数字时，进入 Flarum 本地主题详情页。
- 在本地主题详情页展示转载提示。
- 在后台权限中提供“提取原始链接”权限项。
- 兼容 Flarum 1.x 时代已经存在的 `discussions.original_url` 数据。
- 内置英文和中文语言文件。

## 环境要求

- Flarum `^2.0.0-beta`
- PHP `^8.3`

这个扩展的 2.x 版本用于 Flarum 2.x。如果你仍在使用 Flarum 1.x，请使用旧的 `v0.x` 版本。

## 安装

通过 Composer 安装：

```sh
composer require shebaoting/flarum-repost:"^2.0"
php flarum migrate
php flarum cache:clear
```

如果你在本地开发环境中使用 path repository，可以在 Flarum 项目里执行：

```sh
composer require shebaoting/flarum-repost:"^2.0" -W
php flarum migrate
php flarum cache:clear
```

## 更新

```sh
composer update shebaoting/flarum-repost -W
php flarum migrate
php flarum cache:clear
```

如果你的站点仍然使用旧包名，请先切换 Composer 依赖：

```sh
composer remove shebaoting/repost --no-update
composer require shebaoting/flarum-repost:"^2.0" -W
```

清理缓存后，刷新论坛页面，让 Flarum 重新生成合并后的前端资源。

## 后台配置

进入 **后台 > 权限**，配置 Repost 权限：

- **提取原始链接**：拥有这个权限的用户，在发布以 URL 开头的新主题时，扩展会保存这个 URL 作为主题的原始来源。

没有这个权限的用户仍然可以发布普通主题，但扩展不会为他们提交的主题保存 `originalUrl`。

## 使用方式

发布一个正文以完整 URL 开头的主题：

```text
https://example.com/article

这里写一点你想补充的说明，方便社区成员讨论这篇文章。
```

发布后：

1. 这个 URL 会被保存为主题的原始来源。
2. 主题列表会显示来源域名，例如 `example.com`。
3. 点击主题标题会在新标签页打开外部原文。
4. 点击右侧评论图标或评论数字会进入 Flarum 本地主题页。
5. 回复、管理、标签、订阅等 Flarum 主题功能仍然在本地主题页正常工作。

## 行为说明

扩展只会提取正文最开头的 URL。这个 URL 必须以 `http://` 或 `https://` 开头。

扩展不会抓取、采集或复制远程网页内容，只保存来源 URL，并调整主题列表的链接行为。

本地主题页仍然是评论和讨论的入口。这种模式适合 RSS 导入、新闻分享、收藏链接、外链聚合，以及希望围绕外部内容进行站内讨论的社区。

## 数据和迁移说明

Repost 会把原始来源链接保存在 `discussions.original_url` 字段中。

从 Flarum 1.x 升级时，已有字段和已有数据会被保留。迁移脚本是幂等的，如果字段已经存在，不会重复创建或清空旧数据。

## 开发

安装前端依赖并构建资源：

```sh
cd js
corepack yarn install
corepack yarn build
```

常用检查：

```sh
composer validate --no-check-publish
find . -path './vendor' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

## 常见问题

如果主题列表仍然把转载主题的所有区域都跳转到外部链接，请清理 Flarum 缓存并刷新页面：

```sh
php flarum cache:clear
```

如果原始链接没有保存，请确认发布用户拥有 **提取原始链接** 权限，并且主题正文是直接以 `http://` 或 `https://` 链接开头。

## 相关链接

- [我的社区](https://wyz.xyz)
- [Packagist](https://packagist.org/packages/shebaoting/flarum-repost)
- [GitHub](https://github.com/shebaoting/flarum-repost)

## 授权协议

本扩展基于 [MIT license](LICENSE) 开源。
