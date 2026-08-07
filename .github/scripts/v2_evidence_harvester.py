#!/usr/bin/env python3
"""GoldPlatform V2 read-only GitHub evidence harvester.

Batch-reads PR metadata, exact-head workflow runs and canonical ancestry.
It never infers business rules, Kimia behavior, capability completion or
Production Ready status. Missing evidence remains UNKNOWN / NOT_FOUND.
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
    event: str | None
    run_attempt: int | None


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
    backend_rc1: WorkflowEvidence
    workflows: list[WorkflowEvidence]
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
        request = urllib.request.Request(
            url,
            headers={
                "Accept": "application/vnd.github+json",
                "Authorization": f"Bearer {self.token}",
                "X-GitHub-Api-Version": "2022-11-28",
                "User-Agent": "goldplatform-v2-evidence-harvester",
            },
            method="GET",
        )
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


def workflow_from_run(run: dict[str, Any]) -> WorkflowEvidence:
    return WorkflowEvidence(
        run_id=run.get("id"),
        name=run.get("name"),
        run_number=run.get("run_number"),
        status=run.get("status") or "UNKNOWN",
        conclusion=run.get("conclusion"),
        event=run.get("event"),
        run_attempt=run.get("run_attempt"),
    )


def exact_head_workflows(reader: GitHubReader, repo: str, head_sha: str | None) -> list[WorkflowEvidence]:
    if not head_sha:
        return []
    encoded_sha = urllib.parse.quote(head_sha, safe="")
    payload = reader.get(f"/repos/{repo}/actions/runs?head_sha={encoded_sha}&per_page=100")
    runs = [run for run in payload.get("workflow_runs", []) if run.get("head_sha") == head_sha]
    runs.sort(
        key=lambda run: (
            str(run.get("name") or ""),
            run.get("run_number") or 0,
            run.get("run_attempt") or 0,
        )
    )
    return [workflow_from_run(run) for run in runs]


def select_latest_named(workflows: list[WorkflowEvidence], name: str) -> WorkflowEvidence:
    matches = [workflow for workflow in workflows if workflow.name == name]
    if not matches:
        return WorkflowEvidence(None, name, None, "NOT_FOUND", None, None, None)
    return max(matches, key=lambda workflow: (workflow.run_number or 0, workflow.run_attempt or 0))


def canonical_relation(
    reader: GitHubReader,
    repo: str,
    head_sha: str | None,
    canonical_ref: str,
) -> tuple[str, int | None, int | None]:
    if not head_sha:
        return "UNKNOWN", None, None
    base = urllib.parse.quote(head_sha, safe="")
    head = urllib.parse.quote(canonical_ref, safe="")
    payload = reader.get(f"/repos/{repo}/compare/{base}...{head}")
    return (
        (payload.get("status") or "UNKNOWN").upper(),
        payload.get("ahead_by"),
        payload.get("behind_by"),
    )


def collect_pr(reader: GitHubReader, repo: str, number: int, canonical_ref: str) -> PrEvidence:
    try:
        pr = reader.get(f"/repos/{repo}/pulls/{number}")
        merged = bool(pr.get("merged"))
        head_sha = (pr.get("head") or {}).get("sha")
        workflows = exact_head_workflows(reader, repo, head_sha)
        relation, ahead_by, behind_by = canonical_relation(reader, repo, head_sha, canonical_ref)
        return PrEvidence(
            number=number,
            title=pr.get("title"),
            state=pr.get("state") or "UNKNOWN",
            merged=merged,
            draft=bool(pr.get("draft")),
            base_ref=(pr.get("base") or {}).get("ref"),
            base_sha=(pr.get("base") or {}).get("sha"),
            head_ref=(pr.get("head") or {}).get("ref"),
            head_sha=head_sha,
            merge_sha=pr.get("merge_commit_sha") if merged else None,
            changed_files=pr.get("changed_files"),
            additions=pr.get("additions"),
            deletions=pr.get("deletions"),
            backend_rc1=select_latest_named(workflows, "Backend RC1 Validation"),
            workflows=workflows,
            canonical_relation=relation,
            canonical_ahead_by=ahead_by,
            canonical_behind_by=behind_by,
        )
    except Exception as exc:
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
            backend_rc1=WorkflowEvidence(None, "Backend RC1 Validation", None, "UNKNOWN", None, None, None),
            workflows=[],
            canonical_relation="UNKNOWN",
            canonical_ahead_by=None,
            canonical_behind_by=None,
            evidence_error=f"{type(exc).__name__}: {exc}",
        )


def render_workflow(workflow: WorkflowEvidence) -> str:
    if workflow.run_number is None:
        return workflow.status
    value = f"#{workflow.run_number} {workflow.status}"
    if workflow.conclusion:
        value += f" / {workflow.conclusion}"
    return value


def render_markdown(rows: list[PrEvidence], repo: str, canonical_ref: str) -> str:
    lines = [
        "# GoldPlatform V2 Evidence Harvest",
        "",
        f"Repository: `{repo}`  ",
        f"Canonical ref: `{canonical_ref}`  ",
        "Classification: `READ-ONLY EVIDENCE — NO COMPLETION INFERENCE`",
        "",
        "| PR | State | Merged | Head SHA | Backend RC1 exact-head CI | All exact-head runs | Canonical relation | Error |",
        "|---:|---|:---:|---|---|---:|---|---|",
    ]
    for row in rows:
        lines.append(
            "| {number} | {state} | {merged} | `{sha}` | {backend} | {run_count} | {relation} | {error} |".format(
                number=row.number,
                state=row.state,
                merged="yes" if row.merged else "no",
                sha=row.head_sha or "UNKNOWN",
                backend=render_workflow(row.backend_rc1),
                run_count=len(row.workflows),
                relation=row.canonical_relation,
                error=(row.evidence_error or "").replace("|", "\\|"),
            )
        )
    lines.extend([
        "",
        "> Evidence only: no Kimia Write, financial-rule, merge, capability-completion, stage-closure or Production Ready authorization is inferred.",
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
