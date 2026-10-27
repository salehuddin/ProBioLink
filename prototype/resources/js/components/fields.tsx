import type { ReactNode } from 'react';

const inputClass =
    'w-full rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm text-zinc-100 outline-none transition placeholder:text-zinc-600 focus:border-indigo-400/70 focus:bg-white/[0.06]';

export function Field({
    label,
    children,
    hint,
}: {
    label: string;
    children: ReactNode;
    hint?: string;
}) {
    return (
        <label className="block space-y-1.5">
            <span className="text-[11px] font-medium tracking-[0.15em] text-zinc-500 uppercase">
                {label}
            </span>
            {children}
            {hint ? <span className="block text-xs text-zinc-600">{hint}</span> : null}
        </label>
    );
}

export function TextInput({
    label,
    value,
    onChange,
    placeholder,
}: {
    label: string;
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
}) {
    return (
        <Field label={label}>
            <input
                type="text"
                className={inputClass}
                value={value}
                placeholder={placeholder}
                onChange={(event) => onChange(event.target.value)}
            />
        </Field>
    );
}

export function TextArea({
    label,
    value,
    onChange,
    placeholder,
    rows = 3,
}: {
    label: string;
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    rows?: number;
}) {
    return (
        <Field label={label}>
            <textarea
                className={`${inputClass} resize-y leading-relaxed`}
                rows={rows}
                value={value}
                placeholder={placeholder}
                onChange={(event) => onChange(event.target.value)}
            />
        </Field>
    );
}

export function IconButton({
    label,
    onClick,
    disabled,
    danger,
    children,
}: {
    label: string;
    onClick: () => void;
    disabled?: boolean;
    danger?: boolean;
    children: ReactNode;
}) {
    return (
        <button
            type="button"
            aria-label={label}
            onClick={onClick}
            disabled={disabled}
            className={`rounded-md p-1.5 transition disabled:opacity-30 ${
                danger
                    ? 'text-zinc-500 hover:bg-rose-500/15 hover:text-rose-300'
                    : 'text-zinc-400 hover:bg-white/10 hover:text-zinc-100'
            }`}
        >
            {children}
        </button>
    );
}

export function SmallButton({
    onClick,
    children,
}: {
    onClick: () => void;
    children: ReactNode;
}) {
    return (
        <button
            type="button"
            onClick={onClick}
            className="rounded-lg border border-dashed border-white/15 px-3 py-1.5 text-xs text-zinc-400 transition hover:border-indigo-400/50 hover:text-indigo-200"
        >
            {children}
        </button>
    );
}
