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
use Flarum\Api\Controller\UpdatePostController;
use Flarum\Post\Post;

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

    // 扩展创建讨论控制器
    (new Extend\ApiController(CreateDiscussionController::class))
        ->prepareDataForSerialization(function ($controller, $data, $request, $document) {
            $actor = $request->getAttribute('actor');
            // 只在用户有权限时提取 URL
            if ($actor->can('extractUrl')) {
                $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);
                $originalUrl = Arr::get($attributes, 'attributes.originalUrl', '');

                if ($originalUrl) {
                    $data->original_url = $originalUrl;
                    $data->save();
                }
            }
        }),


    // 扩展更新讨论控制器
    (new Extend\ApiController(UpdateDiscussionController::class))
        ->prepareDataForSerialization(function ($controller, $data, $request, $document) {
            $actor = $request->getAttribute('actor');
            // 只在用户有权限时提取 URL
            if ($actor->can('extractUrl')) {
                $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);
                $originalUrl = Arr::get($attributes, 'originalUrl', null);

                if ($originalUrl !== null) {
                    $data->original_url = $originalUrl;
                }

                $data->save();
            }
        }),


    // 扩展更新帖子控制器
    (new Extend\ApiController(UpdatePostController::class))
        ->prepareDataForSerialization(function ($controller, $data, $request, $document) {
            $actor = $request->getAttribute('actor');

            // 确保用户有权限更新 URL
            if ($actor->can('extractUrl')) {
                $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);
                $content = Arr::get($attributes, 'content', '');

                // 检查内容是否以 http:// 或 https:// 开头
                $urlPattern = '/^(https?:\/\/[^\s]+)/';
                preg_match($urlPattern, $content, $matches);

                // 调试日志
                error_log('Matched URL: ' . print_r($matches, true));

                if (!empty($matches)) {
                    // 如果内容开头是 URL，则更新 original_url
                    $originalUrl = $matches[0];
                    $discussion = $data->discussion;
                    $discussion->original_url = $originalUrl;
                    error_log('Original URL set to: ' . $originalUrl);
                } else {
                    // 如果内容开头不是 URL，重置 original_url 为空
                    $discussion = $data->discussion;
                    $discussion->original_url = '';
                    error_log('Original URL cleared');
                }

                // 保存更改
                $discussion->save();
            }
        }),
    // 添加权限策略
    (new Extend\Policy())
        ->modelPolicy(Discussion::class, RepostPolicy::class),
    // 在管理员界面中添加权限设置
    (new Extend\Settings())
        ->serializeToForum('repost.extractUrl', 'repost.extractUrl', 'boolval', false),
];
