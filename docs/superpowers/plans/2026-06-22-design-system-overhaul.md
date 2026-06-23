# Design System Overhaul Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Apply the Revenue-Grade Automation design from DESIGN.md across the entire Laravel + Tailwind v4 app — replacing the current neutral-dark theme with a monochrome violet-accent system.

**Architecture:** All global styles live in `resources/views/layouts/app.blade.php` as CSS variables + component classes. Individual views add page-scoped styles in `@section('styles')`. The redesign updates variables at the root, then cleans up per-view hardcoded overrides that conflict (box-shadows, oversized radii, multi-color icon palettes).

**Tech Stack:** Laravel 11, Tailwind CSS v4 (`@theme` in `app.css`), Blade templates, Font Awesome 6, Google Fonts, ApexCharts.

## Global Constraints

- Primary accent: `#5757f8` (violet) — used only for primary buttons, active nav states, and link text
- Page background/canvas: `#f5f5f5` (Frost)
- Card surface: `#ffffff` (Paper White)
- Primary text: `#202020` (Ink Black)
- Secondary/muted text: `#333333` (Carbon)
- Borders: `1px solid #202020` — no decorative shadows anywhere
- Border radius: `8px` for cards/buttons/inputs, `9999px` for pills — no intermediate values
- Heading font: Space Grotesk (Google Fonts substitute for NB International Pro), weight 500/700
- Body font: Inter (existing), weight 500/700
- No additional chromatic colors in structural UI — semantic data colors (green/red for status indicators) are allowed in data contexts only

---

### Task 1: Update CSS Design Tokens

**Files:**
- Modify: `resources/css/app.css`
- Modify: `resources/views/layouts/app.blade.php` (`:root` block only, lines 13–70)

**Interfaces:**
- Produces: Updated CSS variables that all component classes and view-level styles consume. Every downstream task depends on these tokens being set correctly first.

- [ ] **Step 1: Update `resources/css/app.css`**

Replace the entire file content with:

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
  /* Colors */
  --color-default-violet: #5757f8;
  --color-ink-black: #202020;
  --color-carbon: #333333;
  --color-frost: #f5f5f5;
  --color-paper-white: #ffffff;

  /* Typography */
  --font-nb-international-pro: 'Space Grotesk', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --font-saans-trial: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';

  /* Typography Scale */
  --text-caption: 14px;
  --leading-caption: 1.43;
  --text-body-sm: 16px;
  --leading-body-sm: 1.4;
  --text-body: 18px;
  --leading-body: 1.4;
  --text-subheading: 20px;
  --leading-subheading: 1.2;
  --text-heading-sm: 26px;
  --leading-heading-sm: 1.2;
  --tracking-heading-sm: -0.52px;
  --text-heading: 36px;
  --leading-heading: 1;
  --tracking-heading: -0.72px;
  --text-heading-lg: 48px;
  --leading-heading-lg: 1;
  --tracking-heading-lg: -0.96px;
  --text-display: 72px;
  --leading-display: 0.97;
  --tracking-display: -1.44px;

  /* Spacing */
  --spacing-4: 4px;
  --spacing-8: 8px;
  --spacing-16: 16px;
  --spacing-20: 20px;
  --spacing-24: 24px;
  --spacing-32: 32px;
  --spacing-40: 40px;
  --spacing-48: 48px;
  --spacing-80: 80px;
  --spacing-88: 88px;
  --spacing-96: 96px;
  --spacing-176: 176px;

  /* Border Radius */
  --radius-lg: 8px;
  --radius-full: 9999px;

  /* Tailwind color aliases */
  --color-background: var(--background);
  --color-foreground: var(--foreground);
  --color-card: var(--card);
  --color-card-foreground: var(--card-foreground);
  --color-primary: var(--primary);
  --color-primary-foreground: var(--primary-foreground);
  --color-secondary: var(--secondary);
  --color-secondary-foreground: var(--secondary-foreground);
  --color-muted: var(--muted);
  --color-muted-foreground: var(--muted-foreground);
  --color-accent: var(--accent);
  --color-accent-foreground: var(--accent-foreground);
  --color-destructive: var(--destructive);
  --color-destructive-foreground: var(--destructive-foreground);
  --color-border: var(--border);
  --color-input: var(--input);
  --color-ring: var(--ring);
  --color-sidebar: var(--sidebar);
  --color-sidebar-foreground: var(--sidebar-foreground);
  --color-sidebar-primary: var(--sidebar-primary);
  --color-sidebar-primary-foreground: var(--sidebar-primary-foreground);
  --color-sidebar-accent: var(--sidebar-accent);
  --color-sidebar-accent-foreground: var(--sidebar-accent-foreground);
  --color-sidebar-border: var(--sidebar-border);
  --color-success: var(--success);
  --color-warning: var(--warning);
  --color-info: var(--info);
}
```

- [ ] **Step 2: Update `:root` block in `resources/views/layouts/app.blade.php`**

In the `<style>` block (approximately lines 13–70), replace the `:root { ... }` block with:

```css
        :root {
            --radius: 8px;

            /* Design system surfaces */
            --background: #f5f5f5;
            --foreground: #202020;
            --card: #ffffff;
            --card-foreground: #202020;
            --popover: #ffffff;
            --popover-foreground: #202020;

            /* Primary = Violet */
            --primary: #5757f8;
            --primary-foreground: #ffffff;

            /* Secondary / Muted */
            --secondary: #f5f5f5;
            --secondary-foreground: #202020;
            --muted: #f5f5f5;
            --muted-foreground: #333333;
            --accent: #f5f5f5;
            --accent-foreground: #202020;

            /* Status */
            --destructive: #ef4444;
            --destructive-foreground: #ffffff;
            --success: #22c55e;
            --warning: #f59e0b;
            --info: #3b82f6;

            /* Borders & inputs */
            --border: #202020;
            --input: #202020;
            --ring: #5757f8;

            /* Sidebar */
            --sidebar: #ffffff;
            --sidebar-foreground: #202020;
            --sidebar-primary: #5757f8;
            --sidebar-primary-foreground: #ffffff;
            --sidebar-accent: #f5f5f5;
            --sidebar-accent-foreground: #202020;
            --sidebar-border: #e5e5e5;

            /* Legacy aliases */
            --white: #ffffff;
            --bg: #f5f5f5;
            --bg-card: #ffffff;
            --fg: #202020;
            --fg-secondary: #333333;
            --fg-tertiary: #737373;
            --border-strong: #202020;
            --border-light: #e5e5e5;
            --hover: #f5f5f5;
            --gray-200: #e5e5e5;
            --gray-300: #d4d4d4;
            --gray-400: #737373;
            --gray-500: #333333;
            --gray-600: #202020;
            --gray-700: #202020;
            --p-font-family-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;

            /* Accent palette — kept only for semantic data indicators */
            --indigo: #5757f8;
            --emerald: #22c55e;
            --sky: #5757f8;
            --amber: #f59e0b;
            --rose: #ef4444;
            --violet: #5757f8;
        }
```

- [ ] **Step 3: Update the Google Fonts import in `layouts/app.blade.php` (line 8)**

Replace:
```html
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```
With:
```html
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```

- [ ] **Step 4: Run the build to verify no syntax errors**

```bash
npm run build
```
Expected: Build completes with no errors.

- [ ] **Step 5: Commit**

```bash
git add resources/css/app.css resources/views/layouts/app.blade.php
git commit -m "feat: apply Revenue-Grade Automation design tokens"
```

---

### Task 2: Update Global Component Styles in Layout

**Files:**
- Modify: `resources/views/layouts/app.blade.php` (component styles block, lines ~72–472)

**Interfaces:**
- Consumes: CSS variables from Task 1
- Produces: Updated button, input, sidebar, nav, modal, and utility component styles

- [ ] **Step 1: Update `body` and heading styles (around line 73)**

Replace:
```css
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--foreground);
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }
        h1, h2, h3, h4, h5, h6 { color: var(--foreground); font-weight: 600; line-height: 1.3; }
```
With:
```css
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--foreground);
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', 'Inter', sans-serif;
            color: var(--foreground);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
```

- [ ] **Step 2: Update sidebar active state to use violet (around line 129)**

Replace:
```css
        .sidebar-nav a.active { background: var(--sidebar-accent); color: var(--sidebar-foreground); font-weight: 600; }
```
With:
```css
        .sidebar-nav a.active { background: var(--primary); color: var(--primary-foreground); font-weight: 600; }
```

- [ ] **Step 3: Update dropdown-nav active state (around line 140)**

Replace:
```css
        .dropdown-nav a.active { background: var(--sidebar-accent); color: var(--sidebar-foreground); font-weight: 600; }
```
With:
```css
        .dropdown-nav a.active { background: var(--primary); color: var(--primary-foreground); font-weight: 600; }
```

- [ ] **Step 4: Update top-header brand-icon to violet (around line 188)**

Replace:
```css
        .top-header .logo-section .brand-icon {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary-foreground); font-weight: 700; font-size: 12px;
        }
```
With (primary is now violet, so this is correct — but the sidebar brand-icon also needs to change):
```css
        .top-header .logo-section .brand-icon {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary-foreground); font-weight: 700; font-size: 12px;
        }
        .sidebar-brand .brand-icon {
            background: var(--primary);
            color: var(--primary-foreground);
        }
```

- [ ] **Step 5: Update `.btn-primary` to remove opacity hover and ensure violet (around line 278)**

Replace:
```css
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: var(--primary); color: var(--primary-foreground);
            border: none; border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.9; }
```
With:
```css
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: var(--primary); color: var(--primary-foreground);
            border: 1px solid var(--primary); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.88; }
```

- [ ] **Step 6: Update `.btn-secondary` (ghost button) border to ink-black (around line 286)**

Replace:
```css
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: var(--secondary); color: var(--secondary-foreground);
            border: 1px solid var(--border); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-secondary:hover { background: var(--accent); }
```
With:
```css
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: transparent; color: var(--foreground);
            border: 1px solid var(--foreground); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-secondary:hover { background: var(--secondary); }
```

- [ ] **Step 7: Update `.btn-flat-primary` and `.btn-flat-secondary` (around lines 295–311)**

Replace:
```css
        .btn-flat-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; background: var(--primary); color: var(--primary-foreground);
            border: none; border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn-flat-primary:hover { opacity: 0.9; }

        .btn-flat-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; background: var(--secondary); color: var(--secondary-foreground);
            border: 1px solid var(--border); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-flat-secondary:hover { background: var(--accent); }
```
With:
```css
        .btn-flat-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; background: var(--primary); color: var(--primary-foreground);
            border: 1px solid var(--primary); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn-flat-primary:hover { opacity: 0.88; }

        .btn-flat-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; background: transparent; color: var(--foreground);
            border: 1px solid var(--foreground); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-flat-secondary:hover { background: var(--secondary); }
```

- [ ] **Step 8: Update `.input-flat` focus ring to violet (around line 321)**

Replace:
```css
        .input-flat {
            width: 100%; height: 40px; padding: 0 12px;
            background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-size: 14px; color: var(--foreground);
            outline: none; transition: border-color 0.15s;
        }
        .input-flat:focus { border-color: var(--ring); }
        .input-flat::placeholder { color: var(--muted-foreground); }
```
With:
```css
        .input-flat {
            width: 100%; height: 40px; padding: 0 12px;
            background: var(--card); border: 1px solid var(--border-light); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-size: 14px; color: var(--foreground);
            outline: none; transition: border-color 0.15s;
        }
        .input-flat:focus { border-color: var(--primary); }
        .input-flat::placeholder { color: var(--muted-foreground); }
```

- [ ] **Step 9: Update `.filter-pill` active state to violet (around line 444)**

Replace:
```css
        .filter-pill.active { background: var(--primary); border-color: var(--primary); color: var(--primary-foreground); }
```
With (already correct since `--primary` is now violet — no change needed, but verify):
```css
        .filter-pill { 
            padding: 0.25rem 0.625rem; border-radius: 9999px;
            font-family: var(--p-font-family-sans); font-size: 0.75rem; font-weight: 600;
            cursor: pointer; transition: all 0.15s; border: 1px solid var(--foreground);
            background: transparent; color: var(--foreground);
        }
        .filter-pill:hover  { background: var(--secondary); }
        .filter-pill.active { background: var(--primary); border-color: var(--primary); color: var(--primary-foreground); }
```

- [ ] **Step 10: Update `select` focus ring to violet (around line 410)**

Replace:
```css
        select:focus  { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
```
With:
```css
        select:focus  { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(87,87,248,0.12); }
```

- [ ] **Step 11: Build and verify**

```bash
npm run build
```
Expected: Build completes, no errors.

- [ ] **Step 12: Commit**

```bash
git add resources/views/layouts/app.blade.php
git commit -m "feat: update global component styles for monochrome+violet design"
```

---

### Task 3: Update Login Page

**Files:**
- Modify: `resources/views/auth/login.blade.php`

**Interfaces:**
- Consumes: CSS variables from Task 1 (violet primary, frost background, ink text)
- Produces: Login page visually aligned with new design — violet brand icon, frost canvas, no decorative circle blobs

- [ ] **Step 1: Replace the `@section('styles')` block in login.blade.php**

Replace the entire styles block (lines 9–141):
```html
@section('styles')
<style>
    body { background: var(--background); }

    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        width: 100%;
        max-width: 440px;
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: 8px;
        padding: 3rem 2.5rem;
    }

    .login-brand {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .login-brand .icon {
        width: 64px;
        height: 64px;
        background: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'Space Grotesk', sans-serif;
    }

    .login-brand h3 {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -0.52px;
        line-height: 1.2;
        margin-bottom: 4px;
        color: var(--foreground);
    }

    .login-brand p {
        color: var(--muted-foreground);
        font-weight: 500;
        font-size: 14px;
    }

    .login-form .field {
        margin-bottom: 20px;
    }

    .login-form .input-icon-wrap {
        position: relative;
    }

    .login-form .input-icon-wrap .icon-pos {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted-foreground);
        font-size: 14px;
        pointer-events: none;
    }

    .login-form .input-icon-wrap .input-flat {
        padding-left: 44px;
    }

    .login-form .btn-submit {
        width: 100%;
        height: 48px;
        margin-top: 8px;
    }

    .login-footer {
        text-align: center;
        margin-top: 32px;
        color: var(--muted-foreground);
        font-size: 12px;
        font-weight: 500;
    }

    .login-footer i {
        margin-right: 4px;
    }

    .error-msg {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
</style>
@endsection
```

- [ ] **Step 2: Verify the login page renders correctly**

Start the dev server (`php artisan serve`) and open `/login` in a browser. Confirm:
- Background is `#f5f5f5` (Frost)
- Card is white with subtle border
- Brand icon is violet (`#5757f8`)
- Heading uses Space Grotesk with tight tracking
- No decorative circles visible
- Submit button is violet

- [ ] **Step 3: Commit**

```bash
git add resources/views/auth/login.blade.php
git commit -m "feat: redesign login page for Revenue-Grade design system"
```

---

### Task 4: Update User Dashboard Page

**Files:**
- Modify: `resources/views/dashboard.blade.php`

**Interfaces:**
- Consumes: CSS variables from Task 1
- Produces: Dashboard welcome banner using ink surface (#202020) instead of gradient, stat cards without shadows, 8px radii throughout

- [ ] **Step 1: Read the full dashboard.blade.php to see all styles**

Read `resources/views/dashboard.blade.php` lines 1–200.

- [ ] **Step 2: Replace the `@section('styles')` block**

The current styles block has multiple issues:
- `.welcome-banner` uses an inline gradient set per-page (via `style=` on the element)  
- `border-radius: 12px` throughout (should be 8px)
- `box-shadow` on stat and quick-link hovers
- Icon radii: `border-radius: 12px`, `border-radius: 10px` (should be 8px)

Replace the entire `@section('styles')` block with:

```html
@section('styles')
<style>
    .welcome-banner {
        border-radius: 8px;
        padding: 2.5rem;
        background: var(--foreground);
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid var(--foreground);
    }
    .welcome-banner h2 { color: white; font-size: 1.5rem; margin-bottom: 0.375rem; position: relative; z-index: 1; font-weight: 700; }
    .welcome-banner p { color: rgba(255,255,255,0.75); font-weight: 500; font-size: 0.9rem; margin: 0; position: relative; z-index: 1; }
    .welcome-banner .wb-date { position: absolute; top: 2rem; right: 2.5rem; text-align: right; z-index: 1; }
    .welcome-banner .wb-date .wd-day { font-size: 2rem; font-weight: 700; line-height: 1; font-family: 'Space Grotesk', sans-serif; }
    .welcome-banner .wb-date .wd-month { font-size: 0.8rem; font-weight: 600; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.08em; }

    .section-divider { display: flex; align-items: center; gap: 0.75rem; margin: 2rem 0 1rem; }
    .section-divider .sd-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; background: var(--primary); font-size: 0.75rem; flex-shrink: 0; }
    .section-divider h4 { font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; font-family: 'Space Grotesk', sans-serif; }
    .section-divider .sd-line { flex: 1; height: 1px; background: var(--border-light); }

    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 0.25rem; }
    .stat-card { background: var(--card); border-radius: 8px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: border-color 0.2s; border: 1px solid var(--border-light); }
    .stat-card:hover { border-color: var(--foreground); }
    .stat-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--primary); color: white; font-size: 1.1rem; flex-shrink: 0; }
    .stat-count { font-size: 1.75rem; font-weight: 700; line-height: 1; margin-bottom: 0.125rem; font-family: 'Space Grotesk', sans-serif; }
    .stat-label { font-size: 0.8rem; font-weight: 600; color: var(--muted-foreground); }

    .chart-section { background: var(--card); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-light); }
    .chart-section #weeklyChart { width: 100% !important; }

    .quick-section { background: var(--card); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-light); }
    .quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .quick-link { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: var(--background); border-radius: 8px; text-decoration: none; color: var(--foreground); transition: all 0.2s; border: 1px solid var(--border-light); }
    .quick-link:hover { background: var(--primary); color: white; border-color: var(--primary); }
    .quick-link:hover .ql-icon { background: rgba(255,255,255,0.2); }
    .ql-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background: var(--primary); color: white; flex-shrink: 0; }
    .ql-label { font-weight: 600; font-size: 0.875rem; }

    .ref-section { margin-bottom: 2rem; }
    .ref-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .ref-card { background: var(--card); border-radius: 8px; padding: 1.5rem; border: 1px solid var(--border-light); text-align: center; text-decoration: none; color: var(--foreground); transition: border-color 0.2s; display: block; }
    .ref-card:hover { border-color: var(--foreground); }
    .ref-card .rc-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1rem; background: var(--primary); color: white; }
    .ref-card .rc-label { font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem; }
    .ref-card .rc-sub { font-size: 0.75rem; color: var(--muted-foreground); }
</style>
@endsection
```

- [ ] **Step 3: In the HTML section, find the welcome-banner div and remove the inline gradient style**

Find and update any `style="background: linear-gradient(...)` or `style="background: var(--indigo)` etc on the `.welcome-banner` div. The new CSS class handles the background. Set it to just `class="welcome-banner"` with no inline background style.

Example: if there's `<div class="welcome-banner" style="background: linear-gradient(135deg, var(--indigo) 0%, var(--violet) 100%);">`, change it to:
```html
<div class="welcome-banner">
```

- [ ] **Step 4: Verify dashboard renders correctly**

Navigate to `/dashboard`. Confirm:
- Welcome banner is dark ink (#202020) with white text, no gradient
- Stat cards have white background, light border, violet icon squares
- No box shadows anywhere on hover
- All radii are 8px

- [ ] **Step 5: Commit**

```bash
git add resources/views/dashboard.blade.php
git commit -m "feat: update user dashboard styles for design system"
```

---

### Task 5: Update Admin Dashboard Page

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`

**Interfaces:**
- Consumes: CSS variables from Task 1
- Produces: Admin dashboard with violet-unified KPI icons, no box-shadows, 8px radii

- [ ] **Step 1: Read the full admin dashboard styles**

Read `resources/views/admin/dashboard.blade.php` lines 1–160.

- [ ] **Step 2: Replace the entire `@section('styles')` block**

```html
@section('styles')
<style>
    /* KPIs */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.75rem; }
    .kpi-card {
        background: var(--card); border-radius: 8px; padding: 1.5rem;
        border: 1px solid var(--border-light); transition: border-color 0.2s; position: relative;
    }
    .kpi-card:hover { border-color: var(--foreground); }
    .kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .kpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
    .kpi-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: white; background: var(--primary); }
    .kpi-value { font-size: 1.75rem; font-weight: 700; line-height: 1; margin-bottom: 0.375rem; font-family: 'Space Grotesk', sans-serif; }
    .kpi-bottom { display: flex; align-items: center; justify-content: space-between; }
    .kpi-sub { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }
    .kpi-spark { width: 60px; height: 24px; }
    .kpi-trend { display: inline-flex; align-items: center; gap: 3px; font-weight: 700; font-size: 0.7rem; padding: 3px 8px; border-radius: 9999px; }
    .kpi-trend.up   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .kpi-trend.down { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .kpi-card[data-color="indigo"]  { border-top: 2px solid var(--primary); }
    .kpi-card[data-color="emerald"] { border-top: 2px solid var(--success); }
    .kpi-card[data-color="sky"]     { border-top: 2px solid var(--primary); }
    .kpi-card[data-color="amber"]   { border-top: 2px solid var(--warning); }
    .kpi-card[data-color="rose"]    { border-top: 2px solid var(--destructive); }
    .kpi-icon[data-color="emerald"] { background: var(--success); }
    .kpi-icon[data-color="amber"]   { background: var(--warning); }
    .kpi-icon[data-color="rose"]    { background: var(--destructive); }

    /* Team Health */
    .health-card {
        background: var(--card); border-radius: 8px; border: 1px solid var(--border-light);
        padding: 1.5rem; margin-bottom: 1.75rem;
    }
    .health-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
    .health-header h4 { font-size: 0.9rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; font-family: 'Space Grotesk', sans-serif; }
    .health-header .pulse { width: 8px; height: 8px; border-radius: 50%; background: var(--success); animation: pulse 2s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    .health-bar-wrap { width: 100%; height: 6px; background: var(--border-light); border-radius: 9999px; overflow: hidden; margin-bottom: 1rem; }
    .health-bar { height: 100%; border-radius: 9999px; transition: width 1s ease; }
    .health-avatars { display: flex; align-items: center; gap: 0; }
    .health-avatar {
        width: 36px; height: 36px; border-radius: 50%; border: 2px solid var(--card);
        display: block; transition: transform 0.2s;
    }
    .health-avatar.logged  { border-color: var(--success); }
    .health-avatar.pending { border-color: var(--destructive); opacity: 0.5; }

    .avatar-tip-wrap { position: relative; display: inline-flex; margin-left: -10px; }
    .avatar-tip-wrap:first-child { margin-left: 0; }
    .avatar-tip-wrap:hover { z-index: 10; }
    .avatar-tip-wrap:hover .health-avatar { transform: translateY(-2px); }
    .avatar-tip-wrap::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        background: var(--foreground);
        color: var(--card);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 600;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s;
        z-index: 10;
    }
    .avatar-tip-wrap:hover::after { opacity: 1; }

    .health-legend { display: flex; gap: 1.25rem; margin-top: 0.75rem; font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }
    .health-legend .dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 4px; }

    /* Role Overview */
    .role-ov-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .role-ov-card {
        background: var(--card); border-radius: 8px; padding: 1.25rem; border: 1px solid var(--border-light);
        transition: border-color 0.2s;
    }
    .role-ov-card:hover { border-color: var(--foreground); }

    /* Quick Links */
    .ql-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .quick-link {
        display: flex; align-items: center; gap: 0.875rem;
        background: var(--card); border-radius: 8px; padding: 1.25rem; text-decoration: none;
        color: var(--foreground); border: 1px solid var(--border-light); transition: border-color 0.2s;
    }
    .quick-link:hover { border-color: var(--foreground); }
    .ql-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: white; background: var(--primary); flex-shrink: 0; }

    /* Activity Feed */
    .activity-card { background: var(--card); border-radius: 8px; border: 1px solid var(--border-light); overflow: hidden; }
    .activity-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border-light); transition: background 0.15s; }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: var(--background); }
    .activity-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
    .activity-line { width: 1px; flex: 1; background: var(--border-light); margin-top: 6px; min-height: 20px; }

    /* Welcome bar */
    .wb-bar {
        background: var(--card); border: 1px solid var(--border-light); border-radius: 8px;
        display: flex; align-items: center; gap: 1.5rem; padding: 1.25rem 1.5rem; margin-bottom: 1.75rem;
    }
    .wb-stat-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted-foreground); margin-bottom: 2px; }
    .wb-stat-val { font-size: 1.1rem; font-weight: 700; font-family: 'Space Grotesk', sans-serif; }
    .wb-divider { width: 1px; height: 36px; background: var(--border-light); }
</style>
@endsection
```

- [ ] **Step 3: Remove inline styles from the RDO/warning banner in HTML (around line 160)**

Find the inline warning div:
```html
    <div style="display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.25rem;background:#fef9ec;border:1px solid #fde68a;border-radius:10px;color:#92400e;...">
```
Replace the `border-radius:10px` with `border-radius:8px` and keep the amber warning colors (these are semantic data colors, acceptable).

- [ ] **Step 4: Remove hardcoded `color:#d97706` on `wb-stat-val` for RDO (around line 175)**

Find `<div class="wb-stat-val" style="font-size:1.25rem;color:#d97706;">RDO</div>` — keep the amber color for the RDO semantic indicator (amber is acceptable as a warning semantic color).

- [ ] **Step 5: Update the quick-link icon at the bottom**

Find `<div class="ql-icon" style="background: var(--indigo);">` — since `--indigo` now maps to `--primary` (violet), this is correct. No change needed.

- [ ] **Step 6: Verify admin dashboard**

Navigate to `/admin`. Confirm:
- KPI cards: white background, subtle border, violet top accent line, violet icons
- Health bar is styled correctly
- No box shadows anywhere
- Quick link icons are violet

- [ ] **Step 7: Commit**

```bash
git add resources/views/admin/dashboard.blade.php
git commit -m "feat: update admin dashboard styles for design system"
```

---

### Task 6: Update Admin Daily Logs and Reports Pages

**Files:**
- Modify: `resources/views/admin/daily-logs.blade.php`
- Modify: `resources/views/admin/reports.blade.php`

**Interfaces:**
- Consumes: CSS variables from Task 1
- Produces: Daily logs and reports pages with consistent 8px radii, no box-shadows, violet active states

- [ ] **Step 1: Read daily-logs styles**

Read `resources/views/admin/daily-logs.blade.php` lines 1–160.

- [ ] **Step 2: In daily-logs.blade.php — remove box-shadows and fix radii in the styles block**

Find and make these targeted replacements:

**Remove `box-shadow` from `.dl-kpi-card` hover** (around line 24):
```css
        transition: border-color 0.2s;
```
(Remove the `box-shadow` from the `transition` property — already just border-color change, or remove `box-shadow` if present in hover rule.)

Find `.dl-kpi-card:hover` and remove any `box-shadow:` property line.

**Fix `.dl-kpi-icon` border-radius** (line 27):
```css
    .dl-kpi-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem; flex-shrink: 0; }
```
(Change `border-radius: 10px` to `border-radius: 8px` if it exists.)

**Fix `.th-icon` border-radius** (around line 74):
```css
    .th-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.7rem; }
```
(Change `border-radius: 6px` to `border-radius: 8px`.)

**Fix `.status-pill` border-radius** (around line 100):
```css
    .status-pill { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 9999px; font-size: 0.65rem; font-weight: 700; }
```
(Change `border-radius: 4px` to `border-radius: 9999px` — pills should be full radius.)

**Fix warning banner border-radius** (around line 184):
Find `border-radius:10px` in the inline RDO warning div and change to `border-radius:8px`.

**Fix calendar legend dot** (around line 419):
Change `border-radius:50%` on the Today dot to stay as 50% (circular dots are fine).

- [ ] **Step 3: Read reports.blade.php styles**

Read `resources/views/admin/reports.blade.php` lines 1–200.

- [ ] **Step 4: In reports.blade.php — remove box-shadows and fix radii**

Find `.rpt-kpi-card:hover` (or the card hover rule around line 63) and remove any `box-shadow:` property.

Fix these radii to 8px wherever they are set to 6px or 7px:
- `.rpt-kpi-icon` — `border-radius: 8px` (from 7px)
- `.rpt-section-icon` — `border-radius: 8px` (from 6px)
- `.rpt-chart-icon` — `border-radius: 8px` (from 6px)

Find the tab/nav button styles (around line 174) and update the active state. Replace the `box-shadow` in tab button focus/active:
```css
        transition: border-color 0.15s, background 0.15s, color 0.15s;
```
(Remove `box-shadow` from transition list and any hover/active box-shadow rules.)

Fix tab button border-radius from `border-radius: 7px` to `border-radius: 8px`.

- [ ] **Step 5: Build and verify both pages**

```bash
npm run build
```
Navigate to `/admin/daily-logs` and `/admin/reports`. Confirm no shadows, consistent 8px radii.

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/daily-logs.blade.php resources/views/admin/reports.blade.php
git commit -m "feat: update admin daily-logs and reports styles for design system"
```

---

### Task 7: Update Admin Users Page and Remaining Views

**Files:**
- Modify: `resources/views/admin/users.blade.php`
- Modify: `resources/views/end-of-day.blade.php`
- Modify: `resources/views/team.blade.php`
- Check (read-only): `resources/views/price-calculator.blade.php`, `resources/views/posting-procedure.blade.php`, `resources/views/data-gathering.blade.php`, `resources/views/ecommerce-requirements.blade.php`, `resources/views/important-links.blade.php`

**Interfaces:**
- Consumes: CSS variables from Task 1
- Produces: All remaining views cleaned up — user badge color, no box-shadows, pill radius for status pills

- [ ] **Step 1: Read users.blade.php**

Read `resources/views/admin/users.blade.php`.

- [ ] **Step 2: Fix the badge inline style (around line 216)**

Find:
```html
<span style="display:inline-flex;align-items:center;margin-left:5px;padding:2px 7px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:4px;font-size:0.6rem;font-weight:700;color:#0369a1;white-space:nowrap;">{{ $u->badge }}</span>
```
Replace with:
```html
<span style="display:inline-flex;align-items:center;margin-left:5px;padding:2px 8px;background:#f0f0ff;border:1px solid #a5a5fc;border-radius:9999px;font-size:0.6rem;font-weight:700;color:#5757f8;white-space:nowrap;">{{ $u->badge }}</span>
```

- [ ] **Step 3: Read end-of-day.blade.php and fix border-radii**

Read `resources/views/end-of-day.blade.php`. Find and fix:
- `border-radius: 4px` → `border-radius: 8px` for card elements (not pills)
- `border-radius: 6px` → `border-radius: 8px` for card elements
- Any `box-shadow` on hover → remove

- [ ] **Step 4: Scan and quick-fix remaining views**

Run:
```bash
grep -rn "box-shadow" resources/views/
```
For each result outside of `layouts/app.blade.php`, remove the `box-shadow` line. Common pattern:
- Change `.card:hover { box-shadow: ...; }` → `.card:hover { border-color: var(--foreground); }`

Then run:
```bash
grep -rn "border-radius: [0-9]*[2-9]px\|border-radius: 1[0-9]px" resources/views/ | grep -v "50%\|9999\|var(--radius"
```
For non-pill, non-circle elements with radius > 8px, change to `8px`.

- [ ] **Step 5: Build and do a final sweep**

```bash
npm run build
```
Visit each page of the app and confirm:
- No box-shadows visible
- Page background is `#f5f5f5`
- Cards are `#ffffff`
- Primary buttons and active sidebar items are violet `#5757f8`
- All borders are subtle (`#e5e5e5`) or ink (`#202020`) — no colored borders except semantic ones
- Headings use Space Grotesk

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/users.blade.php resources/views/end-of-day.blade.php resources/views/team.blade.php
git commit -m "feat: complete design system rollout across all remaining views"
```

---

## Self-Review

### Spec coverage
| DESIGN.md requirement | Task covering it |
|---|---|
| Violet #5757f8 as primary | Task 1 (`:root --primary`) |
| Frost #f5f5f5 page background | Task 1 (`:root --background`) |
| Paper White #ffffff cards | Task 1 (`:root --card`) |
| Ink Black #202020 text + borders | Task 1 (`:root --foreground, --border`) |
| Carbon #333333 muted text | Task 1 (`:root --muted-foreground`) |
| Space Grotesk heading font | Tasks 1 + 2 (font import + h1-h6 rule) |
| 8px border radius | Tasks 1 + 2 + 4–7 (`:root --radius` + per-view fixes) |
| No box shadows | Tasks 2 + 4 + 5 + 6 + 7 (remove all `box-shadow` rules) |
| Violet primary buttons | Task 2 (btn-primary, btn-flat-primary) |
| Ghost buttons: ink border | Task 2 (btn-secondary, btn-flat-secondary) |
| Active nav/sidebar: violet | Task 2 (`.sidebar-nav a.active`) |
| Filter pills: violet active | Task 2 (`.filter-pill.active`) |
| Login page: frost canvas, violet icon | Task 3 |
| Welcome banner: ink surface | Task 4 |
| KPI cards: no shadows, violet icons | Task 5 |
| Status pills: 9999px radius | Task 6 |
| Badge: violet tint | Task 7 |

### No gaps found — all spec requirements are addressed.
