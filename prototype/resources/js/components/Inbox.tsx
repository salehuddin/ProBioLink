import { useQuery } from '@tanstack/react-query';
import { Inbox as InboxIcon } from 'lucide-react';
import { fetchInquiries } from '../lib/api';

export function Inbox({ username }: { username: string }) {
    const { data: inquiries = [], isLoading } = useQuery({
        queryKey: ['inquiries', username],
        queryFn: () => fetchInquiries(username),
    });

    return (
        <section className="space-y-3 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div className="flex items-baseline justify-between">
                <p className="text-[11px] font-medium tracking-[0.15em] text-zinc-500 uppercase">
                    Inbox
                </p>
                <span className="text-[11px] text-zinc-600">
                    {isLoading ? '…' : `${inquiries.length} received`}
                </span>
            </div>

            {isLoading ? null : inquiries.length === 0 ? (
                <div className="flex items-start gap-3 rounded-lg border border-dashed border-white/10 p-4">
                    <InboxIcon className="mt-0.5 size-4 shrink-0 text-zinc-600" />
                    <p className="text-xs text-zinc-500">
                        No inquiries yet. Share your profile link — anything sent
                        through the contact form lands here.
                    </p>
                </div>
            ) : (
                <ul className="space-y-2">
                    {inquiries.map((inquiry) => (
                        <li
                            key={inquiry.id}
                            className="rounded-lg border border-white/10 p-3"
                        >
                            <div className="flex flex-wrap items-baseline justify-between gap-2">
                                <p className="text-sm font-medium">
                                    {inquiry.name}
                                    <span className="ml-2 text-xs font-normal text-zinc-500">
                                        {inquiry.email}
                                    </span>
                                </p>
                                <span className="text-[11px] text-zinc-600">
                                    {new Date(
                                        inquiry.created_at,
                                    ).toLocaleDateString()}
                                </span>
                            </div>
                            {inquiry.service ? (
                                <p className="mt-1 text-xs text-indigo-300">
                                    {inquiry.service}
                                </p>
                            ) : null}
                            <p className="mt-1 text-sm text-zinc-300">
                                {inquiry.message}
                            </p>
                            <a
                                href={`mailto:${inquiry.email}`}
                                className="mt-2 inline-block text-xs text-zinc-500 underline-offset-4 hover:text-zinc-300 hover:underline"
                            >
                                Reply by email
                            </a>
                        </li>
                    ))}
                </ul>
            )}
        </section>
    );
}
