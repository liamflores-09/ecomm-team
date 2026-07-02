# SKU Management (SKU Tracker + SLA & Weekly Output) — Design Spec
Date: 2026-07-03

## Overview
A new "SKU Management" nav group with two pages, replacing the team's external "PR x Content" / "SLA & WEEKLY OUTPUT" Google Sheet:

1. **SKU Tracker** — CRUD table for the SKU pipeline (Product Research → PR → Content → Posting), seeded from the ~1,230 historical rows in the attached workbook.
2. **SLA and Weekly Output** — read-only analytics dashboard computed from SKU Tracker data: average PR/Content SLA per ISO week, compared month-over-month.

Source reference: `Content x PR Posted SKUs 2026.xlsx`, sheets `PR x Content` and `SLA & WEEKLY OUTPUT`. Columns A–C on `PR x Content` (QC Checker, Advance PR Checker, Duplicate Checker) are auto-generated lookup/validation formulas from an external Google Sheet and are **omitted** — not reproduced in the app.

---

## Data

### New `skus` table
| Column                 | Type          | Notes |
|-------------------------|---------------|-------|
| id                       | bigint PK     | |
| brand                    | string        | |
| sku                      | string        | not unique-constrained — duplicates are flagged as a soft UI warning, matching the sheet's "Duplicate Checker" behavior, not blocked |
| variant                  | string, nullable | one of: `Single`, `Variant/Parent`, `Variant/Child`, `Add Variant` |
| pr_file_location         | string, nullable | free text (local file path in source data) |
| pr_assignee              | string, nullable | free text, not an FK — historical names (e.g. "Kent", "Vin") don't map to current user accounts |
| pr_status                | string, nullable | one of: `DONE`, `IN PROGRESS`, `On Hold` |
| ready_for_cvp            | boolean, default false | |
| remarks                  | text, nullable | |
| pr_date_started          | date, nullable | |
| pr_date_completed        | date, nullable | |
| content_assignee         | string, nullable | free text, same rationale as pr_assignee |
| content_date_started     | date, nullable | |
| content_date_posted      | date, nullable | |
| cvp_uploaded             | boolean, default false | |
| shopee_link              | string, nullable | |
| lazada_link              | string, nullable | |
| tiktok_link              | string, nullable | |
| jg_pro_shopee_link       | string, nullable | |
| jg_pro_lazada_link       | string, nullable | |
| shopify_link             | string, nullable | |
| cinepro_link             | string, nullable | |
| lzd_brand_mall_link      | string, nullable | |
| shp_brand_mall_link      | string, nullable | |
| tt_brand_mall_link       | string, nullable | |
| created_by               | bigint FK → users, nullable | |
| created_at / updated_at  | timestamp | |

**Not stored** (computed via Eloquent accessors, matching the sheet's formulas):
- `pr_sla` = `pr_date_completed - pr_date_started` in days (null if either date missing)
- `content_sla` = `content_date_posted - pr_date_completed` in days (null if either date missing) — mirrors sheet formula `U = T - N`
- `content_status` = `PENDING` if `content_date_started` set but `content_date_posted` not, else `DONE`/`—`
- `posted` (bool) = `content_date_posted !== null`
- `pr_month` / `content_month` = derived from `pr_date_started` / `content_date_started` for grouping (no stored column)

### Migration
- Create `skus` table (new migration).

### Historical import
- One-time Artisan command `sku:import` reads a JSON extract of the workbook's `PR x Content` sheet (rows 5+, columns D onward, skipping the ~180 blank rows) from `database/seeders/data/sku_import.json`, and bulk-inserts into `skus`.
- The JSON extract is generated once via a Python/openpyxl script (already available in the environment) since PhpSpreadsheet is not currently a project dependency and adding it solely for a one-time import is unnecessary (YAGNI). The generating script is not committed as project tooling — the JSON output is committed, the extraction is a one-off step.
- Import is idempotent-safe to re-run only if the table is empty (command aborts with a message if `skus` already has rows, to avoid duplicate imports).

---

## Access control

New middleware group reusing the existing `NotAnalystMiddleware` (already blocks the `analyst` role) applied to all SKU Management routes. No role sees these pages if `role === 'analyst'`.

### Permission matrix
| Role | Create row | Edit PR fields | Edit Content fields | View SKU Tracker | View SLA & Weekly Output |
|---|---|---|---|---|---|
| Researcher | ✅ | ✅ | ❌ | ✅ | ✅ |
| Content | ❌ | ❌ | ✅ | ✅ | ✅ |
| Graphics | ❌ | ❌ | ❌ | ✅ (read-only) | ✅ |
| Backend | ✅ | ✅ | ✅ | ✅ | ✅ |
| Manager / Head | ✅ | ✅ | ✅ | ✅ | ✅ |
| Analyst | — | — | — | blocked | blocked |

"PR fields" = brand, sku, variant, pr_file_location, pr_assignee, pr_status, ready_for_cvp, remarks, pr_date_started, pr_date_completed.
"Content fields" = content_assignee, content_date_started, content_date_posted, cvp_uploaded, all 10 marketplace link fields.

SLA & Weekly Output has no editable fields for anyone — it's fully derived.

---

## Routes

```
GET  /sku-tracker                 SkuController@index
POST /sku-tracker                 SkuController@store        (create row — researcher/backend/manager/head only)
PUT  /sku-tracker/{sku}           SkuController@update        (field-level permission enforced server-side per matrix above)
GET  /sla-weekly-output           SkuController@slaWeeklyOutput
```

All routes behind `auth` + `NotAnalystMiddleware`.

---

## Controller — `SkuController`

### `index()`
- Query params: `month` (filter by pr_date_started or content_date_started month, defaults to current month), `status`, `posted` (bool filter), `brand` (search).
- Loads paginated `skus` rows matching filters.
- KPI cards (matching existing KPI card format — icon, big number, colored top border): Total SKUs, Posted count, Avg PR SLA, Avg Content SLA.
- Passes current user's effective permissions (can_create, can_edit_pr, can_edit_content) to the view based on role.

### `store(Request $request)`
- Guarded: 403 if role not in {researcher, backend, manager, head}.
- Validates brand, sku required; other PR fields optional.

### `update(Request $request, Sku $sku)`
- Field-level guard: strips/rejects PR-field changes unless role in {researcher, backend, manager, head}; strips/rejects Content-field changes unless role in {content, backend, manager, head}.
- Graphics gets 403 on any write attempt.

### `slaWeeklyOutput()`
- Query params: `month_a`, `month_b` (defaults: most recent month with data vs. the one before it).
- For each month, groups matching `skus` rows by ISO week number (via `pr_date_started`/`content_date_started` as in the sheet), computes average PR SLA and average Content SLA per week.
- Computes % change per week between month_a and month_b (mirrors sheet's `(N-O)/N` formula).
- Read-only view, no write actions.

---

## Model — `Sku`

```php
fillable: [brand, sku, variant, pr_file_location, pr_assignee, pr_status,
  ready_for_cvp, remarks, pr_date_started, pr_date_completed,
  content_assignee, content_date_started, content_date_posted, cvp_uploaded,
  shopee_link, lazada_link, tiktok_link, jg_pro_shopee_link, jg_pro_lazada_link,
  shopify_link, cinepro_link, lzd_brand_mall_link, shp_brand_mall_link,
  tt_brand_mall_link, created_by]
casts: [pr_date_started => 'date', pr_date_completed => 'date',
  content_date_started => 'date', content_date_posted => 'date',
  ready_for_cvp => 'boolean', cvp_uploaded => 'boolean']

accessors: getPrSlaAttribute(), getContentSlaAttribute(),
  getContentStatusAttribute(), getPostedAttribute()
```

---

## UI

### SKU Tracker
- KPI row (4 cards) at top.
- Filter bar: month dropdown, status filter, posted toggle, brand/SKU search.
- Table: Brand, SKU, Variant, PR Assignee, PR Status, PR SLA, Content Assignee, Content Status, Content SLA, Posted (chip), links (icon group), CVP Uploaded.
- Row click opens an edit panel/modal; fields render disabled/read-only based on the current user's permission (PR fields disabled for content-only users, Content fields disabled for researcher-only users, everything disabled for graphics).
- "Add SKU" button visible only to researcher/backend/manager/head.
- Duplicate SKU warning (non-blocking) shown inline if the entered SKU matches an existing row, matching the sheet's soft "Duplicate Checker" behavior.

### SLA and Weekly Output
- Two month dropdowns (Month A / Month B) to pick the comparison, defaulting to latest vs. previous available month.
- Table or chart grouped by ISO week: Avg PR SLA (Month A), Avg PR SLA (Month B), % change; same for Content SLA.
- No edit affordances anywhere on this page.

---

## Navigation

Add a "SKU Management" group with two links (SKU Tracker, SLA and Weekly Output) to:
1. **Member sidebar** (`resources/views/components/sidebar.blade.php`, `else` block) — gated by `$role !== 'analyst'` — visible to content, researcher, graphics, backend.
2. **Admin Panel sidebar** (`isAdmin` block) — visible unconditionally there, since only manager/head reach that block and both are permitted.

---

## Out of scope
- No migration of the other 14 sheets in the workbook (Overview, Calculator, Red Prio SKU CO Analyst, etc.) — only `PR x Content` and `SLA & WEEKLY OUTPUT` per this request.
- No live Google Sheets sync — the imported data is a one-time snapshot; all future updates happen in-app.
- No notifications on SKU changes.
- No CSV/XLSX export (can be added later if needed).
- No approval workflow for `ready_for_cvp` / `cvp_uploaded` beyond a simple checkbox toggle.
