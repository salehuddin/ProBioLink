# ProBioLink — app

Working prototype of **ProBioLink**: a block-based professional profile builder (link-in-bio simplicity, resume-level depth). The product brief lives in a separate repository; this one is the application.

## Stack

The brief specifies Supabase, but this scaffold uses the stack the team agreed on:

| Layer | Choice |
| --- | --- |
| Backend | Laravel 13 (API routes under `/api`) |
| Frontend | React 19 + TanStack Router + TanStack Query |
| Styling | Tailwind CSS 4 |
| Build | Vite 8 via `laravel-vite-plugin` |
| Database | SQLite for local development; MySQL/Postgres for anything shared |

There is one process and one origin: Laravel serves the SPA shell, React resolves every non-API path client-side. No CORS setup, no second dev server.

## Run it

```sh
composer install
npm install

cp .env.example .env      # then set APP_KEY
php artisan key:generate

php artisan migrate --seed
npm run build             # or: npm run dev, for hot reload
php artisan serve
```

Open <http://127.0.0.1:8000>. The seeder creates one demo profile, `amir`, owned by a demo account:

| Login | Value |
| --- | --- |
| Email | `amir@example.com` |
| Password | `password` |

| Path | What it is |
| --- | --- |
| `/` | Directory of published profiles (SPA) |
| `/u/amir` | Public profile — **server-rendered by Laravel**, no JavaScript required |
| `/editor/amir` | Editor — requires sign-in; profile details, template, presets, blocks |
| `/login` | Sign-in form |
| `/robots.txt`, `/sitemap.xml` | Crawler discovery — published profiles only |

### Why the public profile is not React

Shared links have to work without a browser executing JavaScript: search engines index them, and chat apps scrape their meta tags. So `/u/{username}` is rendered by `PublicProfileController` into Blade views, with `<title>`, description, canonical, Open Graph tags and `Person` JSON-LD. The SPA handles the directory and the editor, where JavaScript is a given.

Theme classes live in `resources/themes.json`, read by both the Blade renderer (`App\Support\Templates`) and React (`resources/js/lib/templates.ts`), so the two cannot drift.

## API

Reads are public; every write requires a signed-in session and ownership of the profile.

Every response is wrapped in `data`.

| Method | Path | Auth | Purpose |
| --- | --- | --- | --- |
| `GET` | `/api/profiles` | — | Published profiles (username, name, headline) |
| `GET` | `/api/profiles/{username}` | — | Profile with ordered blocks |
| `GET` | `/api/me` | required | Current user |
| `PATCH` | `/api/profiles/{username}` | owner | Update display name, headline, location, template |
| `POST` | `/api/profiles/{username}/presets` | owner | Replace all blocks with a preset (`job_seeker`, `freelancer`) |
| `POST` | `/api/profiles/{username}/blocks` | owner | Add a block of a given type |
| `PATCH` | `/api/profiles/{username}/blocks` | owner | Save order, visibility and content together |
| `PATCH` | `/api/profiles/{username}/blocks/{block}` | owner | Update one block's content |
| `DELETE` | `/api/profiles/{username}/blocks/{block}` | owner | Delete a block and reindex the rest |

Authentication is a Laravel session managed by Sanctum's SPA mode (`statefulApi()`): same origin, `credentials: 'same-origin'`, and an `X-CSRF-TOKEN` header taken from the shell's `<meta name="csrf-token">`. There are no bearer tokens in the browser.

`PATCH .../blocks` body: `{ "blocks": [{ "id": 1, "position": 0, "is_visible": true, "data": { … } }] }`. `data` is optional per block; when present it replaces that block's content in the same transaction as the layout.

## Layout

```
app/Http/Controllers/Api/ProfileController.php   JSON API (editor)
app/Http/Controllers/PublicProfileController.php  server-rendered profile
app/Http/Controllers/AuthController.php          sign in / out, session
app/Policies/ProfilePolicy.php                  owner-only writes
resources/views/profile/                          Blade renderer + blocks
app/Models/{Profile,Block}.php                    Eloquent models
database/migrations/                              profiles, blocks
database/seeders/ProfileSeeder.php                demo content
resources/themes.json                             theme classes, shared PHP + TS
resources/js/routes/                              TanStack Router (file-based)
resources/js/components/                          ProfileView, BlockEditor
resources/js/lib/api.ts                           typed API client
resources/js/types.ts                             shared block/profile types
```

A block is `{ id, type, position, is_visible, data }`. `data` is JSON whose shape depends on `type` (`hero`, `bio`, `links`, `experience`, `skills`, `services`, `testimonial`); see `resources/js/types.ts`. New blocks start from the defaults in `app/Support/BlockTemplates.php`.

Presets live in `app/Support/BlockPresets.php` (block order plus starter copy per intent); themes are class maps in `resources/themes.json`, applied by the Blade renderer and by React's `ProfileView` independently of content.

## Deployment notes

Before this is reachable from the internet:

1. `npm run build` (build output is gitignored, so it must run in CI or on the host).
2. Set `APP_URL` to the real origin, and add that origin to `SANCTUM_STATEFUL_DOMAINS`.
3. Set `SESSION_SECURE_COOKIE=true` and serve over HTTPS — the auth cookie depends on it.
4. Change or delete the seeded demo account; its password is in this README.
5. Move off SQLite (`DB_CONNECTION`), and run `php artisan migrate --force`.

## Known gaps

These are deliberate — the skeleton is meant to grow into them:

- **Auth is session-only, no API tokens.** Fine for this SPA (same origin); a mobile client would need Sanctum tokens or OAuth.
- **No image or avatar upload.** `avatar_url` is a column, but nothing sets it; profiles fall back to initials.
- **No drag-and-drop.** Reordering is arrow buttons; `dnd-kit` is the intended upgrade.
- **Two presets, three templates.** The brief describes five presets and four template categories; the extra ones are unbuilt.
- **Applying a preset replaces every block** after an inline confirmation. There is no merge or undo.
- **No registration, password reset or email verification.** Accounts are created by the seeder or by hand; the demo password is public knowledge and must be changed before any real deployment.
- **One profile per user.** The schema allows more, but nothing creates a second profile and the editor assumes one.
- **SQLite locally.** Fine for one developer; switch `DB_CONNECTION` before two people share a database.
- **No browser tests.** The API and server-rendered pages have feature tests; the SPA has none.

## Conventions

- One concern per branch, PRs reviewed by the other person.
- Types in `resources/js/types.ts` are the contract between API and UI — change them together with the resource classes.
- Run `npm run build` before pushing: the Vite build is the only type/compile gate at the moment.
