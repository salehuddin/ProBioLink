<?php

namespace App\Support;

use Illuminate\Support\Facades\Validator;

/**
 * Per-type rules for the JSON payload inside a block.
 *
 * The frontend types are the same contract; this is the server enforcing it.
 */
class BlockSchema
{
    public const LINK_URL_PATTERN = '#^(https?://|mailto:|tel:|/|\#)#i';

    /** @return array<string, array<int, mixed>> */
    public static function rules(string $type): array
    {
        $heading = ['nullable', 'string', 'max:120'];
        $text = ['nullable', 'string', 'max:2000'];

        return match ($type) {
            'hero', 'bio' => [
                'heading' => $heading,
                'body' => $text,
            ],
            'links' => [
                'heading' => $heading,
                'links' => ['nullable', 'array', 'max:20'],
                'links.*.label' => ['required', 'string', 'max:80'],
                'links.*.url' => ['required', 'string', 'max:2048', 'regex:'.self::LINK_URL_PATTERN],
            ],
            'experience' => [
                'heading' => $heading,
                'items' => ['nullable', 'array', 'max:20'],
                'items.*.role' => ['required', 'string', 'max:120'],
                'items.*.company' => ['required', 'string', 'max:120'],
                'items.*.period' => ['required', 'string', 'max:60'],
                'items.*.summary' => ['nullable', 'string', 'max:500'],
            ],
            'skills' => [
                'heading' => $heading,
                'tags' => ['nullable', 'array', 'max:40'],
                'tags.*' => ['string', 'max:40'],
            ],
            'services' => [
                'heading' => $heading,
                'services' => ['nullable', 'array', 'max:20'],
                'services.*.name' => ['required', 'string', 'max:120'],
                'services.*.description' => ['nullable', 'string', 'max:500'],
                'services.*.price' => ['nullable', 'string', 'max:60'],
            ],
            'testimonial' => [
                'quote' => ['required', 'string', 'max:1000'],
                'author' => ['nullable', 'string', 'max:120'],
                'role' => ['nullable', 'string', 'max:120'],
            ],
            default => [],
        };
    }

    /**
     * Prefix a type's rules for use inside a larger payload, e.g. "blocks.2.data".
     *
     * @return array<string, array<int, mixed>>
     */
    public static function rulesFor(string $type, string $prefix): array
    {
        $rules = [];

        foreach (self::rules($type) as $field => $fieldRules) {
            $rules["{$prefix}.{$field}"] = $fieldRules;
        }

        return $rules;
    }

    public static function validate(array $data, string $type, string $prefix = 'data'): void
    {
        Validator::make([$prefix => $data], self::rulesFor($type, $prefix))->validate();
    }
}
