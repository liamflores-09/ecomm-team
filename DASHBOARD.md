# Admin Dashboard Reference

> This document explains what each chart, KPI, and section on the Admin Dashboard displays, how data is calculated, and how to filter by role.

---

## KPI Cards

| Card | Color | What it shows |
|------|-------|---------------|
| **This Month** | Indigo | Total tasks logged across all roles for the current calendar month. The trend badge (↑/↓) compares to the same period last month. |
| **Today** | Emerald | How many non-manager members have submitted their EOD report today vs. the total active member count. The badge shows "pending" count or "Complete" when everyone is in. |
| **Avg / Person** | Sky | Total tasks logged this month divided by the number of active (non-manager) members. The sparkline underneath shows the daily total for the last 7 days. |
| **Team Size** | Rose | Total registered users. Sub-text shows the count of active (non-manager) members who contribute daily logs. |

---

## Today's Pulse

A live snapshot of who has submitted their End-of-Day (EOD) report today.

- **Green border** — member has logged in today.
- **Faded/red border** — member has not submitted yet.
- **Progress bar** — percentage of the team that has logged in.
- **Hover tooltip** — shows the member's full name and their status (Logged / Pending).

---

## Role Activity — Last 7 Days

One card per active role. Each card contains:

- **Role name + member count** — how many people are in that role.
- **Area chart** — daily task output totals for that role over the last 7 calendar days. Each data point is the **sum of all 5 task fields** (`task_1` through `task_5`) across every member in that role on that day.
- **Sunday = RDO** — Sunday labels appear in **red** on the x-axis. When you hover over a Sunday data point, the tooltip shows `"Sun (RDO)"`. No output is expected on Sundays; a zero on Sunday is normal.
- **View Reports →** — links directly to the Reports page filtered to that role.

### Role color legend

| Role | Color |
|------|-------|
| Lead | Indigo |
| Content | Sky |
| Graphics | Amber |
| Backend | Rose |
| Researcher | Emerald |

---

## Quick Actions

Navigation shortcuts for the four most common admin tasks:

| Action | Destination |
|--------|-------------|
| Manage Users | Add, edit, or deactivate team members |
| Daily Logs | Browse & review all submitted EOD reports |
| Reports | Weekly & monthly task analytics per role |
| The Team | Browse member profiles & roles |

---

## Recent Activity

A chronological feed of the last system events:

| Color | Event type |
|-------|-----------|
| Green | EOD submitted |
| Blue | EOD updated |
| Red | EOD deleted / user deleted |
| Purple | New user created |
| Amber | User profile updated |

---

## Sunday = RDO (Rest Day)

Sundays are **Regular Days Off**. Across the system:

- If today is Sunday, a notice banner appears at the top of the dashboard.
- Sunday labels on all charts are colored **red** and tooltip shows `"(RDO)"`.
- No EOD submission is expected from any team member on Sundays.
- A zero task count on Sunday does **not** negatively affect performance averages.

---

## Filtering by Role

Both the **Daily Logs** and **Reports** pages support per-role filtering via the **sidebar**:

1. Navigate to **Daily Logs** or **Reports**.
2. The sidebar shows a **"Filter by Role"** section with links for each role: All Roles, Content, Lead, Researcher, Graphics, Backend.
3. All data on the page (tables, charts, summaries) updates to show only that role's members and logs.

From the dashboard, clicking **"View Reports →"** on any role card navigates directly to that role's filtered report view.

---

## Roles Reference

| Role | Description |
|------|-------------|
| `manager` | Admin access. Not included in EOD tracking or task counts. |
| `lead` | Team leads / PR role. |
| `content` | Content creation team. |
| `graphics` | Graphics & design team. |
| `backend` | Backend / technical team. |
| `researcher` | Research team. |
