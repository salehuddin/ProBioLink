import { useMutation } from '@tanstack/react-query';
import { Loader2, Sparkles } from 'lucide-react';
import { useState } from 'react';
import type { Profile } from '../types';
import { applyPreset } from '../lib/api';
import { PRESETS } from '../lib/presets';

export function PresetPicker({
    profile,
    onApplied,
}: {
    profile: Profile;
    onApplied: (profile: Profile) => void;
}) {
    const [confirming, setConfirming] = useState<string | null>(null);

    const mutation = useMutation({
        mutationFn: (preset: string) => applyPreset(profile.username, preset),
        onSuccess: (next) => {
            setConfirming(null);
            onApplied(next);
        },
    });

    return (
        <section className="space-y-3 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div className="flex items-baseline justify-between">
                <p className="text-[11px] font-medium tracking-[0.15em] text-zinc-500 uppercase">
                    Starting point
                </p>
                <span className="text-[11px] text-zinc-600">
                    current: {profile.preset.replace('_', ' ')}
                </span>
            </div>

            <ul className="grid gap-2 sm:grid-cols-2">
                {PRESETS.map((preset) => {
                    const active = profile.preset === preset.id;

                    return (
                        <li key={preset.id}>
                            <button
                                type="button"
                                aria-pressed={active}
                                onClick={() => setConfirming(preset.id)}
                                className={`w-full rounded-xl border px-3 py-3 text-left transition ${
                                    active
                                        ? 'border-indigo-400/60 bg-indigo-500/10'
                                        : 'border-white/10 hover:border-white/25'
                                }`}
                            >
                                <span className="flex items-center gap-2 text-sm font-medium">
                                    <Sparkles className="size-3.5 text-indigo-300" />
                                    {preset.label}
                                </span>
                                <span className="mt-1 block text-xs text-zinc-500">
                                    {preset.goal} · {preset.description}
                                </span>
                            </button>
                        </li>
                    );
                })}
            </ul>

            {confirming ? (
                <div className="flex items-center justify-between gap-3 rounded-lg border border-amber-400/30 bg-amber-400/10 px-3 py-2">
                    <p className="text-xs text-amber-100">
                        Replace all {profile.blocks.length} blocks with the{' '}
                        {PRESETS.find((preset) => preset.id === confirming)?.label}{' '}
                        preset?
                    </p>
                    <div className="flex shrink-0 items-center gap-2">
                        <button
                            type="button"
                            onClick={() => setConfirming(null)}
                            className="rounded-md px-2 py-1 text-xs text-zinc-300 hover:bg-white/10"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            onClick={() => mutation.mutate(confirming)}
                            disabled={mutation.isPending}
                            className="inline-flex items-center gap-1.5 rounded-md bg-amber-400 px-2.5 py-1 text-xs font-medium text-amber-950 hover:bg-amber-300 disabled:opacity-50"
                        >
                            {mutation.isPending ? (
                                <Loader2 className="size-3 animate-spin" />
                            ) : null}
                            Replace
                        </button>
                    </div>
                </div>
            ) : null}

            {mutation.isError ? (
                <p className="text-xs text-rose-300">
                    {mutation.error.message}
                </p>
            ) : null}
        </section>
    );
}
