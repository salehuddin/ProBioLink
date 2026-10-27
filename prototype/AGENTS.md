# Project rules

ProBioLink app: a Laravel JSON API with a React/TanStack SPA on top, plus server-rendered public profiles. The product brief lives in the separate `salehuddin/ProBioLink` repository — product decisions go there, code decisions go here.

## Before you push

Run both, and fix what they report:

```sh
php artisan test
npm run build
```

`npm run build` is the only type/compile gate for the SPA, so a green build is not optional.

## Git workflow

- **Branch per change**: `git switch main && git pull --rebase`, then `git switch -c <area>/<topic>` (e.g. `editor/drag-drop`, `api/block-validation`).
- Open a PR; the other person reviews and squash-merges. One branch, one concern.
- Never commit `.env`, `database/database.sqlite`, `vendor/`, `node_modules/` or `public/build/` — all ignored already.
- Never edit an existing migration after it has been shared; add a new one.

## Two renderers, one source of truth

Public profiles are rendered twice: Blade (`resources/views/profile/`) for crawlers and shared links, React (`resources/js/components/ProfileView.tsx`) for the editor preview.

- Theme classes change **only** in `resources/themes.json` — never inline a theme class in either renderer.
- A new block type needs all three: a Blade partial, a React renderer case, and rules in `app/Support/BlockSchema.php`. Missing any one of them produces a page that silently differs from its preview.
- `resources/js/types.ts` is the API contract. Change it together with `app/Http/Resources/*` and `BlockSchema`.

## Conventions

- API responses are always wrapped in `data`, and mutations return the full profile.
- Every write route needs `auth:sanctum` and a `Gate::authorize('update', $profile)` check. Do not add a write endpoint without both.
- Server-rendered output must stay escaped: use `{{ }}`, never `{!! !!}`, except for the JSON-LD script which is encoded with the `JSON_HEX_*` flags.
- Link URLs are allowlisted by `BlockSchema::LINK_URL_PATTERN`; if you widen it, widen the Blade guard in `profile/blocks/links.blade.php` too.

## Local setup

See `README.md`. The seeded login is `amir@example.com` / `password`; it is a fixture, not a secret.
