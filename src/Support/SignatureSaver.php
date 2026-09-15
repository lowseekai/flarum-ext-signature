<?php

namespace Gtdxyz\Signature\Support;

use Flarum\User\User;
use Gtdxyz\Signature\Event\SignatureSaved;
use Gtdxyz\Signature\Event\SignatureSaving;
use Gtdxyz\Signature\Formatter\SignatureFormatter;
use Gtdxyz\Signature\Validator\SignatureValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

class SignatureSaver
{
    public function __construct(
        protected Dispatcher $events,
        protected SignatureFormatter $formatter,
        protected SignatureValidator $validator
    ) {
    }

    public function save(User $user, User $actor, ?string $signature): void
    {
        $this->checkPermissions($actor, $user);
        $this->validator->assertValid(['signature' => $signature]);

        $signature = Str::of($signature ?? '')->trim();
        $user->setAttribute(
            'signature',
            $signature->isEmpty() ? null : $this->formatter->parse($signature)
        );

        if ($user->isDirty('signature')) {
            $this->dispatchEvents($user, $actor);
        }
    }

    protected function checkPermissions(User $actor, User $user): void
    {
        $actor->assertCan('editSignature', $user);
    }

    protected function dispatchEvents(User $user, User $actor): void
    {
        $effectiveActor = $actor->id === $user->id ? null : $actor;

        $this->events->dispatch(new SignatureSaving($user, $effectiveActor));

        $user->afterSave(function (User $user) use ($effectiveActor) {
            $user->raise(new SignatureSaved($user, $effectiveActor));
        });
    }
}
