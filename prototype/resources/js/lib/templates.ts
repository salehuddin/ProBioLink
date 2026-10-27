import rawThemes from '../../themes.json';

export interface TemplateTheme {
    label: string;
    shell: string;
    card: string;
    labelText: string;
    bodyText: string;
    mutedText: string;
    accentText: string;
    accentSurface: string;
    linkCard: string;
    tag: string;
    avatar: string;
    hairline: string;
}

/**
 * Theme classes live in resources/themes.json so the Blade renderer and this
 * renderer share one source of truth.
 */
export const TEMPLATES = rawThemes as Record<string, TemplateTheme>;

export const TEMPLATE_ORDER = ['minimal', 'professional', 'bold'];

export function themeFor(template: string): TemplateTheme {
    return TEMPLATES[template] ?? TEMPLATES.minimal;
}
