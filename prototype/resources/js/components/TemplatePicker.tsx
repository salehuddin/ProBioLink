import { useMutation } from '@tanstack/react-query';
import { Check, Loader2 } from 'lucide-react';
import type { Profile } from '../types';
import { saveProfileDetails } from '../lib/api';
import { TEMPLATE_ORDER, TEMPLATES } from '../lib/templates';

export function TemplatePicker({
    profile,
    onSaved,
}: {
    profile: Profile;
    onSaved: (profile: Profile) => void;
}) {
    const mutation = useMutation({
        mutationFn: (template: string) =>
            saveProfileDetails(profile.username, {
                display_name: profile.display_name,
                headline: profile.headline,
                location: profile.location,
                template,
            }),
        onSuccess: onSaved,
    });

    return (
        <section className="space-y-3 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div className="flex items-baseline justify-between">
                <p className="text-[11px] font-medium tracking-[0.15em] text-zinc-500 uppercase">
                    Template
                </p>
                {mutation.isPending ? (
                    <Loader2 className="size-3.5 animate-spin text-zinc-500" />
                ) : null}
            </div>

            <div className="grid grid-cols-3 gap-2">
                {TEMPLATE_ORDER.map((key) => {
                    const active = profile.template === key;
                    const theme = TEMPLATES[key];

                    return (
                        <button
                            key={key}
                            type="button"
                            aria-pressed={active}
                            onClick={() => mutation.mutate(key)}
                            disabled={mutation.isPending}
                            className={`relative rounded-xl border px-3 py-4 text-left text-xs transition ${
                                active
                                    ? 'border-indigo-400/70 bg-indigo-500/10'
                                    : 'border-white/10 hover:border-white/25'
                            }`}
                        >
                            <span className="block text-sm font-medium">
                                {theme.label}
                            </span>
                            <span className="mt-1 block text-[11px] text-zinc-500">
                                {key === 'minimal'
                                    ? 'Text first, fast'
                                    : key === 'professional'
                                      ? 'Light, corporate'
                                      : 'High contrast'}
                            </span>
                            {active ? (
                                <Check className="absolute top-2 right-2 size-3.5 text-indigo-300" />
                            ) : null}
                        </button>
                    );
                })}
            </div>
        </section>
    );
}
