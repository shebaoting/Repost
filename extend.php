<?php

/*
 * This file is part of shebaoting/repost.
 *
 * Copyright (c) 2024 shebaoting.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Shebaoting\Repost;

use Flarum\Api\Context;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Extend;
use Flarum\Discussion\Discussion;
use Flarum\Post\Event\Saving;
use Shebaoting\Repost\Policies\RepostPolicy;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),
    new Extend\Locales(__DIR__ . '/locale'),

    // 扩展模型，添加 original_url 属性
    (new Extend\Model(Discussion::class))
        ->cast('original_url', 'string'),

    (new Extend\ApiResource(Resource\DiscussionResource::class))
        ->fields(fn () => [
            Schema\Str::make('originalUrl')
                ->property('original_url')
                ->nullable()
                ->maxLength(255)
                ->writable(function (Discussion $discussion, Context $context): bool {
                    return $context->getActor()->hasPermission('repost.extractUrl');
                }),
        ]),

    (new Extend\ApiResource(Resource\ForumResource::class))
        ->fields(fn () => [
            Schema\Boolean::make('canExtractOriginalUrl')
                ->get(fn ($forum, Context $context): bool => $context->getActor()->hasPermission('repost.extractUrl')),
        ]),

    // 使用事件监听器扩展更新帖子操作
    (new Extend\Event())
        ->listen(Saving::class, Listeners\HandleOriginalUrl::class),


    // 添加权限策略
    (new Extend\Policy())
        ->modelPolicy(Discussion::class, RepostPolicy::class),
];
