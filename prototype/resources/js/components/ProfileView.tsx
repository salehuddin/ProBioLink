import type { Block, Profile } from '../types';
import { themeFor, type TemplateTheme } from '../lib/templates';

function Avatar({ profile }: { profile: Profile }) {
    const theme = themeFor(profile.template);
    const initials = profile.display_name
        .split(' ')
        .map((part) => part[0])
        .slice(0, 2)
        .join('');

    if (profile.avatar_url) {
        return (
            <img
                src={profile.avatar_url}
                alt={profile.display_name}
                className="size-20 rounded-full object-cover ring-2 ring-black/10"
            />
        );
    }

    return (
        <div
            className={`grid size-20 place-items-center rounded-full bg-gradient-to-br text-2xl font-semibold ring-2 ring-black/10 ${theme.avatar}`}
        >
            {initials}
        </div>
    );
}

function BlockBody({ block, theme }: { block: Block; theme: TemplateTheme }) {
    switch (block.type) {
        case 'hero':
            return (
                <div className="flex items-center gap-4">
                    <div
                        className={`grid size-14 shrink-0 place-items-center rounded-full text-lg font-semibold ${theme.accentSurface}`}
                    >
                        {block.data.heading?.[0] ?? '?'}
                    </div>
                    <div className="min-w-0">
                        <h2 className="text-lg font-semibold sm:text-xl">
                            {block.data.heading}
                        </h2>
                        <p className={`text-sm ${theme.mutedText}`}>
                            {block.data.body}
                        </p>
                    </div>
                </div>
            );

        case 'bio':
            return (
                <div className="space-y-2">
                    <h3
                        className={`text-xs font-medium tracking-[0.2em] uppercase ${theme.labelText}`}
                    >
                        {block.data.heading ?? 'About'}
                    </h3>
                    <p className={`text-sm leading-relaxed ${theme.bodyText}`}>
                        {block.data.body}
                    </p>
                </div>
            );

        case 'links':
            return (
                <div className="space-y-3">
                    <h3
                        className={`text-xs font-medium tracking-[0.2em] uppercase ${theme.labelText}`}
                    >
                        {block.data.heading ?? 'Links'}
                    </h3>
                    <ul className="space-y-2">
                        {(block.data.links ?? []).map((link) => (
                            <li key={`${link.label}-${link.url}`}>
                                <a
                                    href={link.url}
                                    target="_blank"
                                    rel="noreferrer noopener"
                                    className={`block ${theme.linkCard}`}
                                >
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            );

        case 'experience':
            return (
                <div className="space-y-4">
                    <h3
                        className={`text-xs font-medium tracking-[0.2em] uppercase ${theme.labelText}`}
                    >
                        {block.data.heading ?? 'Experience'}
                    </h3>
                    <ol className="space-y-4">
                        {(block.data.items ?? []).map((item) => (
                            <li
                                key={`${item.company}-${item.role}`}
                                className={`border-l pl-4 ${theme.hairline}`}
                            >
                                <p className="text-sm font-medium">
                                    {item.role} · {item.company}
                                </p>
                                <p className={`text-xs ${theme.mutedText}`}>
                                    {item.period}
                                </p>
                                {item.summary ? (
                                    <p className={`mt-1 text-sm ${theme.bodyText}`}>
                                        {item.summary}
                                    </p>
                                ) : null}
                            </li>
                        ))}
                    </ol>
                </div>
            );

        case 'skills':
            return (
                <div className="space-y-3">
                    <h3
                        className={`text-xs font-medium tracking-[0.2em] uppercase ${theme.labelText}`}
                    >
                        {block.data.heading ?? 'Skills'}
                    </h3>
                    <ul className="flex flex-wrap gap-2">
                        {(block.data.tags ?? []).map((tag) => (
                            <li key={tag} className={theme.tag}>
                                {tag}
                            </li>
                        ))}
                    </ul>
                </div>
            );

        case 'services':
            return (
                <div className="space-y-4">
                    <h3
                        className={`text-xs font-medium tracking-[0.2em] uppercase ${theme.labelText}`}
                    >
                        {block.data.heading ?? 'Services'}
                    </h3>
                    <ul className="grid gap-3 sm:grid-cols-2">
                        {(block.data.services ?? []).map((service) => (
                            <li
                                key={service.name}
                                className={`rounded-xl border p-4 ${theme.hairline}`}
                            >
                                <p className="text-sm font-medium">
                                    {service.name}
                                </p>
                                <p className={`mt-1 text-xs ${theme.mutedText}`}>
                                    {service.description}
                                </p>
                                {service.price ? (
                                    <p
                                        className={`mt-2 text-xs font-medium ${theme.accentText}`}
                                    >
                                        {service.price}
                                    </p>
                                ) : null}
                            </li>
                        ))}
                    </ul>
                </div>
            );

        case 'testimonial':
            return (
                <figure className="space-y-3">
                    <blockquote className={`text-sm leading-relaxed ${theme.bodyText}`}>
                        “{block.data.quote}”
                    </blockquote>
                    <figcaption className={`text-xs ${theme.mutedText}`}>
                        {block.data.author}
                        {block.data.role ? ` — ${block.data.role}` : ''}
                    </figcaption>
                </figure>
            );

        default:
            return (
                <p className={`text-sm ${theme.mutedText}`}>
                    No renderer for “{block.type}” yet.
                </p>
            );
    }
}

export function ProfileView({ profile }: { profile: Profile }) {
    const theme = themeFor(profile.template);
    const visible = profile.blocks.filter((block) => block.is_visible);

    return (
        <article
            className={`mx-auto w-full max-w-xl space-y-4 ${theme.shell}`}
        >
            <header className="flex flex-col items-center gap-4 pb-2 text-center">
                <Avatar profile={profile} />
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        {profile.display_name}
                    </h1>
                    {profile.headline ? (
                        <p className={`mt-1 text-sm ${theme.mutedText}`}>
                            {profile.headline}
                        </p>
                    ) : null}
                    {profile.location ? (
                        <p className={`mt-1 text-xs ${theme.mutedText}`}>
                            {profile.location}
                        </p>
                    ) : null}
                </div>
            </header>

            {visible.map((block) => (
                <section key={block.id} className={theme.card}>
                    <BlockBody block={block} theme={theme} />
                </section>
            ))}

            <footer className={`pt-6 text-center text-xs ${theme.mutedText}`}>
                Made with ProBioLink
            </footer>
        </article>
    );
}
