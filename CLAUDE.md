# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
npm install

# Start the server
npm start           # or: node server.js

# Syntax-check the server without running it
node --check server.js

# Health check (server must be running)
curl http://localhost:3000/api/health
```

There is no test suite or linter configured.

## Environment Variables

| Variable | Description |
|---|---|
| `PORT` | HTTP port (default 3000) |
| `NODE_ENV` | Set to `production` on Render |
| `GOOGLE_CALENDAR_ID` | Google Calendar ID for booking events |
| `GOOGLE_CREDENTIALS_JSON` | Service account JSON (cloud); falls back to `data/google-credentials.json` locally |
| `AI_API_KEY` | Anthropic API key; can also be set via admin UI (stored in `data/ai-config.json`) |
| `RENDER_EXTERNAL_URL` | When set, triggers a self-ping every 14 min to prevent cold starts |

## Architecture

Everything lives in a single `server.js` (~1 100 lines) — a plain Node.js `http` server with no framework. All routing is done with `if/else` on `req.url` and `req.method`.

### Data layer

No database. All persistent state lives in JSON files under `data/` (gitignored):

- `data/leads.json` — booking & contact form submissions
- `data/findings.json` — gas safety findings (photos, status, approval)
- `data/users.json` — installer/admin accounts (scrypt-hashed passwords)
- `data/ai-config.json` — Anthropic API key set via admin UI
- `data/google-credentials.json` — service account for local dev

Uploaded images go to `images/findings/` and `images/contact/`. Multipart parsing is hand-rolled inside `server.js` (no `multer`).

### Auth

Token-based sessions stored in an in-memory `Map` (`activeSessions`). Tokens are passed as `Authorization: Bearer <token>`. Sessions expire after 8 hours. There is no refresh flow. A super-admin seed account is hardcoded near the top of `server.js` and merged with `data/users.json` at startup.

### Role model

Three roles checked inline throughout `server.js`:
- **public** — booking, contact, viewing approved findings
- **installer** — submit/delete own findings, use AI rewrite
- **admin** — approve/feature findings, manage users, configure AI key

### External integrations

- **Google Calendar** (`googleapis` package) — creates calendar events on booking; credentials loaded from env or local file
- **Claude API** (raw `https` fetch, no SDK) — called at `/api/installer/ai-rewrite`; model is `claude-haiku-4-5-20251001`; supports `rewrite` (UK Gas Safe professional tone) and `proofread` modes

### Frontend

Static files served directly by `server.js`. Two distinct UIs:
- **Public site** — `index.html` + `pages/` + `js/main.js` + `css/style.css`
- **Installer/admin portal** — `pages/installer.html` + `admin/index.html` + `js/installer.js` + `css/installer.css`

### Deployment

Hosted on Render (see `render.yaml`). Build command: `npm install`. Start command: `node server.js`. Auto-deploys from the `master` branch.
