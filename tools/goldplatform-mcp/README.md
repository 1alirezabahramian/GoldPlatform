# GoldPlatform Developer MCP

Private, tool-only MCP server for controlled development access to the approved GoldPlatform working copy.

## Current phase

Phase A foundation only:

- Streamable HTTP MCP endpoint at `/mcp`
- health endpoint at `/healthz`
- project-root confinement
- `.env` access denial
- read-only `project_status` tool
- path-confinement tests

No arbitrary shell execution, Git writes, database mutations, or Kimia write operations are included.

## Requirements

- Node.js 22 or newer
- Docker Desktop running
- GoldPlatform working copy at:

```text
C:\Users\USER\Desktop\p\GoldPlatform
```

Override the path only when the approved working copy moves:

```powershell
$env:GOLDPLATFORM_PROJECT_ROOT = 'C:\approved\path\GoldPlatform'
```

## Install

From PowerShell 7:

```powershell
cd C:\Users\USER\Desktop\p\GoldPlatform\tools\goldplatform-mcp
npm install
```

## Validate

```powershell
npm run check
```

This performs TypeScript compilation and the Node test suite.

## Run locally

```powershell
npm run dev
```

Defaults:

- MCP: `http://127.0.0.1:8787/mcp`
- Health: `http://127.0.0.1:8787/healthz`

Check health:

```powershell
Invoke-RestMethod http://127.0.0.1:8787/healthz
```

## ChatGPT development connection

1. Keep the local server running.
2. Expose port `8787` through an approved temporary HTTPS tunnel.
3. Enable Developer Mode in ChatGPT app/plugin settings.
4. Create a private app using the HTTPS tunnel URL ending in `/mcp`.
5. Refresh the app whenever tool metadata changes.
6. Test `project_status` before enabling any future mutating tools.

## Security boundary

The MCP is restricted to the configured GoldPlatform project root. It must not read `.env`, credentials, tokens, or files outside that root.

The project contract is defined in [SPEC.md](./SPEC.md). GoldPlatform financial and Kimia rules remain governed by `docs/00_PROJECT_MEMORY.md` and accepted ADRs.

## Not yet validated

The files were scaffolded through GitHub. Dependency installation, TypeScript compilation, runtime startup, MCP discovery, and Windows execution must be validated on the shop computer before this phase is called healthy.
