import type { Block, BlockType, Profile } from '../types';

const apiBase = '/api';

export class ApiError extends Error {
    constructor(
        message: string,
        readonly status: number,
    ) {
        super(message);
        this.name = 'ApiError';
    }
}

function csrfToken(): string {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content') ?? ''
    );
}

async function request<T>(
    path: string,
    init?: RequestInit,
    base = apiBase,
): Promise<T> {
    const method = (init?.method ?? 'GET').toUpperCase();
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...((init?.headers as Record<string, string>) ?? {}),
    };

    if (method !== 'GET' && method !== 'HEAD') {
        headers['X-CSRF-TOKEN'] = csrfToken();
    }

    const response = await fetch(`${base}${path}`, {
        credentials: 'same-origin',
        ...init,
        headers,
    });

    if (!response.ok) {
        throw new ApiError(messageFor(response.status), response.status);
    }

    if (response.status === 204) {
        return undefined as T;
    }

    return response.json() as Promise<T>;
}

function messageFor(status: number): string {
    switch (status) {
        case 401:
            return 'Sign in to continue';
        case 403:
            return 'You do not own this profile';
        case 404:
            return 'Profile not found';
        case 419:
            return 'Session expired — reload the page';
        case 422:
            return 'That change was rejected';
        default:
            return `Request failed with ${status}`;
    }
}

export interface AuthUser {
    id: number;
    name: string;
    email: string;
}

export async function fetchMe(): Promise<AuthUser | null> {
    try {
        const payload = await request<{ data: AuthUser }>('/me');

        return payload.data;
    } catch (error) {
        if (error instanceof ApiError && error.status === 401) {
            return null;
        }

        throw error;
    }
}

export async function login(
    email: string,
    password: string,
): Promise<AuthUser> {
    const payload = await request<{ data: AuthUser }>(
        '/login',
        { method: 'POST', body: JSON.stringify({ email, password }) },
        '',
    );

    return payload.data;
}

export async function logout(): Promise<void> {
    await request('/logout', { method: 'POST' }, '');
}

export interface ProfileSummary {
    username: string;
    display_name: string;
    headline: string | null;
}

export async function fetchProfiles(): Promise<ProfileSummary[]> {
    const payload = await request<{ data: ProfileSummary[] }>('/profiles');

    return payload.data;
}

export async function fetchProfile(username: string): Promise<Profile> {
    const payload = await request<{ data: Profile }>(`/profiles/${username}`);

    return payload.data;
}

/** Persists order, visibility and edited content in one transaction. */
export async function saveProfile(
    username: string,
    blocks: Block[],
): Promise<Profile> {
    const payload = await request<{ data: Profile }>(
        `/profiles/${username}/blocks`,
        {
            method: 'PATCH',
            body: JSON.stringify({
                blocks: blocks.map(({ id, position, is_visible, data }) => ({
                    id,
                    position,
                    is_visible,
                    data,
                })),
            }),
        },
    );

    return payload.data;
}

export interface ProfileDetails {
    display_name: string;
    headline: string | null;
    location: string | null;
    template?: string;
}

export async function saveProfileDetails(
    username: string,
    details: ProfileDetails,
): Promise<Profile> {
    const payload = await request<{ data: Profile }>(`/profiles/${username}`, {
        method: 'PATCH',
        body: JSON.stringify(details),
    });

    return payload.data;
}

export async function applyPreset(
    username: string,
    preset: string,
): Promise<Profile> {
    const payload = await request<{ data: Profile }>(
        `/profiles/${username}/presets`,
        { method: 'POST', body: JSON.stringify({ preset }) },
    );

    return payload.data;
}

export async function createBlock(
    username: string,
    type: BlockType,
): Promise<Profile> {
    const payload = await request<{ data: Profile }>(
        `/profiles/${username}/blocks`,
        { method: 'POST', body: JSON.stringify({ type }) },
    );

    return payload.data;
}

export async function deleteBlock(
    username: string,
    blockId: number,
): Promise<Profile> {
    const payload = await request<{ data: Profile }>(
        `/profiles/${username}/blocks/${blockId}`,
        { method: 'DELETE' },
    );

    return payload.data;
}
