<?php

namespace Gtdxyz\Signature\Formatter;

use Flarum\Extension\ExtensionManager;
use Flarum\Formatter\Formatter;
use Illuminate\Cache\Repository;
use s9e\TextFormatter\Configurator;
use s9e\TextFormatter\Parser;

class SignatureFormatter extends Formatter
{
    /**
     * @var ExtensionManager
     */
    protected $extensions;

    public function __construct(Repository $repository, string $cacheDir, ExtensionManager $extensions)
    {
        parent::__construct($repository, $cacheDir);

        $this->extensions = $extensions;
    }

    protected function getComponent(string $name): mixed
    {
        $formatter = $this->cache->rememberForever('nodeloc-signature.formatter', function () {
            return $this->getConfigurator()->finalize();
        });

        return $formatter[$name];
    }

    protected function getParser(mixed $context = null): Parser
    {
        $parser = parent::getParser($context);

        $parser->disableTag('IFRAME');
        $parser->disableTag('EMBED');

        return $parser;
    }

    protected function getConfigurator(): Configurator
    {
        $configurator = parent::getConfigurator();

        if ($this->extensions->isEnabled('flarum-markdown')) {
            /** @phpstan-ignore-next-line */
            $configurator->Litedown;
        }

        if ($this->extensions->isEnabled('flarum-bbcode')) {
            (new \Flarum\BBCode\Configure())($configurator);
        }

        $configurator->tags->get("URL")->template =
            '<a href="{@url}" rel="{@rel}" target="_blank">
                <xsl:copy-of select="@rel|@title"/>
                <xsl:apply-templates/>
            </a>';

        return $configurator;
    }

    public function flush(): void
    {
        $this->cache->forget('nodeloc-signature.formatter');
    }
}
