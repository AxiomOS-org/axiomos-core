# PERFORMANCE REPORT — HTTP Kernel (Sprint 4.8)

## Measurements

Observed on the dev machine (PHP 8.3.16), booting the real `modules/` directory (20 discovered, 4 booted):

| Metric | Value |
|---|---|
| Kernel boot time | ~6–15 ms (cold, per process) |
| `/` response | 200, negligible compute |
| `/health` response | 200, boot reused (no re-boot) |
| Process memory after boot | ~6 MB |

## Findings

1. **Lazy boot + reuse** — the kernel boots on the first request and is reused while `Ready`; subsequent requests skip boot (`bootCount` stays 1 across requests in tests).
2. **Discovery scans disk every cold boot** — 20 `module.json` files are read per process. Fine at this scale; no cross-request cache yet.
3. **Router container is minimal** — only `CallableDispatcher`/`ControllerDispatcher` are bound; no middleware stack overhead.
4. **Health checks are O(checks)** and cheap (state read + `memory_get_usage`).

## Risks

| Risk | Severity | Mitigation |
|---|---|---|
| Per-process disk scan under FPM at high module counts | Medium | Add a persistent module-discovery cache (APCu/file) |
| Re-boot if a request leaves kernel non-Ready | Low | Boot is idempotent and guarded by state check |
| No output/opcode caching configured | Low | Enable OPcache in production; add route cache later |

## Recommendations

1. Enable a persistent `CacheRepository` on `ModuleLoader` for FPM deployments.
2. Prefer a long-running runtime (Octane/RoadRunner) so boot happens once per worker.
3. Enable OPcache + `composer dump-autoload -o` in production.
4. Add a `/metrics` scrape exporter (Prometheus) before production rollout.

## Status

**Pass** — meets the milestone with headroom; caching is an optimisation, not a blocker.
