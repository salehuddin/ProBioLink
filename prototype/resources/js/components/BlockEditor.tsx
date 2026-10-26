import { useMutation, useQueryClient } from '@tanstack/react-query';
import {
    Box,
    ChevronDown,
    ChevronUp,
    Eye,
    EyeOff,
    GripVertical,
    Loader2,
    Plus,
    Trash2,
    X,
} from 'lucide-react';
import { useState } from 'react';
import type {
    Block,
    BlockData,
    ExperienceItem,
    Profile,
    ProfileLink,
    ServiceItem,
} from '../types';
import { BLOCK_META, BLOCK_ORDER } from '../lib/blocks';
import { createBlock, deleteBlock, saveProfile } from '../lib/api';
import {
    Field,
    IconButton,
    SmallButton,
    TextArea,
    TextInput,
} from './fields';

function reorder(blocks: Block[], index: number, direction: -1 | 1): Block[] {
    const target = index + direction;

    if (target < 0 || target >= blocks.length) {
        return blocks;
    }

    const next = [...blocks];
    [next[index], next[target]] = [next[target], next[index]];

    return next.map((block, position) => ({ ...block, position }));
}

export function BlockEditor({
    profile,
    onBlocksChange,
}: {
    profile: Profile;
    onBlocksChange?: (blocks: Block[]) => void;
}) {
    const queryClient = useQueryClient();
    const [blocks, setBlocks] = useState<Block[]>(profile.blocks);
    const [expandedId, setExpandedId] = useState<number | null>(null);
    const [pickerOpen, setPickerOpen] = useState(false);
    const [dirty, setDirty] = useState(false);
    const [notice, setNotice] = useState<string | null>(null);
    const [confirmingId, setConfirmingId] = useState<number | null>(null);

    function commit(next: Block[]) {
        setBlocks(next);
        onBlocksChange?.(next);
        setDirty(true);
        setNotice(null);
    }

    function applyProfile(next: Profile) {
        queryClient.setQueryData(['profile', next.username], next);
        setBlocks(next.blocks);
        onBlocksChange?.(next.blocks);
        setDirty(false);
    }

    const save = useMutation({
        mutationFn: () => saveProfile(profile.username, blocks),
        onSuccess: applyProfile,
        onError: (error: Error) => setNotice(error.message),
    });

    const add = useMutation({
        mutationFn: (type: keyof typeof BLOCK_META) =>
            createBlock(profile.username, type),
        onSuccess: (next) => {
            applyProfile(next);
            setPickerOpen(false);
            const added = next.blocks[next.blocks.length - 1];
            setExpandedId(added?.id ?? null);
        },
        onError: (error: Error) => setNotice(error.message),
    });

    const remove = useMutation({
        mutationFn: (blockId: number) => deleteBlock(profile.username, blockId),
        onSuccess: applyProfile,
        onError: (error: Error) => setNotice(error.message),
    });

    function move(index: number, direction: -1 | 1) {
        commit(reorder(blocks, index, direction));
    }

    function toggle(block: Block) {
        commit(
            blocks.map((item) =>
                item.id === block.id
                    ? { ...item, is_visible: !item.is_visible }
                    : item,
            ),
        );
    }

    function updateData(blockId: number, patch: Partial<BlockData>) {
        commit(
            blocks.map((item) =>
                item.id === blockId
                    ? { ...item, data: { ...item.data, ...patch } }
                    : item,
            ),
        );
    }

    const busy = save.isPending || add.isPending || remove.isPending;

    return (
        <div className="space-y-4">
            <ul className="space-y-2">
                {blocks.map((block, index) => (
                    <li
                        key={block.id}
                        className="overflow-hidden rounded-xl border border-white/10 bg-white/[0.03]"
                    >
                        <div className="flex items-center gap-2 px-3 py-2">
                            <GripVertical className="size-4 shrink-0 text-zinc-600" />

                            <button
                                type="button"
                                onClick={() =>
                                    setExpandedId(
                                        expandedId === block.id ? null : block.id,
                                    )
                                }
                                className="min-w-0 flex-1 text-left"
                                aria-expanded={expandedId === block.id}
                            >
                                <span className="block truncate text-sm font-medium">
                                    {BLOCK_META[block.type]?.label ?? block.type}
                                    {!block.is_visible ? (
                                        <span className="ml-2 text-[11px] font-normal text-zinc-500">
                                            hidden
                                        </span>
                                    ) : null}
                                </span>
                                <span className="block truncate text-xs text-zinc-500">
                                    {block.data.heading ??
                                        block.data.quote ??
                                        block.data.body ??
                                        'No content yet'}
                                </span>
                            </button>

                            <div className="flex shrink-0 items-center">
                                <IconButton
                                    label={`Move ${block.type} up`}
                                    onClick={() => move(index, -1)}
                                    disabled={index === 0}
                                >
                                    <ChevronUp className="size-4" />
                                </IconButton>
                                <IconButton
                                    label={`Move ${block.type} down`}
                                    onClick={() => move(index, 1)}
                                    disabled={index === blocks.length - 1}
                                >
                                    <ChevronDown className="size-4" />
                                </IconButton>
                                <IconButton
                                    label={
                                        block.is_visible
                                            ? `Hide ${block.type}`
                                            : `Show ${block.type}`
                                    }
                                    onClick={() => toggle(block)}
                                >
                                    {block.is_visible ? (
                                        <Eye className="size-4" />
                                    ) : (
                                        <EyeOff className="size-4" />
                                    )}
                                </IconButton>
                                <IconButton
                                    label={`Delete ${block.type}`}
                                    danger
                                    onClick={() => setConfirmingId(block.id)}
                                >
                                    <Trash2 className="size-4" />
                                </IconButton>
                            </div>
                        </div>

                        {confirmingId === block.id ? (
                            <div className="flex items-center justify-between gap-3 border-t border-rose-500/20 bg-rose-500/10 px-3 py-2">
                                <p className="text-xs text-rose-200">
                                    Delete this block?
                                </p>
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setConfirmingId(null)}
                                        className="rounded-md px-2 py-1 text-xs text-zinc-300 hover:bg-white/10"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setConfirmingId(null);
                                            remove.mutate(block.id);
                                        }}
                                        className="rounded-md bg-rose-500 px-2.5 py-1 text-xs font-medium text-white hover:bg-rose-400"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        ) : null}

                        {expandedId === block.id ? (
                            <div className="space-y-4 border-t border-white/10 bg-black/20 p-4">
                                <BlockFields
                                    block={block}
                                    onChange={(patch) =>
                                        updateData(block.id, patch)
                                    }
                                />
                            </div>
                        ) : null}
                    </li>
                ))}
            </ul>

            <div className="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    onClick={() => setPickerOpen((open) => !open)}
                    className="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-3 py-1.5 text-xs text-zinc-300 transition hover:border-indigo-400/50 hover:text-indigo-200"
                >
                    <Plus className="size-3.5" />
                    Add block
                </button>

                <p className="ml-auto text-xs text-zinc-500">
                    {notice ? (
                        <span className="text-rose-300">{notice}</span>
                    ) : dirty ? (
                        'Unsaved changes'
                    ) : (
                        'Saved'
                    )}
                </p>

                <button
                    type="button"
                    onClick={() => save.mutate()}
                    disabled={!dirty || busy}
                    className="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-400 disabled:opacity-40"
                >
                    {save.isPending ? (
                        <Loader2 className="size-4 animate-spin" />
                    ) : null}
                    Save changes
                </button>
            </div>

            {pickerOpen ? (
                <div className="rounded-xl border border-white/10 bg-white/[0.03] p-3">
                    <div className="mb-2 flex items-center justify-between">
                        <p className="text-[11px] font-medium tracking-[0.15em] text-zinc-500 uppercase">
                            Add a block
                        </p>
                        <IconButton
                            label="Close block picker"
                            onClick={() => setPickerOpen(false)}
                        >
                            <X className="size-4" />
                        </IconButton>
                    </div>
                    <ul className="grid gap-2 sm:grid-cols-2">
                        {BLOCK_ORDER.map((type) => (
                            <li key={type}>
                                <button
                                    type="button"
                                    disabled={add.isPending}
                                    onClick={() => add.mutate(type)}
                                    className="flex w-full items-start gap-3 rounded-lg border border-white/10 px-3 py-2 text-left transition hover:border-indigo-400/50 hover:bg-indigo-500/10 disabled:opacity-40"
                                >
                                    <Box className="mt-0.5 size-4 shrink-0 text-indigo-300" />
                                    <span>
                                        <span className="block text-sm">
                                            {BLOCK_META[type].label}
                                        </span>
                                        <span className="block text-xs text-zinc-500">
                                            {BLOCK_META[type].description}
                                        </span>
                                    </span>
                                </button>
                            </li>
                        ))}
                    </ul>
                </div>
            ) : null}
        </div>
    );
}

function BlockFields({
    block,
    onChange,
}: {
    block: Block;
    onChange: (patch: Partial<BlockData>) => void;
}) {
    switch (block.type) {
        case 'hero':
        case 'bio':
            return (
                <>
                    <TextInput
                        label="Heading"
                        value={block.data.heading ?? ''}
                        onChange={(heading) => onChange({ heading })}
                    />
                    <TextArea
                        label="Body"
                        value={block.data.body ?? ''}
                        onChange={(body) => onChange({ body })}
                    />
                </>
            );

        case 'links':
            return (
                <>
                    <TextInput
                        label="Heading"
                        value={block.data.heading ?? ''}
                        onChange={(heading) => onChange({ heading })}
                    />
                    <LinkList
                        links={block.data.links ?? []}
                        onChange={(links) => onChange({ links })}
                    />
                </>
            );

        case 'experience':
            return (
                <>
                    <TextInput
                        label="Heading"
                        value={block.data.heading ?? ''}
                        onChange={(heading) => onChange({ heading })}
                    />
                    <ExperienceList
                        items={block.data.items ?? []}
                        onChange={(items) => onChange({ items })}
                    />
                </>
            );

        case 'skills':
            return (
                <>
                    <TextInput
                        label="Heading"
                        value={block.data.heading ?? ''}
                        onChange={(heading) => onChange({ heading })}
                    />
                    <TagList
                        tags={block.data.tags ?? []}
                        onChange={(tags) => onChange({ tags })}
                    />
                </>
            );

        case 'services':
            return (
                <>
                    <TextInput
                        label="Heading"
                        value={block.data.heading ?? ''}
                        onChange={(heading) => onChange({ heading })}
                    />
                    <ServiceList
                        services={block.data.services ?? []}
                        onChange={(services) => onChange({ services })}
                    />
                </>
            );

        case 'testimonial':
            return (
                <>
                    <TextArea
                        label="Quote"
                        value={block.data.quote ?? ''}
                        onChange={(quote) => onChange({ quote })}
                    />
                    <TextInput
                        label="Author"
                        value={block.data.author ?? ''}
                        onChange={(author) => onChange({ author })}
                    />
                    <TextInput
                        label="Role"
                        value={block.data.role ?? ''}
                        onChange={(role) => onChange({ role })}
                    />
                </>
            );

        default:
            return (
                <p className="text-sm text-zinc-500">
                    This block type has no editor yet.
                </p>
            );
    }
}

function LinkList({
    links,
    onChange,
}: {
    links: ProfileLink[];
    onChange: (links: ProfileLink[]) => void;
}) {
    function patch(index: number, changes: Partial<ProfileLink>) {
        onChange(
            links.map((link, position) =>
                position === index ? { ...link, ...changes } : link,
            ),
        );
    }

    return (
        <Field label="Links">
            <ul className="space-y-2">
                {links.map((link, index) => (
                    <li key={index} className="flex items-start gap-2">
                        <div className="grid flex-1 gap-2 sm:grid-cols-[140px_1fr]">
                            <input
                                aria-label={`Link ${index + 1} label`}
                                className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                                value={link.label}
                                placeholder="Label"
                                onChange={(event) =>
                                    patch(index, { label: event.target.value })
                                }
                            />
                            <input
                                aria-label={`Link ${index + 1} URL`}
                                className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                                value={link.url}
                                placeholder="https://"
                                onChange={(event) =>
                                    patch(index, { url: event.target.value })
                                }
                            />
                        </div>
                        <IconButton
                            label={`Remove link ${index + 1}`}
                            danger
                            onClick={() =>
                                onChange(
                                    links.filter(
                                        (_, position) => position !== index,
                                    ),
                                )
                            }
                        >
                            <X className="size-4" />
                        </IconButton>
                    </li>
                ))}
            </ul>
            <div className="mt-2">
                <SmallButton
                    onClick={() =>
                        onChange([...links, { label: '', url: 'https://' }])
                    }
                >
                    + Add link
                </SmallButton>
            </div>
        </Field>
    );
}

function ExperienceList({
    items,
    onChange,
}: {
    items: ExperienceItem[];
    onChange: (items: ExperienceItem[]) => void;
}) {
    function patch(index: number, changes: Partial<ExperienceItem>) {
        onChange(
            items.map((item, position) =>
                position === index ? { ...item, ...changes } : item,
            ),
        );
    }

    return (
        <Field label="Roles">
            <ul className="space-y-3">
                {items.map((item, index) => (
                    <li
                        key={index}
                        className="space-y-2 rounded-lg border border-white/10 p-3"
                    >
                        <div className="grid gap-2 sm:grid-cols-2">
                            <input
                                aria-label={`Role ${index + 1}`}
                                className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                                value={item.role}
                                placeholder="Role"
                                onChange={(event) =>
                                    patch(index, { role: event.target.value })
                                }
                            />
                            <input
                                aria-label={`Company ${index + 1}`}
                                className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                                value={item.company}
                                placeholder="Company"
                                onChange={(event) =>
                                    patch(index, {
                                        company: event.target.value,
                                    })
                                }
                            />
                        </div>
                        <input
                            aria-label={`Period ${index + 1}`}
                            className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                            value={item.period}
                            placeholder="2022 — now"
                            onChange={(event) =>
                                patch(index, { period: event.target.value })
                            }
                        />
                        <textarea
                            aria-label={`Summary ${index + 1}`}
                            rows={2}
                            className="w-full resize-y rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                            value={item.summary ?? ''}
                            placeholder="One measurable outcome."
                            onChange={(event) =>
                                patch(index, { summary: event.target.value })
                            }
                        />
                        <div className="text-right">
                            <IconButton
                                label={`Remove role ${index + 1}`}
                                danger
                                onClick={() =>
                                    onChange(
                                        items.filter(
                                            (_, position) => position !== index,
                                        ),
                                    )
                                }
                            >
                                <Trash2 className="size-4" />
                            </IconButton>
                        </div>
                    </li>
                ))}
            </ul>
            <div className="mt-2">
                <SmallButton
                    onClick={() =>
                        onChange([
                            ...items,
                            { role: '', company: '', period: '', summary: '' },
                        ])
                    }
                >
                    + Add role
                </SmallButton>
            </div>
        </Field>
    );
}

function ServiceList({
    services,
    onChange,
}: {
    services: ServiceItem[];
    onChange: (services: ServiceItem[]) => void;
}) {
    function patch(index: number, changes: Partial<ServiceItem>) {
        onChange(
            services.map((service, position) =>
                position === index ? { ...service, ...changes } : service,
            ),
        );
    }

    return (
        <Field label="Services">
            <ul className="space-y-3">
                {services.map((service, index) => (
                    <li
                        key={index}
                        className="space-y-2 rounded-lg border border-white/10 p-3"
                    >
                        <div className="grid gap-2 sm:grid-cols-[1fr_120px]">
                            <input
                                aria-label={`Service ${index + 1} name`}
                                className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                                value={service.name}
                                placeholder="Service name"
                                onChange={(event) =>
                                    patch(index, {
                                        name: event.target.value,
                                    })
                                }
                            />
                            <input
                                aria-label={`Service ${index + 1} price`}
                                className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                                value={service.price ?? ''}
                                placeholder="from $2,400"
                                onChange={(event) =>
                                    patch(index, {
                                        price: event.target.value,
                                    })
                                }
                            />
                        </div>
                        <textarea
                            aria-label={`Service ${index + 1} description`}
                            rows={2}
                            className="w-full resize-y rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                            value={service.description}
                            placeholder="What the client gets."
                            onChange={(event) =>
                                patch(index, {
                                    description: event.target.value,
                                })
                            }
                        />
                        <div className="text-right">
                            <IconButton
                                label={`Remove service ${index + 1}`}
                                danger
                                onClick={() =>
                                    onChange(
                                        services.filter(
                                            (_, position) => position !== index,
                                        ),
                                    )
                                }
                            >
                                <Trash2 className="size-4" />
                            </IconButton>
                        </div>
                    </li>
                ))}
            </ul>
            <div className="mt-2">
                <SmallButton
                    onClick={() =>
                        onChange([
                            ...services,
                            { name: '', description: '', price: '' },
                        ])
                    }
                >
                    + Add service
                </SmallButton>
            </div>
        </Field>
    );
}

function TagList({
    tags,
    onChange,
}: {
    tags: string[];
    onChange: (tags: string[]) => void;
}) {
    const [draft, setDraft] = useState('');

    function add() {
        const value = draft.trim();

        if (value && !tags.includes(value)) {
            onChange([...tags, value]);
        }

        setDraft('');
    }

    return (
        <Field label="Tags">
            <ul className="flex flex-wrap gap-2">
                {tags.map((tag) => (
                    <li
                        key={tag}
                        className="inline-flex items-center gap-1 rounded-full border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs"
                    >
                        {tag}
                        <button
                            type="button"
                            aria-label={`Remove ${tag}`}
                            onClick={() =>
                                onChange(tags.filter((item) => item !== tag))
                            }
                            className="text-zinc-500 hover:text-rose-300"
                        >
                            <X className="size-3" />
                        </button>
                    </li>
                ))}
            </ul>
            <div className="mt-2 flex gap-2">
                <input
                    aria-label="New tag"
                    className="w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm outline-none focus:border-indigo-400/70"
                    value={draft}
                    placeholder="Add a skill and press Enter"
                    onChange={(event) => setDraft(event.target.value)}
                    onKeyDown={(event) => {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            add();
                        }
                    }}
                />
                <button
                    type="button"
                    onClick={add}
                    className="rounded-lg border border-white/10 px-3 text-xs text-zinc-300 transition hover:border-indigo-400/50"
                >
                    Add
                </button>
            </div>
        </Field>
    );
}
