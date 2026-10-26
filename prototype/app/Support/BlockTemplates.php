<?php

namespace App\Support;

class BlockTemplates
{
    /**
     * Every block type the editor can create, with the empty shape it starts from.
     *
     * @var array<string, array<string, mixed>>
     */
    public const DEFAULTS = [
        'hero' => [
            'heading' => 'Your name or availability',
            'body' => 'One line about what you do next.',
        ],
        'bio' => [
            'heading' => 'About',
            'body' => 'Two or three sentences on what you do and who you do it for.',
        ],
        'links' => [
            'heading' => 'Find me',
            'links' => [
                ['label' => 'Website', 'url' => 'https://'],
            ],
        ],
        'experience' => [
            'heading' => 'Experience',
            'items' => [
                ['role' => 'Role', 'company' => 'Company', 'period' => '2024 — now', 'summary' => ''],
            ],
        ],
        'skills' => [
            'heading' => 'Skills',
            'tags' => ['Skill'],
        ],
        'services' => [
            'heading' => 'What I can do for you',
            'services' => [
                ['name' => 'Service', 'description' => 'What the client gets.', 'price' => ''],
            ],
        ],
        'testimonial' => [
            'quote' => 'What a client or manager said about working with you.',
            'author' => 'Name',
            'role' => 'Title, Company',
        ],
    ];

    public static function types(): array
    {
        return array_keys(self::DEFAULTS);
    }

    public static function defaults(string $type): array
    {
        return self::DEFAULTS[$type] ?? [];
    }
}
