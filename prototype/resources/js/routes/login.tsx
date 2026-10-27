import { useMutation, useQueryClient } from '@tanstack/react-query';
import { createFileRoute, useNavigate } from '@tanstack/react-router';
import { Loader2 } from 'lucide-react';
import { useState } from 'react';
import { login } from '../lib/api';
import { Field } from '../components/fields';

export const Route = createFileRoute('/login')({
    validateSearch: (search: Record<string, unknown>) => ({
        redirect: typeof search.redirect === 'string' ? search.redirect : undefined,
    }),
    component: LoginPage,
});

function LoginPage() {
    const { redirect } = Route.useSearch();
    const navigate = useNavigate();
    const queryClient = useQueryClient();
    const [email, setEmail] = useState('amir@example.com');
    const [password, setPassword] = useState('');

    const mutation = useMutation({
        mutationFn: () => login(email, password),
        onSuccess: (user) => {
            queryClient.setQueryData(['me'], user);

            if (redirect) {
                window.location.assign(redirect);
                return;
            }

            navigate({ to: '/' });
        },
    });

    return (
        <div className="mx-auto w-full max-w-sm space-y-6">
            <header className="space-y-2">
                <p className="text-xs font-medium tracking-[0.2em] text-indigo-300 uppercase">
                    Editor access
                </p>
                <h1 className="text-2xl font-semibold tracking-tight">
                    Sign in
                </h1>
                <p className="text-sm text-zinc-400">
                    The editor writes to the API, so it needs an account. Public
                    profiles stay open to everyone.
                </p>
            </header>

            <form
                className="space-y-4"
                onSubmit={(event) => {
                    event.preventDefault();
                    mutation.mutate();
                }}
            >
                <Field label="Email">
                    <input
                        type="email"
                        aria-label="Email"
                        autoComplete="username"
                        className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                        value={email}
                        onChange={(event) => setEmail(event.target.value)}
                    />
                </Field>

                <Field label="Password">
                    <input
                        type="password"
                        aria-label="Password"
                        autoComplete="current-password"
                        className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                        value={password}
                        onChange={(event) => setPassword(event.target.value)}
                    />
                </Field>

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
                    Sign in
                </button>
            </form>

            <p className="text-xs text-zinc-500">
                No profile yet?{' '}
                <Link to="/signup" className="text-indigo-300 hover:text-indigo-200">
                    Create one in a minute
                </Link>
            </p>

            <p className="text-xs text-zinc-600">
                Seeded demo account: amir@example.com / password
            </p>
        </div>
    );
}
