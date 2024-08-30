<?php

namespace Shebaoting\Repost\Listeners;

use Flarum\Post\Event\Saving;
use Illuminate\Support\Arr;

class HandleOriginalUrl
{
    public function handle(Saving $event)
    {
        $post = $event->post;
        $discussion = $post->discussion;
        $actor = $event->actor;

        // 仅在帖子内容更新时处理 original_url
        if (isset($event->data['attributes']['content']) && $actor->can('repost.extractUrl')) {
            $attributes = Arr::get($event->data, 'attributes', []);
            $content = Arr::get($attributes, 'content', '');

            // 检查内容是否以 http:// 或 https:// 开头
            $urlPattern = '/^(https?:\/\/[^\s]+)/';
            preg_match($urlPattern, $content, $matches);

            if (!empty($matches)) {
                // 如果内容开头是 URL，则更新 original_url
                $originalUrl = $matches[0];
                $discussion->original_url = $originalUrl;
            } else {
                // 如果内容开头不是 URL，不改变原有的 original_url
                // 或者可以选择清空 original_url，取决于业务逻辑
                $discussion->original_url = '';
            }

            // 保存更改
            $discussion->save();
        }
    }
}
