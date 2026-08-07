#!/usr/bin/env python3
"""GoldPlatform V2 read-only GitHub evidence harvester.

Purpose:
- Batch-read PR metadata, exact PR-head workflow runs, and canonical ancestry.
- Produce deterministic JSON or Markdown evidence tables for V2 recovery audits.

Safety:
- Read-only GitHub REST calls only.
- No merge, branch mutation, issue/PR update, workflow rerun, or repository write.
- No business-rule, Kimia, financial, capability-completion, or Production-Ready inference.
- Missing evidence is emitted as UNKNOWN / NOT_FOUND rather than guessed.

Environment:
- GITHUB_TOKEN: required unless --token is supplied.
- GITHUB_API_URL: optional, defaults to https://api.github.com.

Example:
  python .github/scripts/v2_evidence_harvester.py \
    --repo 1alirezabahramian/GoldPlatform \
    --start-pr 151 --end-pr 168 \
    --canonical-ref recovery/rc2-product-rebuild \
    --format markdown
"""

from __future__ import annotations

import argparse
import json
import os
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from dataclasses import asdict, dataclass
from typing import Any


@dataclass
class WorkflowEvidence:
    run_id: int | None
    name: str | None
    run_number: int | None
    status: str
    conclusion: str | None


@dataclass
class PrEvidence:
    number: int
    title: str | None
    state: str
    merged: bool
    draft: bool
    base_ref: str | None
    base_sha: str | None
    head_ref: str | None
    head_sha: str | None
    merge_sha: str | None
    changed_files: int | None
    additions: int | None
    deletions: int | None
    workflow: WorkflowEvidence
    canonical_relation: str
    canonical_ahead_by: int | None
    canonical_behind_by: int | None
    evidence_error: str | None = None


class GitHubReader:
    def __init__(self, token: str, api_url: str, timeout: int = 30, retries: int = 3) -> None:
        self.token = token
        self.api_url = api_url.rstrip('/')
        self.timeout = timeout
        self.retries = retries

    def get(self, path: str) -> Any:
        url = f"{self.api_url}{path}"
        headers = {
            "Accept": "application/vnd.github+json",
            "Authorization": f"Bearer {self.token}",
            "X-GitHub-Api-Version": "2022-11-28",
            "User-Agent": "goldplatform-v2-evidence-harvester",
        }
        request = urllib.request.Request(url, headers=headers, method="GET")
        last_error: Exception | None = None
        for attempt in range(1, self.retries + 1):
            try:
                with urllib.request.urlopen(request, timeout=self.timeout) as response:
                    return json.loads(response.read().decode("utf-8"))
            except (urllib.error.HTTPError, urllib.error.URLError, TimeoutError) as exc:
                last_error = exc
                if isinstance(exc, urllib.error.HTTPError) and exc.code in {401, 403, 404, 422}:
                    raise
                if attempt < self.retries:
                    time.sleep(min(2 ** (attempt - 1), 4))
        assert last_error is not None
        raise last_error


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Read-only V2 GitHub evidence harvester")
    parser.add_argument("--repo", required=True, help="owner/repository")
    parser.add_argument("--start-pr", type=int, required=True)
    parser.add_argument("--end-pr", type=int, required=True)
    parser.add_argument("--canonical-ref", required=True)
    parser.add_argument("--token", default=os.getenv("GITHUB_TOKEN"))
    parser.add_argument("--api-url", default=os.getenv("GITHUB_API_URL", "https://api.github.com"))
    parser.add_argument("--format", choices=("json", "markdown"), default="json")
    return parser.parse_args()


def exact_head_workflow(reader: GitHubReader, repo: str, head_sha: str | None) -> WorkflowEvidence:
    if not head_sha:
        return WorkflowEvidence(None, None, None, "NOT_FOUND", None)
    encoded_sha = urllib.parse.quote(head_sha, safe="")
    payload = reader.get(f"/repos/{repo}/actions/runs?head_sha={encoded_sha}&per_page=100")
    runs = [run for run in payload.get("workflow_runs", []) if run.get("head_sha") == head_sha]
    if not runs:
        return WorkflowEvidence(None, None, None, "NOT_FOUND", None)
    runs.sort(key=lambda run: (run.get("run_number") or 0, run.get("run_attempt") or 0), reverse=True)
    run = runs[0]
    return WorkflowEvidence(
        run_id=run.get("id"),
        name=run.get("name"),
        run_number=run.get("run_number"),
        status=run.get("status") or "UNKNOWN",
        conclusion=run.get("conclusion"),
    )


def canonical_relation(reader: GitHubReader, repo: str, head_sha: str | None, canonical_ref: str) -> tuple[str, int | None, int | None]:
    if not head_sha:
        return "UNKNOWN", None, None
    base = urllib.parse.quote(head_sha, safe="")
    head = urllib.parse.quote(canonical_ref, safe="")
    payload = reader.get(f"/repos/{repo}/compare/{base}...{head}")
    status = (payload.get("status") or "UNKNOWN").upper()
    return status, payload.get("ahead_by"), payload.get("behind_by")


def collect_pr(reader: GitHubReader, repo: str, number: int, canonical_ref: str) -> PrEvidence:
    try:
        pr = reader.get(f"/repos/{repo}/pulls/{number}")
        head_sha = (pr.get("head") or {}).get("sha")
        workflow = exact_head_workflow(reader, repo, head_sha)
        relation, ahead_by, behind_by = canonical_relation(reader, repo, head_sha, canonical_ref)
        return PrEvidence(
            number=number,
            title=pr.get("title"),
            state=pr.get("state") or "UNKNOWN",
            merged=bool(pr.get("merged")),
            draft=bool(pr.get("draft")),
            base_ref=(pr.get("base") or {}).get("ref"),
            base_sha=(pr.get("base") or {}).get("sha"),
            head_ref=(pr.get("head") or {}).get("ref"),
            head_sha=head_sha,
            merge_sha=pr.get("merge_commit_sha"),
            changed_files=pr.get("changed_files"),
            additions=pr.get("additions"),
            deletions=pr.get("deletions"),
            workflow=workflow,
            canonical_relation=relation,
            canonical_ahead_by=ahead_by,
            canonical_behind_by=behind_by,
        )
    except Exception as exc:  # Evidence collection must fail closed per PR, not invent data.
        return PrEvidence(
            number=number,
            title=None,
            state="UNKNOWN",
            merged=False,
            draft=False,
            base_ref=None,
            base_sha=None,
            head_ref=None,
            head_sha=None,
            merge_sha=None,
            changed_files=None,
            additions=None,
            deletions=None,
            workflow=WorkflowEvidence(None, None, None, "UNKNOWN", None),
            canonical_relation="UNKNOWN",
            canonical_ahead_by=None,
            canonical_behind_by=None,
            evidence_error=f"{type(exc).__name__}: {exc}",
        )


def render_markdown(rows: list[PrEvidence], repo: str, canonical_ref: str) -> str:
    lines = [
        "# GoldPlatform V2 Evidence Harvest",
        "",
        f"Repository: `{repo}`  ",
        f"Canonical ref: `{canonical_ref}`  ",
        "Classification: `READ-ONLY EVIDENCE — NO COMPLETION INFERENCE`",
        "",
        "| PR | State | Merged | Head SHA | Exact-head CI | Canonical relation | Error |",
        "|---:|---|:---:|---|---|---|---|",
    ]
    for row in rows:
        ci = row.workflow.status
        if row.workflow.run_number is not None:
            ci = f"#{row.workflow.run_number} {row.workflow.status}"
        if row.workflow.conclusion:
            ci += f" / {row.workflow.conclusion}"
        lines.append(
            "| {number} | {state} | {merged} | `{sha}` | {ci} | {relation} | {error} |".format(
                number=row.number,
                state=row.state,
                merged="yes" if row.merged else "no",
                sha=row.head_sha or "UNKNOWN",
                ci=ci,
                relation=row.canonical_relation,
                error=(row.evidence_error or "").replace("|", "\\|"),
            )
        )
    lines.extend([
        "",
        "> This output is evidence only. It does not authorize Kimia Write, financial rules, merge, stage closure, or Production Ready claims.",
    ])
    return "\n".join(lines)


def main() -> int:
    args = parse_args()
    if args.start_pr <= 0 or args.end_pr < args.start_pr:
        print("Invalid PR range", file=sys.stderr)
        return 2
    if not args.token:
        print("GITHUB_TOKEN or --token is required", file=sys.stderr)
        return 2

    reader = GitHubReader(args.token, args.api_url)
    rows = [collect_pr(reader, args.repo, number, args.canonical_ref) for number in range(args.start_pr, args.end_pr + 1)]

    if args.format == "markdown":
        print(render_markdown(rows, args.repo, args.canonical_ref))
    else:
        print(json.dumps({
            "repository": args.repo,
            "canonical_ref": args.canonical_ref,
            "classification": "READ-ONLY EVIDENCE — NO COMPLETION INFERENCE",
            "pull_requests": [asdict(row) for row in rows],
        }, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
