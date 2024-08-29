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
use Flarum\Api\Controller\CreateDiscussionController;
use Flarum\Api\Controller\UpdateDiscussionController;
use Flarum\Discussion\Discussion;
use Illuminate\Support\Arr;
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

    // 扩展 API Serializer，确保 original_url 被包含在 API 响应中
    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attributes(function (DiscussionSerializer $serializer, Discussion $discussion, array $attributes) {
            $attributes['original_url'] = $discussion->original_url;
            return $attributes;
        }),

    (new Extend\ApiController(CreateDiscussionController::class))
        ->prepareDataForSerialization(function ($controller, $data, $request, $document) {

            $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);
            $originalUrl = Arr::get($attributes, 'attributes.originalUrl', '');

            $data->original_url = $originalUrl;
            $data->save();
        }),

    // 在更新讨论时处理 original_url 字段
    (new Extend\ApiController(UpdateDiscussionController::class))
        ->prepareDataForSerialization(function ($controller, $data, $request, $document) {

            $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);
            $originalUrl = Arr::get($attributes, 'attributes.originalUrl', $data->original_url);


            $data->original_url = $originalUrl;
            $data->save();
        }),
    // 注册自定义权限
    (new Extend\Policy())
        ->modelPolicy(Discussion::class, RepostPolicy::class),

    (new Extend\ApiSerializer(\Flarum\Api\Serializer\ForumSerializer::class))
        ->attributes(function ($serializer, $model, $attributes) {
            $actor = $serializer->getActor();
            $attributes['canExtractUrl'] = $actor->can('repost.extract_url');
            return $attributes;
        }),

    // 在 Flarum 管理面板中注册权限
    (new Extend\Settings())
        ->serializeToForum('canExtractUrl', 'repost.extract_url', 'boolval', false),
];
