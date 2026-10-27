import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Link, createFileRoute, useNavigate } from '@tanstack/react-router';
import { Loader2 } from 'lucide-react';
import { useState } from 'react';
import { register } from '../lib/api';
import { Field } from '../components/fields';
import { PRESETS } from '../lib/presets';

export const Route = createFileRoute('/signup')({
    component: SignupPage,
});

function SignupPage() {
    const navigate = useNavigate();
    const queryClient = useQueryClient();
    const [form, setForm] = useState({
        name: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
        preset: 'job_seeker',
    });

    function change(key: keyof typeof form, value: string) {
        setForm((current) => ({ ...current, [key]: value }));
    }

    const mutation = useMutation({
        mutationFn: () => register(form),
        onSuccess: ({ user, profile }) => {
            queryClient.setQueryData(['me'], user);
            queryClient.setQueryData(['profile', profile.username], profile);
            navigate({
                to: '/editor/$username',
                params: { username: profile.username },
            });
        },
    });

    const host =
        typeof window === 'undefined' ? 'probiolink.test' : window.location.host;

    return (
        <div className="mx-auto w-full max-w-lg space-y-6">
            <header className="space-y-2">
                <p className="text-xs font-medium tracking-[0.2em] text-indigo-300 uppercase">
                    Start free
                </p>
                <h1 className="text-2xl font-semibold tracking-tight">
                    Build your profile
                </h1>
                <p className="text-sm text-zinc-400">
                    Pick what you are here for. We lay out the blocks; you edit
                    the words.
                </p>
            </header>

            <form
                className="space-y-4"
                onSubmit={(event) => {
                    event.preventDefault();
                    mutation.mutate();
                }}
            >
                <div className="grid gap-4 sm:grid-cols-2">
                    <Field label="Your name">
                        <input
                            aria-label="Your name"
                            className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                            value={form.name}
                            onChange={(event) => change('name', event.target.value)}
                        />
                    </Field>

                    <Field label="Username" hint={`${host}/u/${form.username || 'you'}`}>
                        <input
                            aria-label="Username"
                            className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                            value={form.username}
                            placeholder="amir"
                            onChange={(event) =>
                                change(
                                    'username',
                                    event.target.value
                                        .toLowerCase()
                                        .replace(/[^a-z0-9_-]/g, ''),
                                )
                            }
                        />
                    </Field>
                </div>

                <Field label="Email">
                    <input
                        type="email"
                        aria-label="Email"
                        autoComplete="username"
                        className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                        value={form.email}
                        onChange={(event) => change('email', event.target.value)}
                    />
                </Field>

                <div className="grid gap-4 sm:grid-cols-2">
                    <Field label="Password">
                        <input
                            type="password"
                            aria-label="Password"
                            autoComplete="new-password"
                            className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                            value={form.password}
                            onChange={(event) =>
                                change('password', event.target.value)
                            }
                        />
                    </Field>

                    <Field label="Confirm password">
                        <input
                            type="password"
                            aria-label="Confirm password"
                            autoComplete="new-password"
                            className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                            value={form.password_confirmation}
                            onChange={(event) =>
                                change('password_confirmation', event.target.value)
                            }
                        />
                    </Field>
                </div>

                <div className="space-y-2">
                    <p className="text-[11px] font-medium tracking-[0.15em] text-zinc-500 uppercase">
                        What are you here for?
                    </p>
                    <div className="grid gap-2 sm:grid-cols-2">
                        {PRESETS.map((preset) => {
                            const active = form.preset === preset.id;

                            return (
                                <button
                                    key={preset.id}
                                    type="button"
                                    aria-pressed={active}
                                    onClick={() => change('preset', preset.id)}
                                    className={`rounded-xl border px-3 py-3 text-left transition ${
                                        active
                                            ? 'border-indigo-400/60 bg-indigo-500/10'
                                            : 'border-white/10 hover:border-white/25'
                                    }`}
                                >
                                    <span className="block text-sm font-medium">
                                        {preset.label}
                                    </span>
                                    <span className="block text-xs text-zinc-500">
                                        {preset.goal} · {preset.description}
                                    </span>
                                </button>
                            );
                        })}
                    </div>
                </div>

                {mutation.isError ? (
                    <p className="text-xs text-rose-300">
                        {mutation.error.message}
                    </p>
                ) : null}

                <button
                    type="submit"
                    disabled={mutation.isPending}
                    className="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-400 disabled:opacity-40"
                >
                    {mutation.isPending ? (
                        <Loader2 className="size-4 animate-spin" />
                    ) : null}
                    Create my profile
                </button>
            </form>

            <p className="text-xs text-zinc-500">
                Already have an account?{' '}
                <Link to="/login" className="text-indigo-300 hover:text-indigo-200">
                    Sign in
                </Link>
            </p>
        </div>
    );
}
