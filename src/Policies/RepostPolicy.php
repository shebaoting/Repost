<?php

namespace Shebaoting\Repost\Policies;

use Flarum\User\Access\AbstractPolicy;
use Flarum\Discussion\Discussion;
use Flarum\User\User;

class RepostPolicy extends AbstractPolicy
{
    protected $model = Discussion::class;

    public function extractUrl(User $actor, Discussion $discussion)
    {
        return $actor->hasPermission('repost.extractUrl');
    }
}
