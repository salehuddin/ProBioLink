import { useMutation } from '@tanstack/react-query';
import { Loader2 } from 'lucide-react';
import { useState } from 'react';
import type { Profile } from '../types';
import { saveProfileDetails } from '../lib/api';
import { TextInput } from './fields';

export function ProfileDetails({
    profile,
    onSaved,
}: {
    profile: Profile;
    onSaved: (profile: Profile) => void;
}) {
    const [form, setForm] = useState({
        display_name: profile.display_name,
        headline: profile.headline ?? '',
        location: profile.location ?? '',
    });
    const [dirty, setDirty] = useState(false);

    function change(key: keyof typeof form, value: string) {
        setForm((current) => ({ ...current, [key]: value }));
        setDirty(true);
    }

    const mutation = useMutation({
        mutationFn: () =>
            saveProfileDetails(profile.username, {
                display_name: form.display_name.trim() || profile.username,
                headline: form.headline.trim() || null,
                location: form.location.trim() || null,
            }),
        onSuccess: (next) => {
            onSaved(next);
            setDirty(false);
        },
    });

    return (
        <section className="space-y-3 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div className="flex items-baseline justify-between">
                <p className="text-[11px] font-medium tracking-[0.15em] text-zinc-500 uppercase">
                    Profile details
                </p>
                {mutation.isError ? (
                    <span className="text-xs text-rose-300">
                        {mutation.error.message}
                    </span>
                ) : null}
            </div>

            <TextInput
                label="Display name"
                value={form.display_name}
                onChange={(value) => change('display_name', value)}
            />
            <TextInput
                label="Headline"
                value={form.headline}
                placeholder="Product engineer · ships interfaces people trust"
                onChange={(value) => change('headline', value)}
            />
            <TextInput
                label="Location"
                value={form.location}
                placeholder="Kuala Lumpur, Malaysia"
                onChange={(value) => change('location', value)}
            />

            <div className="flex items-center justify-end gap-3">
                <span className="text-xs text-zinc-500">
                    {dirty ? 'Unsaved' : 'Saved'}
                </span>
                <button
                    type="button"
                    onClick={() => mutation.mutate()}
                    disabled={!dirty || mutation.isPending}
                    className="inline-flex items-center gap-2 rounded-lg border border-white/10 px-3 py-1.5 text-xs text-zinc-200 transition hover:border-indigo-400/50 disabled:opacity-40"
                >
                    {mutation.isPending ? (
                        <Loader2 className="size-3.5 animate-spin" />
                    ) : null}
                    Save details
                </button>
            </div>
        </section>
    );
}
