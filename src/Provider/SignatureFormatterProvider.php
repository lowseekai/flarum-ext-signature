<?php

namespace Gtdxyz\Signature\Provider;

use Flarum\Extension\ExtensionManager;
use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Foundation\Paths;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use Gtdxyz\Signature\Formatter\SignatureFormatter;

class SignatureFormatterProvider extends AbstractServiceProvider
{
    public function register(): void
    {
        $this->container->singleton('nodeloc-signature.formatter', function (Container $container) {
            return self::createFormatterInstance($container);
        });

        $this->container->alias('nodeloc-signature.formatter', SignatureFormatter::class);
    }

    public static function createFormatterInstance(Container $container): SignatureFormatter
    {
        return new SignatureFormatter(
            new Repository($container->make('cache.filestore')),
            $container[Paths::class]->storage.'/formatter',
            $container->make(ExtensionManager::class)
        );
    }
}
