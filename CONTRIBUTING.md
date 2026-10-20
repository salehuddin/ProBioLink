# Working on the brief

How to change anything in this repository without stepping on the other person's work. Five minutes of setup, then one rule that matters.

## 1. Set up (once)

Accept the collaborator invite first — the clone below fails until you have, then:

```sh
git clone https://github.com/salehuddin/ProBioLink.git
cd ProBioLink
git config user.name "Your Name"
git config user.email "you@example.com"
```

Optionally install the GitHub CLI (`gh auth login`) so you can open pull requests from the terminal. Without it, pushing prints a link that opens the pull request form in a browser.

Clone into a normal folder, not a OneDrive or Dropbox one. The primary checkout happens to live in OneDrive, but a cloud client syncing `.git` while git writes to it can corrupt the repository.

## 2. The one rule

**Never commit to `main`.** It is a protected branch: direct pushes, force-pushes and deletion are rejected for everyone, and merges are squash-only. Every change goes on a branch and comes back as a pull request.

## 3. The loop

```sh
git switch main
git pull --rebase
git switch -c brief/pricing
# edit one section file, e.g. docs/08-business-model.md
git add -A
git commit -m "Tighten pricing assumptions"
git push -u origin brief/pricing
gh pr create --fill        # or open the link git prints
```

Then whoever is free reviews and squash-merges; the branch is deleted automatically. Start the next change with `git switch main && git pull --rebase` again.

One branch, one topic, ideally one file. This is the whole reason the brief is split into files under `docs/`: we can both work at once without touching the same lines. Git will never silently overwrite a pushed commit, but it will cheerfully merge two rewrites of the same paragraph into something neither of us wrote.

## 4. Your OpenCode agent

The repository root has an `AGENTS.md`, and OpenCode loads it automatically at session start in this folder — no setup step, and it applies to every session. It tells the agent how we work: branch per change, never push to `main`, touch only the file you name, keep diffs small, do not reformat files or reflow tables, do not silently rewrite the author's wording.

So "add a competitor row to `docs/06-competitive-landscape.md`" should change that one file. If an agent edits three sections you did not mention, it is ignoring the rules — start a fresh session.

Changes to `AGENTS.md` itself only take effect in a new session.

## 5. House style

- One top-level `## ` heading per file. The number prefix is the reading order.
- A new section becomes `docs/16-your-topic.md`, linked from the `README.md` table in the same pull request.
- Keep diffs small. Do not reflow tables or reformat whole files: a whole-file diff collides with whatever the other person is editing.
- Leave the author's wording alone unless the wording is the point. That covers the escaped `\$`, the `&#x20;` artifacts and the box-drawing diagrams. Flag them; do not tidy them.
- A Markdown table needs a header row plus a `| --- |` separator row to render at all. If you find one that does not render, fixing the header is a legitimate standalone change.

## 6. When git complains

- **"protected branch update failed"** — you are committing on `main`. Run `git switch -c brief/your-topic`, commit again, push that branch.
- **A conflict** — `git pull --rebase`, edit the marked sections, `git add <file>`, `git rebase --continue`.
- **A file you barely touched shows as fully rewritten** — line endings or a table reflow. `.gitattributes` should prevent it; if it happens anyway, stop before pushing.
- **Lost** — `git status`, then `git switch main && git pull --rebase` puts you back on solid ground.

## 7. Claiming work

Say which section you are taking ("I'll do business model and growth") so the other person stays out of it. If something does not fit an existing file, raise it rather than inventing a new structure.

## Reference

- [README.md](README.md) — index of the brief
- [AGENTS.md](AGENTS.md) — the rules agents follow in this repository
