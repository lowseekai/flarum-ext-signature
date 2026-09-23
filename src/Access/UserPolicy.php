<?php

namespace Gtdxyz\Signature\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class UserPolicy extends AbstractPolicy
{
    public function editSignature(User $actor, User $user): ?string
    {
        if (! $user->hasPermission('haveSignature')) {
            return $this->deny();
        }

        if ($user->isAdmin() && !$actor->isAdmin()) {
            return $this->deny();
        }

        if ($actor->id === $user->id || $actor->hasPermission('moderateSignature')) {
            return $this->allow();
        }

        return null;
    }
}
