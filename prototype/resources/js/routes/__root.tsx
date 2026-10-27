import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import {
    Link,
    Outlet,
    createRootRouteWithContext,
    useLocation,
    useRouter,
} from '@tanstack/react-router';
import type { QueryClient } from '@tanstack/react-query';
import { fetchMe, logout } from '../lib/api';

export const Route = createRootRouteWithContext<{ queryClient: QueryClient }>()({
    component: RootLayout,
});

function RootLayout() {
    const queryClient = useQueryClient();
    const router = useRouter();
    const location = useLocation();
    const { data: user } = useQuery({
        queryKey: ['me'],
        queryFn: fetchMe,
        staleTime: 60_000,
        retry: false,
    });

    const signOut = useMutation({
        mutationFn: logout,
        onSuccess: () => {
            queryClient.setQueryData(['me'], null);
            router.navigate({ to: '/' });
        },
    });

    return (
        <div className="mx-auto flex min-h-screen w-full max-w-3xl flex-col px-5 py-10">
            <nav className="mb-10 flex items-center justify-between gap-4 text-sm">
                <Link to="/" className="font-semibold tracking-tight">
                    ProBioLink
                </Link>

                {user ? (
                    <span className="flex items-center gap-3 text-xs text-zinc-500">
                        {user.name}
                        <button
                            type="button"
                            onClick={() => signOut.mutate()}
                            disabled={signOut.isPending}
                            className="rounded-md border border-white/10 px-2 py-1 text-zinc-400 transition hover:border-white/25 hover:text-zinc-200 disabled:opacity-50"
                        >
                            Sign out
                        </button>
                    </span>
                ) : location.pathname === '/login' || location.pathname === '/signup' ? (
                    <span className="text-xs text-zinc-600">Editor access</span>
                ) : (
                    <span className="flex items-center gap-3">
                        <Link
                            to="/signup"
                            className="rounded-md bg-indigo-500 px-2.5 py-1 text-xs font-medium text-white transition hover:bg-indigo-400"
                        >
                            Get started
                        </Link>
                        <Link
                            to="/login"
                            className="text-xs text-zinc-400 transition hover:text-zinc-200"
                        >
                            Sign in
                        </Link>
                    </span>
                )}
            </nav>

            <main className="min-w-0 flex-1">
                <Outlet />
            </main>
        </div>
    );
}
