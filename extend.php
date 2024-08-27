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

use Flarum\Extend;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Discussion;

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

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attributes(function (DiscussionSerializer $serializer, Discussion $discussion, array $attributes) {
            $attributes['original_url'] = $discussion->original_url;
            return $attributes;
        }),
    (new Extend\Listener())
        ->listen(Saving::class, function (Saving $event) {
            $discussion = $event->discussion;
            $actor = $event->actor;
            $data = $event->data;

            if (isset($data['attributes']['originalUrl'])) {
                $discussion->original_url = $data['attributes']['originalUrl'];
            }
        }),
];
