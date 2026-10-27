import type { BlockType } from '../types';

export const BLOCK_META: Record<
    BlockType,
    { label: string; description: string }
> = {
    hero: {
        label: 'Identity',
        description: 'Name, availability or one-line pitch at the top.',
    },
    bio: { label: 'About', description: 'A short paragraph in your own voice.' },
    links: { label: 'Links', description: 'The places people should go next.' },
    experience: {
        label: 'Experience',
        description: 'Roles, companies and outcomes.',
    },
    skills: { label: 'Skills', description: 'Tools and strengths as tags.' },
    services: {
        label: 'Services',
        description: 'What you sell, with optional pricing.',
    },
    testimonial: { label: 'Testimonial', description: 'A quote that backs it up.' },
};

export const BLOCK_ORDER: BlockType[] = [
    'hero',
    'bio',
    'links',
    'experience',
    'skills',
    'services',
    'testimonial',
];
