export interface PresetMeta {
    id: string;
    label: string;
    goal: string;
    description: string;
    blocks: number;
}

export const PRESETS: PresetMeta[] = [
    {
        id: 'job_seeker',
        label: 'Job seeker',
        goal: 'Get hired',
        description: 'Experience and skills first, links last.',
        blocks: 5,
    },
    {
        id: 'freelancer',
        label: 'Freelancer',
        goal: 'Get booked',
        description: 'Services and proof first, then contact.',
        blocks: 6,
    },
];
