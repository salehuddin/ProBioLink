export type BlockType =
    | 'hero'
    | 'bio'
    | 'links'
    | 'experience'
    | 'skills'
    | 'services'
    | 'testimonial';

export interface ProfileLink {
    label: string;
    url: string;
    icon?: string;
}

export interface ExperienceItem {
    role: string;
    company: string;
    period: string;
    summary?: string;
}

export interface ServiceItem {
    name: string;
    description: string;
    price?: string;
}

export interface BlockData {
    heading?: string;
    body?: string;
    links?: ProfileLink[];
    items?: ExperienceItem[];
    tags?: string[];
    services?: ServiceItem[];
    quote?: string;
    author?: string;
    role?: string;
}

export interface Block {
    id: number;
    type: BlockType;
    position: number;
    is_visible: boolean;
    data: BlockData;
}

export interface Profile {
    username: string;
    display_name: string;
    headline: string | null;
    location: string | null;
    avatar_url: string | null;
    template: string;
    preset: string;
    blocks: Block[];
}

export interface BlockLayoutChange {
    id: number;
    position: number;
    is_visible: boolean;
}
