<?php

namespace Gtdxyz\Signature\Validator;

use Flarum\Foundation\AbstractValidator;
use Flarum\Locale\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Validation\Factory;
use Gtdxyz\Signature\Formatter\SignatureFormatter;
use Symfony\Component\DomCrawler\Crawler;

class SignatureValidator extends AbstractValidator
{
    protected $settings;
    protected $formatter;

    public function __construct(Factory $validator, TranslatorInterface $translator, SettingsRepositoryInterface $settings, SignatureFormatter $formatter)
    {
        parent::__construct($validator, $translator);

        $this->settings = $settings;
        $this->formatter = $formatter;

        $this->validator->extend('signature_images', function ($attribute, $value, $parameters, $validator) {
            return $this->validateSignatureImages($value);
        });
    }

    protected function getRules(): array
    {
        return [
            'signature' => [
                'nullable',
                'string',
                'max:' . $this->settings->get('signature.maximum_char_limit'),
                'signature_images',
            ],
        ];
    }

    private function validateSignatureImages($value)
    {
        if ($value === null || $value === '') {
            return true;
        }

        $parsedContent = $this->formatter->parse($value);

        // Create a Crawler instance for the XML content
        $crawler = new Crawler($parsedContent);

        // Filter for image tags - adjust the selector if needed based on your XML structure
        $images = $crawler->filter('img'); // Adjust the selector if your XML structure requires it

        // Image count check
        if ($images->count() > (int) $this->settings->get('signature.maximum_image_count')) {
            return false;
        }

        return true;
    }
}
