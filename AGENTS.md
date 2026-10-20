# Project instructions

This repository holds the product brief for **ProBioLink**, a block-based professional profile builder (link-in-bio simplicity, resume-level depth). It is a **documentation-only repo**: Markdown, no application code, no build step, no tests. Two people edit it concurrently, each often through an agent. The rules below exist to keep their changes from colliding.

## Layout

- `README.md` — index of the brief, and the short version of the workflow.
- `docs/NN-topic.md` — one top-level section of the brief per file. The number is the reading order.
- `AGENTS.md` — this file.

## Git workflow (do not skip)

- **Never commit to `main`.** Branch per change: `git switch -c brief/<topic>`.
- Always `git switch main && git pull --rebase` before branching, so you start from the other person's latest work.
- Push the branch and open a PR (`gh pr create --fill`). The other person reviews and squash-merges. Do not merge your own PR unless the user tells you to.
- One branch, one topic. Keep commits small and scoped to the section being changed.
- **Never force-push, never rewrite history, never rebase `main`.** If a push is rejected, pull and rebase your branch instead.
- `main` is a protected branch: GitHub rejects direct pushes, force-pushes and deletion of it, for everyone including the owner. A rejected push means you forgot to branch — create the branch and push that instead. Squash merges only; the head branch is deleted on merge.

## Editing rules

- **Touch only the file(s) the task names.** If a change seems to need another section, say so rather than editing it.
- **Keep diffs minimal.** Do not reformat whole files, re-wrap prose, re-align or reflow tables, or "tidy" whitespace. A whole-file diff silently collides with the other person's concurrent edit to the same section. Change the lines concerned and nothing else.
- Preserve each file's `## ` heading, its heading levels, the table column padding style, and the blank-line spacing around headings.
- **Preserve the author's wording unless asked to change it.** This includes escaped dollars (`\$`), escaped tildes and asterisks, `&#x20;` artifacts, and the ASCII diagrams. Flag problems instead of fixing them silently. The one exception is a Markdown table that cannot render: a table needs a header row plus a separator row, so restoring a missing header is fair — mention it in the PR.
- When adding a section file, use the next `NN-` prefix, link it from the `README.md` table in the same branch, and keep the table's column alignment.
- Do not delete or rename existing `docs/` files, and do not renumber them, without asking the user first.

## Verifying a change

- `git diff --stat` must show only the files you meant to change, with small line counts. If a section you barely edited shows as entirely rewritten, your editor changed line endings or reflowed the table — revert and redo.
- Check that any table you touched still has a header row and a `| --- |` separator, and that the row has the same number of pipes as the header.
- Render check when it matters: `gh pr view --web` or the PR diff on GitHub.

## Environment notes

- The repository lives inside a OneDrive folder on the primary machine. Avoid creating lock files, temp files or editor swap files in the tree; `.gitignore` covers the usual noise.
- PowerShell is the shell on Windows. Quote paths that contain spaces, and expect git to print progress on stderr (PowerShell shows that as an error even when the command succeeded — check the exit state, not the red text).
