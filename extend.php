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
use Flarum\Post\Event\Saving;


set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // 捕获弃用警告
    if (error_reporting() === 0) {
        return false; // 忽略错误控制运算符 (@) 影响的错误
    }
    if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
        // 记录弃用警告日志，或者直接返回 true 忽略它们
        error_log("Deprecated warning: $errstr in $errfile on line $errline");
        return true; // 返回 true 表示错误已经被处理，不继续传播
    }
    return false; // 返回 false 表示让其他错误处理程序继续处理
});


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
            if ($actor->can('repost.extractUrl')) {
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
            if ($actor->can('repost.extractUrl')) {
                $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);
                $originalUrl = Arr::get($attributes, 'originalUrl', null);

                if ($originalUrl !== null) {
                    $data->original_url = $originalUrl;
                }

                $data->save();
            }
        }),


    // 使用事件监听器扩展更新帖子操作
    (new Extend\Event())
        ->listen(Saving::class, Listeners\HandleOriginalUrl::class),


    // 添加权限策略
    (new Extend\Policy())
        ->modelPolicy(Discussion::class, RepostPolicy::class),
    // 在管理员界面中添加权限设置
    (new Extend\Settings())
        ->serializeToForum('repost.extractUrl', 'repost.extractUrl', 'boolval', false),
];
