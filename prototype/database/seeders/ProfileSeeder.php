<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'amir@example.com'],
            ['name' => 'Amiruddin', 'password' => 'password'],
        );

        $profile = Profile::updateOrCreate(
            ['username' => 'amir'],
            [
                'user_id' => $user->id,
                'display_name' => 'Amiruddin',
                'headline' => 'Product engineer · ships interfaces people trust',
                'location' => 'Kuala Lumpur, Malaysia',
                'template' => 'minimal',
                'preset' => 'job_seeker',
                'is_published' => true,
            ],
        );

        $profile->blocks()->delete();

        $blocks = [
            [
                'type' => 'hero',
                'data' => [
                    'heading' => 'Open to senior product engineering roles',
                    'body' => 'Available from December · remote or KL',
                ],
            ],
            [
                'type' => 'bio',
                'data' => [
                    'heading' => 'About',
                    'body' => 'I build the front end of products that have to feel obvious: editors, dashboards and profile builders. Ten years across startups and agencies, most of it spent turning messy requirements into calm interfaces.',
                ],
            ],
            [
                'type' => 'links',
                'data' => [
                    'heading' => 'Find me',
                    'links' => [
                        ['label' => 'GitHub', 'url' => 'https://github.com/salehuddin'],
                        ['label' => 'LinkedIn', 'url' => 'https://linkedin.com/in/example'],
                        ['label' => 'Read the CV (PDF)', 'url' => '#'],
                    ],
                ],
            ],
            [
                'type' => 'experience',
                'data' => [
                    'heading' => 'Experience',
                    'items' => [
                        [
                            'role' => 'Lead Product Engineer',
                            'company' => 'Northwind',
                            'period' => '2022 — now',
                            'summary' => 'Rebuilt the onboarding flow; activation up 34% in two quarters.',
                        ],
                        [
                            'role' => 'Frontend Engineer',
                            'company' => 'Kite Studio',
                            'period' => '2019 — 2022',
                            'summary' => 'Design system used by seven client products.',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'skills',
                'data' => [
                    'heading' => 'Skills',
                    'tags' => ['TypeScript', 'React', 'Laravel', 'Tailwind', 'Design systems', 'Postgres'],
                ],
            ],
            [
                'type' => 'services',
                'data' => [
                    'heading' => 'What I can do for you',
                    'services' => [
                        ['name' => 'Product audit', 'description' => 'Two weeks, one report, prioritized fixes.', 'price' => 'from $2,400'],
                        ['name' => 'Interface build', 'description' => 'Design to shipped front end in your stack.', 'price' => 'from $6,000'],
                    ],
                ],
            ],
            [
                'type' => 'testimonial',
                'data' => [
                    'quote' => 'He shipped in three weeks what our team had been circling for a quarter.',
                    'author' => 'Dana Ruiz',
                    'role' => 'Head of Product, Northwind',
                ],
            ],
        ];

        foreach ($blocks as $position => $block) {
            $profile->blocks()->create($block + [
                'position' => $position,
                'is_visible' => true,
            ]);
        }
    }
}
