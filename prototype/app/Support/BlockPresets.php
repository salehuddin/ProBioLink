<?php

namespace App\Support;

/**
 * Intent-based starting points from the product brief: each preset lays out
 * the blocks a person in that situation actually needs.
 */
class BlockPresets
{
    /**
     * @var array<string, array<int, array{type: string, data: array<string, mixed>}>>
     */
    public const PRESETS = [
        'job_seeker' => [
            'label' => 'Job seeker',
            'goal' => 'Get hired',
            'description' => 'Experience and skills first, links last.',
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'heading' => 'Open to senior product engineering roles',
                        'body' => 'Available from December · remote or hybrid',
                    ],
                ],
                [
                    'type' => 'bio',
                    'data' => [
                        'heading' => 'About',
                        'body' => 'Two or three sentences on what you do, who you do it for, and the kind of role you want next.',
                    ],
                ],
                [
                    'type' => 'experience',
                    'data' => [
                        'heading' => 'Experience',
                        'items' => [
                            ['role' => 'Role', 'company' => 'Company', 'period' => '2022 — now', 'summary' => 'One outcome with a number in it.'],
                            ['role' => 'Previous role', 'company' => 'Company', 'period' => '2019 — 2022', 'summary' => 'What changed because of you.'],
                        ],
                    ],
                ],
                [
                    'type' => 'skills',
                    'data' => [
                        'heading' => 'Skills',
                        'tags' => ['Skill', 'Tool', 'Strength'],
                    ],
                ],
                [
                    'type' => 'links',
                    'data' => [
                        'heading' => 'Find me',
                        'links' => [
                            ['label' => 'Download CV (PDF)', 'url' => '#'],
                            ['label' => 'LinkedIn', 'url' => 'https://linkedin.com/in/'],
                            ['label' => 'GitHub', 'url' => 'https://github.com/'],
                        ],
                    ],
                ],
            ],
        ],
        'freelancer' => [
            'label' => 'Freelancer',
            'goal' => 'Get booked',
            'description' => 'Services and proof first, then contact.',
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'heading' => 'Taking on two new clients this quarter',
                        'body' => 'Available from December · remote worldwide',
                    ],
                ],
                [
                    'type' => 'bio',
                    'data' => [
                        'heading' => 'What I do',
                        'body' => 'One paragraph on the problem you solve and the kind of client you work best with.',
                    ],
                ],
                [
                    'type' => 'services',
                    'data' => [
                        'heading' => 'Work with me',
                        'services' => [
                            ['name' => 'Audit', 'description' => 'What the client gets and how long it takes.', 'price' => 'from $2,400'],
                            ['name' => 'Build', 'description' => 'A larger engagement with a clear outcome.', 'price' => 'from $6,000'],
                        ],
                    ],
                ],
                [
                    'type' => 'testimonial',
                    'data' => [
                        'quote' => 'A client sentence about the result, not the process.',
                        'author' => 'Client name',
                        'role' => 'Title, Company',
                    ],
                ],
                [
                    'type' => 'links',
                    'data' => [
                        'heading' => 'Get in touch',
                        'links' => [
                            ['label' => 'Book a call', 'url' => 'https://cal.com/'],
                            ['label' => 'Email me', 'url' => 'mailto:you@example.com'],
                        ],
                    ],
                ],
                [
                    'type' => 'skills',
                    'data' => [
                        'heading' => 'Tools I use',
                        'tags' => ['Tool', 'Tool', 'Tool'],
                    ],
                ],
            ],
        ],
    ];

    public static function keys(): array
    {
        return array_keys(self::PRESETS);
    }

    public static function blocks(string $preset): array
    {
        return self::PRESETS[$preset]['blocks'] ?? [];
    }

    public static function meta(): array
    {
        return array_map(
            fn (array $preset) => [
                'label' => $preset['label'],
                'goal' => $preset['goal'],
                'description' => $preset['description'],
                'blocks' => count($preset['blocks']),
            ],
            self::PRESETS,
        );
    }
}
