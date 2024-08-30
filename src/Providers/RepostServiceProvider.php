<?php

namespace Shebaoting\Repost\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Discussion\Discussion;
use Flarum\User\User;

class RepostServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        // 通过宏方法扩展模型
        Discussion::macro('canExtractUrl', function (User $actor) {
            return $actor->hasPermission('repost.extractUrl');
        });
    }
}
