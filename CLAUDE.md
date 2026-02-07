# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Always use Context7 MCP when I need library/API documentation, code generation, setup or configuration steps without me having to explicitly ask.

## Project Overview

Prezes Bot — AI assistant with a Laravel 12 REST API backend and an Expo (React Native) mobile app. The AI uses Google Gemini 2.5 Flash via OpenRouter through the Prism PHP library. All communication is in Polish.

## Development Commands

### Backend (from project root)
- `composer dev` — starts all services concurrently: API server (port 8000), queue worker, log viewer, Vite, and scheduler
- `composer test` — run PHPUnit tests
- `composer setup` — initial project setup
- `php artisan serve` — API server only
- `php artisan schedule:work` — run scheduler (executes planned tasks every 2 seconds)
- `./vendor/bin/pint` — fix code style (Laravel preset)
- `composer analyse` — run PHPStan static analysis (level 5, Larastan preset)

### Mobile (from `mobile/`)
- `bun start` — Expo dev server
- `bun run android` — run on Android
- **Always use `bun`, never `npm`** — for installing deps, running scripts, everything

## Architecture

### Backend — Action Pattern
Business logic lives in `app/Actions/`, not in controllers. Controllers are thin invokable classes that delegate to actions.

**Request flow**: Route → Middleware (`EnsureTokenIsValid`) → Controller → Action → Response

Key actions:
- `AskAI` — orchestrates AI chat with tool support (web search, task creation)
- `GenerateTTS` — ElevenLabs text-to-speech via Prism
- `ExecuteTaskWithAI` — runs scheduled tasks through AI
- `WebSearch` — Perplexity Sonar search integration

### AI Tool System
The AI (Gemini) has access to tools defined in `GetTools` as Prism `ProviderTool` objects:
- `create_planned_task` — schedule one-time or repeating tasks
- `web_search` — search the web via Perplexity

### Task Scheduling
`PlannedTask` model stores scheduled AI tasks with `instruction`, `execute_at`, and `repeating` fields. The `tasks:execute` command runs every 2 seconds via Laravel's scheduler, picking up overdue tasks.

### Mobile App
- Expo Router file-based routing in `mobile/app/`
- Single chat screen with speech recognition input (Polish) and TTS output
- `mobile/lib/api.ts` — API client with token auth
- `mobile/lib/tts.ts` — ElevenLabs TTS via backend, falls back to expo-speech
- Environment variables (`API_URL`, `APP_TOKEN`) loaded via `react-native-dotenv`

### API Endpoints (all require Authorization header)
- `POST /api/ask` — chat (body: `{ prompt }`)
- `POST /api/tts` — text-to-speech, returns `{ audio: "<base64>" }` (body: `{ text }`)
- `GET /api/check` — health check

## Code Conventions

- PHP: strict types (`declare(strict_types=1)`) in all files, Laravel Pint for formatting, PHPStan level 5 (Larastan) for static analysis
- After changing PHP code, run `composer analyse` to verify there are no PHPStan errors. Add `@property` PHPDoc on models for cast attributes when needed.
- TypeScript: strict mode enabled
- Controllers are invokable (single `__invoke` method)
- Form requests handle validation (`app/Http/Requests/`)
- All dates use `CarbonImmutable`
- Database: SQLite, also used for queue, cache, and sessions
