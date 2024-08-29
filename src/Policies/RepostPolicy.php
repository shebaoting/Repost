<?php

namespace Shebaoting\Repost\Policies;

use Flarum\User\Access\AbstractPolicy;
use Flarum\Discussion\Discussion;
use Flarum\User\User;

class RepostPolicy extends AbstractPolicy
{
    public function extractUrl(User $actor, Discussion $discussion)
    {
        // 检查用户是否具有提取URL的权限
        return $actor->hasPermission('discussion.extractUrl');
    }
}
