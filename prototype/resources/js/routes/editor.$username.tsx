import { useQueryClient, useSuspenseQuery } from '@tanstack/react-query';
import { createFileRoute, redirect } from '@tanstack/react-router';
import { useState } from 'react';
import { BlockEditor } from '../components/BlockEditor';
import { PresetPicker } from '../components/PresetPicker';
import { ProfileDetails } from '../components/ProfileDetails';
import { ProfileView } from '../components/ProfileView';
import { TemplatePicker } from '../components/TemplatePicker';
import { fetchMe, fetchProfile } from '../lib/api';
import type { Block } from '../types';

export const Route = createFileRoute('/editor/$username')({
    beforeLoad: async ({ context, location }) => {
        const user = await context.queryClient.ensureQueryData({
            queryKey: ['me'],
            queryFn: fetchMe,
        });

        if (!user) {
            throw redirect({
                to: '/login',
                search: { redirect: location.href },
            });
        }
    },
    loader: ({ context, params }) =>
        context.queryClient.ensureQueryData({
            queryKey: ['profile', params.username],
            queryFn: () => fetchProfile(params.username),
        }),
    component: EditorPage,
});

function EditorPage() {
    const { username } = Route.useParams();
    const { data: profile } = useSuspenseQuery({
        queryKey: ['profile', username],
        queryFn: () => fetchProfile(username),
    });
    const [previewBlocks, setPreviewBlocks] = useState<Block[]>(profile.blocks);
    const [revision, setRevision] = useState(0);
    const queryClient = useQueryClient();

    function adopt(next: Profile) {
        queryClient.setQueryData(['profile', next.username], next);
        setPreviewBlocks(next.blocks);
    }

    return (
        <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
            <section className="min-w-0 space-y-6">
                <header className="space-y-2">
                    <p className="text-xs font-medium tracking-[0.2em] text-indigo-300 uppercase">
                        Editor
                    </p>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        {profile.display_name}
                    </h1>
                    <p className="text-sm text-zinc-400">
                        Edit any block, reorder it, or hide it. Changes save
                        together, and the preview follows before you save.
                    </p>
                </header>

                <ProfileDetails
                    profile={profile}
                    onSaved={(next) => {
                        queryClient.setQueryData(['profile', next.username], next);
                        setPreviewBlocks(next.blocks);
                    }}
                />

                <TemplatePicker profile={profile} onSaved={adopt} />

                <PresetPicker
                    profile={profile}
                    onApplied={(next) => {
                        adopt(next);
                        setRevision((value) => value + 1);
                    }}
                />

                <BlockEditor
                    key={`${profile.username}-${revision}`}
                    profile={profile}
                    onBlocksChange={setPreviewBlocks}
                />

                <div className="flex flex-wrap items-center gap-4">
                    <a
                        href={`/u/${username}`}
                        className="text-xs text-zinc-500 underline-offset-4 hover:text-zinc-300 hover:underline"
                    >
                        Open public profile
                    </a>
                    <a
                        href={`/u/${username}/cv.pdf`}
                        className="text-xs text-zinc-500 underline-offset-4 hover:text-zinc-300 hover:underline"
                    >
                        Download CV (PDF)
                    </a>
                </div>
            </section>

            <aside className="h-fit min-w-0 rounded-2xl border border-white/10 bg-white/[0.02] p-4">
                <p className="mb-4 text-xs font-medium tracking-[0.2em] text-zinc-500 uppercase">
                    Live preview
                </p>
                <div className="origin-top scale-[0.92]">
                    <ProfileView
                        profile={{ ...profile, blocks: previewBlocks }}
                    />
                </div>
            </aside>
        </div>
    );
}
