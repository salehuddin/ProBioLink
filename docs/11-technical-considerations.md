## Technical Considerations

### Implemented Stack (prototype)

The working prototype uses Laravel instead of Supabase. This is the stack the team agreed to build on:

| Layer             | Choice                                           |
| ----------------- | ------------------------------------------------ |
| Backend           | Laravel 13, JSON API under `/api`                |
| Frontend          | React 19, TanStack Router, TanStack Query        |
| Styling and build | Tailwind CSS 4, Vite 8                           |
| Database          | SQLite locally; MySQL or Postgres when shared    |

The specification below is the original proposal, kept for reference. Where the two disagree, the prototype is what exists.

### Core Technology Decisions

┌─────────────────────────────────────────────────────┐

│  ALTERNATIVE: FULL STACK SPECIFICATION             │

├─────────────────────────────────────────────────────┤

│                                                     │

│  FRONTEND                                           │

│  ├── Framework: TanStack Start                     │

│  ├── Router: TanStack Router                       │

│  ├── Data: TanStack Query (built-in)              │

│  ├── UI: Tailwind CSS + shadcn/ui                 │

│  ├── DnD: dnd-kit                                  │

│  └── Animations: Framer Motion                    │

│                                                     │

│  BACKEND                                            │

│  └── Supabase (same as above)                      │

│                                                     │

│  DEPLOYMENT                                         │

│  └── Vercel                                         │

│                                                     │

└─────────────────────────────────────────────────────┘

| Component | Recommendation             | Rationale                                   |
| --------- | -------------------------- | ------------------------------------------- |
| Frontend  | Next.js + React            | SSR for SEO, responsive, component-based    |
| Styling   | Tailwind CSS               | Rapid development, consistent design system |
| Database  | PostgreSQL (Supabase/Neon) | Relational data for blocks, user profiles   |
| Auth      | Clerk or Supabase Auth     | Simpler than building from scratch          |
| Storage   | Cloudinary or S3           | Image/video handling for portfolios         |
| Hosting   | Vercel                     | Seamless Next.js deployment                 |
| Analytics | Custom + Plausible         | Privacy-focused, simpler than GA            |

### Key Technical Challenges

1. Drag-and-Drop Block Editor — Complex UI, consider libraries like dnd-kit
2. Real-time Preview — Must feel instant across mobile/desktop
3. Profile Performance — Must load fast despite media (lazy loading, CDNs)
4. SEO for Profiles — Each profile needs to be indexable
5. PDF Generation — Server-side rendering for resume export
