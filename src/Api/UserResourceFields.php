<?php

namespace Gtdxyz\Signature\Api;

use Flarum\Api\Context;
use Flarum\Api\Schema;
use Flarum\User\User;
use Gtdxyz\Signature\Formatter\SignatureFormatter;
use Gtdxyz\Signature\Support\SignatureSaver;

class UserResourceFields
{
    public function __construct(
        protected SignatureFormatter $formatter,
        protected SignatureSaver $saver
    ) {
    }

    public function __invoke(): array
    {
        return [
            Schema\Str::make('signature')
                ->nullable()
                ->get(fn (User $user) => $this->formatter->unparse($user->getAttribute('signature')))
                ->writable($canEditSignature = fn (User $user, Context $context) => $context->getActor()->can('editSignature', $user))
                ->set(fn (User $user, ?string $value, Context $context) => $this->saver->save($user, $context->getActor(), $value)),

            Schema\Str::make('signatureHtml')
                ->nullable()
                ->get(function (User $user) {
                    $signature = $user->getAttribute('signature');

                    return $signature ? $this->formatter->render($signature) : null;
                }),

            Schema\Boolean::make('canEditSignature')
                ->get($canEditSignature),

            Schema\Boolean::make('canHaveSignature')
                ->get(fn (User $user) => $user->hasPermission('haveSignature')),
        ];
    }
}
