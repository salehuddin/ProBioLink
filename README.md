# ProBioLink

Product brief for a block-based professional profile builder — link-in-bio simplicity with resume-level depth. The working name is still undecided (ProfileLink / Biofolio / ProBioLink).

The brief is split into one file per top-level section so two people can work at the same time without editing the same lines. Read in order, or jump to the part you own.

| #   | Section                 | What it covers                                              | File                                                                     |
| --- | ----------------------- | ----------------------------------------------------------- | ------------------------------------------------------------------------ |
| 01  | Executive Summary       | Product name, one-paragraph pitch, target users             | [01-executive-summary.md](docs/01-executive-summary.md)                  |
| 02  | Problem Statement       | The gap between link-in-bio tools and a real resume         | [02-problem-statement.md](docs/02-problem-statement.md)                  |
| 03  | Product Description     | Core concept, block architecture, presets, templates        | [03-product-description.md](docs/03-product-description.md)              |
| 04  | Feature Set             | MVP, advanced and premium features by phase                 | [04-feature-set.md](docs/04-feature-set.md)                              |
| 05  | Target Market           | User segments, use cases                                    | [05-target-market.md](docs/05-target-market.md)                          |
| 06  | Competitive Landscape   | Competitor matrix, market gaps                              | [06-competitive-landscape.md](docs/06-competitive-landscape.md)          |
| 07  | Market Opportunity      | Market size, TAM/SAM/SOM, timing                            | [07-market-opportunity.md](docs/07-market-opportunity.md)                |
| 08  | Business Model          | Revenue streams, projected economics, unit economics        | [08-business-model.md](docs/08-business-model.md)                        |
| 09  | Growth Strategy         | Channels, viral loop, launch sequence                       | [09-growth-strategy.md](docs/09-growth-strategy.md)                      |
| 10  | Competitive Advantages  | What is defensible                                          | [10-competitive-advantages.md](docs/10-competitive-advantages.md)        |
| 11  | Technical Considerations| Stack decisions, key technical challenges                   | [11-technical-considerations.md](docs/11-technical-considerations.md)    |
| 12  | Risks & Mitigation      | Risk, likelihood, response                                  | [12-risks.md](docs/12-risks.md)                                          |
| 13  | Success Metrics         | Product, business and growth targets                        | [13-success-metrics.md](docs/13-success-metrics.md)                      |
| 14  | Next Steps              | Immediate, short-term and medium-term actions               | [14-next-steps.md](docs/14-next-steps.md)                                |
| 15  | Appendix                | Feature comparison detail, sources, new competitors         | [15-appendix-feature-comparison.md](docs/15-appendix-feature-comparison.md) |

## Working on this together

Never commit directly to `main`. Each change goes on its own branch and comes back through a pull request that the other person reviews:

```sh
git switch main && git pull --rebase
git switch -c brief/<what-you-are-changing>
# edit one section file
git add -A && git commit -m "Tighten pricing assumptions"
git push -u origin brief/<what-you-are-changing>
gh pr create --fill
```

Notes for agents working in this repo live in [AGENTS.md](AGENTS.md) — OpenCode reads it automatically.
