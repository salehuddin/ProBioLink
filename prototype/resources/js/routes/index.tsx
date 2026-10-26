import { useQuery } from '@tanstack/react-query';
import { Link, createFileRoute } from '@tanstack/react-router';
import { fetchProfiles } from '../lib/api';

export const Route = createFileRoute('/')({
    loader: ({ context }) =>
        context.queryClient.ensureQueryData({
            queryKey: ['profiles'],
            queryFn: fetchProfiles,
        }),
    component: HomePage,
});

function HomePage() {
    const { data: profiles = [] } = useQuery({
        queryKey: ['profiles'],
        queryFn: fetchProfiles,
    });

    return (
        <div className="space-y-10">
            <header className="space-y-4">
                <p className="text-xs font-medium tracking-[0.25em] text-indigo-300 uppercase">
                    Link-in-bio depth, resume-level detail
                </p>
                <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">
                    Build a profile that gets you hired or booked.
                </h1>
                <p className="max-w-xl text-sm leading-relaxed text-zinc-400">
                    Modular blocks, intent-based presets, and one shareable link.
                    This is the walking skeleton: a public profile renderer and a
                    block editor backed by the Laravel API.
                </p>
            </header>

            <section className="space-y-3">
                <h2 className="text-xs font-medium tracking-[0.2em] text-zinc-500 uppercase">
                    Profiles in this directory
                </h2>

                {profiles.length === 0 ? (
                    <p className="rounded-xl border border-dashed border-white/10 p-6 text-sm text-zinc-500">
                        No profiles yet. Run{' '}
                        <code className="text-zinc-300">
                            php artisan db:seed
                        </code>
                        .
                    </p>
                ) : (
                    <ul className="grid gap-3 sm:grid-cols-2">
                        {profiles.map((profile) => (
                            <li
                                key={profile.username}
                                className="rounded-2xl border border-white/10 bg-white/[0.03] p-5"
                            >
                                <p className="text-sm font-medium">
                                    {profile.display_name}
                                </p>
                                <p className="mt-1 line-clamp-2 text-xs text-zinc-500">
                                    {profile.headline}
                                </p>
                                <div className="mt-4 flex gap-3 text-xs">
                                    <a
                                        href={`/u/${profile.username}`}
                                        className="text-indigo-300 hover:text-indigo-200"
                                    >
                                        View profile
                                    </a>
                                    <Link
                                        to="/editor/$username"
                                        params={{ username: profile.username }}
                                        className="text-zinc-400 hover:text-zinc-200"
                                    >
                                        Edit blocks
                                    </Link>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </section>
        </div>
    );
}
