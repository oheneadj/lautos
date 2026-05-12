# DPC Design System

Complete reference for building UI in this codebase. All components exist as Blade components — always use them over hand-rolled HTML.

---

## ⚠ Dead Utilities Warning

The following class names appear in component source code but **produce no CSS output** — Tailwind does not recognise them and they are silently ignored:

`shadow-dp-lg`, `text-dp-sm`, `text-dp-xs`, `text-dp-success`, `text-dp-warning`, `text-dp-danger`, `text-dp-info`, `text-dp-white`, `text-dp-text-body`, `text-dp-text-disabled`, `text-dp-rose`, `bg-success-soft`, `bg-base-200-mid`, `focus:border-dp-rose`, `focus:ring-dp-rose-soft`, `focus:ring-dp-danger-soft`, `border-dp-danger`, `border-l-dp-success`, `border-l-dp-warning`, `border-l-dp-danger`, `border-l-dp-info`, `border-t-dp-rose`, `border-t-dp-green`, `border-t-dp-warning`, `border-t-dp-info`

When writing new code, replace them with working equivalents from the table below.

---

## Colour Tokens

Defined in `resources/css/app.css`. These are the actual resolved values:

| Token | Hex | Usage |
|---|---|---|
| `primary` | `#D52518` | Red — brand primary, CTAs, links |
| `primary-content` | `#FFFFFF` | Text on primary backgrounds |
| `secondary` | *(FlyonUI default)* | Toggle-on state (amber `#F4B303` in old design doc — check FlyonUI theme) |
| `success` | `#9ABC05` | Kiwi green — confirmed, paid, active |
| `error` | `#F96015` | Orange — urgent, cancel, danger. Also used as the main admin brand orange |
| `accent` | `#FFC926` | Sunshine yellow — highlights |
| `neutral` | `#121212` | Near-black — sidebar |
| `base-100` | `#FFFFFF` | White — cards, inputs |
| `base-200` | `#F4F4F6` | Light grey — page bg, pills, inactive tabs |
| `base-300` | `#E6E8EA` | Subtle border grey |
| `base-content` | `#1C1A18` | Ink — primary text |

**Important:** `primary` is **red** (`#D52518`), not orange. The orange seen throughout the admin UI is `error` (`#F96015`). Use `text-error`, `bg-error`, `border-error` etc. when reaching for that orange.

**Hardcoded hex values used directly in views (no token):**
- `#18542A` — Forest green. Button `variant="green"`, WhatsApp CTA, authority text
- `#A31C4E` — Deep rose. "This Week" filter-active pill only
- `#FFC926` — Sunshine. Same as `accent` — use `accent` token instead

**CSS-only variables (not usable as Tailwind utilities):**
- `--text-authority: #18542A` — not a Tailwind class; use `text-[#18542A]`
- `--text-sunshine: #FFC926` — use `text-accent` instead

---

## Working Equivalents for Dead dp- Utilities

When you encounter a `dp-` class or need to replicate what it was intended to do, use these instead:

| Dead class | Working replacement |
|---|---|
| `shadow-dp-lg` | `shadow-lg` or `shadow-xl` |
| `text-dp-sm` | `text-[13px]` |
| `text-dp-xs` | `text-[11px]` |
| `text-dp-success` | `text-success` |
| `text-dp-warning` | `text-warning` |
| `text-dp-danger` | `text-error` |
| `text-dp-info` | `text-info` |
| `text-dp-text-body` | `text-base-content` |
| `text-dp-text-disabled` | `text-base-content/40` |
| `bg-success-soft` | `bg-success/10` |
| `bg-base-200-mid` | `bg-base-200` |
| `focus:border-dp-rose` | `focus:border-primary` |
| `focus:ring-dp-rose-soft` | `focus:ring-primary/20` |
| `focus:ring-dp-danger-soft` | `focus:ring-error/20` |
| `border-dp-danger` | `border-error` |
| `border-l-dp-success` | `border-l-success` |
| `border-l-dp-warning` | `border-l-warning` |
| `border-l-dp-danger` | `border-l-error` |
| `border-l-dp-info` | `border-l-info` |
| `border-t-dp-rose` | `border-t-primary` |
| `border-t-dp-green` | `border-t-[#18542A]` |
| `border-t-dp-warning` | `border-t-warning` |
| `border-t-dp-info` | `border-t-info` |

---

## Page Backgrounds

- **Auth pages:** `bg-base-200` (`#F4F4F6`) full page
- **Admin pages:** `bg-base-200` — cards sit on `bg-white`
- **Public pages:** `bg-base-100` / `bg-base-200` alternating sections

---

## Layout

### Auth Layout (`<x-layouts::auth>`)

Renders `layouts/auth/simple.blade.php`.

Structure (outer → inner):
1. Full-page centre flex: `min-h-screen bg-base-200 flex items-center justify-center p-6 md:p-10`
2. Column wrapper: `w-full max-w-[460px] flex flex-col gap-8`
3. Logo + "Go back" link above the card
4. Card: `bg-base-100 rounded-lg border border-base-content/10 shadow-lg overflow-hidden`
5. Card inner: `p-6 sm:p-10`
6. Copyright footer: `text-[10px] text-base-content/30 font-medium uppercase tracking-[0.15em]`

Logo link: `hover:opacity-80 transition-opacity duration-200`.
"Go back" link: `text-[12px] text-base-content/40 hover:text-base-content/70 font-medium transition-colors` with a `size-3.5` left-arrow icon (stroke-width 2.5).

### Admin Layout

Sidebar + main content. Pages use `space-y-6` at the root `<div>`.

---

## Typography

### Page Headings (Admin)
- Title: `text-[28px] font-semibold text-base-content leading-tight`
- Subtitle: `text-[14px] text-base-content/50 mt-1`

### Page Headings (Auth)
- Title: `text-xl font-semibold text-base-content`
- Subtitle: `text-[14px] text-base-content/50 font-medium leading-relaxed`

### Section / Card Headings
- `text-lg font-semibold text-base-content` or `text-xl font-semibold`

### Labels
- `text-[13px] font-medium text-base-content`

### Small / Meta Text
- `text-[11px] font-bold uppercase tracking-widest text-base-content/40` — table headers, stat labels, breadcrumb segments
- `text-[10px] font-bold uppercase tracking-widest text-base-content/40` — stat card labels
- `text-[12px] text-base-content/40 font-medium` — timestamps, fine print

### Hint / Help Text
- `text-[11px] text-base-content/40` (under inputs, field notes)

---

## Components

### `<x-ui.input>` — Form Input

Props: `label`, `hint`, `error`, `required` (bool)

- `type="password"` auto-wraps in show/hide toggle (eye icon `w-4 h-4`, right side)
- Error state: `border-error focus:ring-3 focus:ring-error/20`
- Error message: `text-xs text-error flex items-center gap-1` with `⚠` prefix
- Hint: `text-[11px] text-base-content/60`

Base input classes:
```
w-full px-[14px] py-[10px] text-[15px] bg-base-100 border border-base-content/10 rounded-lg
transition-all duration-120 outline-none placeholder:text-base-content/40
disabled:bg-base-200 disabled:cursor-not-allowed
focus:border-primary focus:ring-3 focus:ring-primary/20
```

**Never use:** `rounded-xl`, `rounded-full`, `shadow-inner`, `bg-[#F4F4F6]` on inputs.

---

### `<x-ui.select>` — Select Dropdown

Same label/error/hint wrapper as input. Select element:
```
w-full px-[14px] py-[10px] text-[15px] bg-base-100 border border-base-content/10 rounded-md
transition-all duration-120 outline-none
disabled:bg-base-200 disabled:cursor-not-allowed
focus:border-primary focus:ring-3 focus:ring-primary/20
```
Note: `rounded-md` (not `rounded-lg`) on the select itself.

---

### `<x-ui.textarea>` — Textarea

Identical structure to select. Default `rows="3"`.
```
w-full px-[14px] py-[10px] text-[15px] bg-base-100 border border-base-content/10 rounded-md
transition-all outline-none placeholder:text-base-content/40
focus:border-primary focus:ring-3 focus:ring-primary/20
```

---

### `<x-app.input>` (Auth-specific)

Used on all auth pages. Supports an `icon` slot:
- Icon wrapper: `absolute inset-y-0 left-0 pl-4 flex items-center text-base-content/40 pointer-events-none`
- Icon size: `w-5 h-5`
- Input gets `pl-12` when an icon is present

---

### `<x-ui.button>` — Button

Props: `variant`, `size`, `loading` (bool), `icon` (raw SVG), `href` (renders `<a>`)

**Variants:**

| Variant | Colour |
|---|---|
| `primary` | Red `#D52518` — main CTA |
| `secondary` | `bg-base-200` grey |
| `success` | `bg-success` kiwi green |
| `danger` | `bg-error` orange — destructive actions |
| `outline` | Transparent with hover tint |
| `ghost` | Transparent, hover `bg-base-200` |
| `black` | `bg-black` |
| `green` | `bg-[#18542A]` forest green |
| `accent` | `bg-accent` sunshine yellow |

**Sizes:**

| Size | Padding / Text |
|---|---|
| `sm` | `px-[12px] py-[6px] text-[11px]` · icon `w-3.5 h-3.5` |
| `md` | `px-[18px] py-[10px] text-[13px]` · icon `w-4 h-4` |
| `lg` | `px-[24px] py-[13px] text-[15px]` · icon `w-5 h-5` |
| `icon` | `p-[8px]` |
| `icon-sm` | `p-[5px]` |

Base always: `inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 focus:outline-none focus:ring-3 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap no-underline`

Loading state: spinner overlay, label hidden with `invisible`.

**Never add:** `hover:scale-[1.02]`, `hover:-translate-y-1`, arrow icons unless explicitly requested.

---

### `<x-app.button>` (Auth-specific)

Used on auth forms as `type="submit" class="w-full"`. Renders a full-width primary button.

---

### `<x-ui.badge>` — Status Badge

Uses FlyonUI `badge` classes. Props: `type`, `dot` (bool).

| Types | FlyonUI class |
|---|---|
| `success` / `confirmed` / `completed` / `paid` | `badge-success` |
| `warning` / `pending` / `preparing` | `badge-warning` |
| `danger` / `cancelled` / `failed` | `badge-error` |
| `info` / `new` | `badge-info` |
| `primary` / `brand` | `badge-primary` |
| `ghost` | `badge-ghost` |
| `neutral` | `badge-neutral` |
| `outline` | `badge-outline` |

Dot: `w-1.5 h-1.5 rounded-full` in matching colour.

**Inline status pill (without the component):**
```
px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
bg-success/10 text-success border border-success/20
```
With pulse dot: `size-1.5 rounded-full bg-success animate-pulse`

---

### `<x-ui.card>` — Content Card

Props: `padding` (`default`=`p-6`, `compact`=`p-4`, `none`=`p-0`), `accent` (`rose`, `green`, `warning`, `info`, `primary`)

```
bg-base-100 border border-base-content/10 rounded-lg shadow-sm transition-all duration-200
```

Accent adds a `border-t-3` in the matching colour (use working equivalents from the table above).

**Admin pages** use `bg-white border border-base-content/5 rounded-xl` directly — softer border, `rounded-xl` instead of `rounded-lg`.

---

### `<x-ui.modal>` — Modal / Dialog

Props: `title`, `maxWidth` (`sm`=400px, `md`=640px, `lg`=768px, `xl`=1024px), `show`, `persistent`

1. **Overlay:** `fixed inset-0 bg-black/40 backdrop-blur-[2px]` — fade transition
2. **Panel:** `bg-base-100 w-full {maxWidth} rounded-lg shadow-xl overflow-hidden` — slides up from `translate-y-2`
3. **Header:** `px-6 py-5 border-b border-base-content/10 flex items-center justify-between`
   - Title: `text-xl font-semibold text-base-content`
   - Close: `p-1 rounded-md hover:bg-base-200 text-base-content/60 hover:text-primary` · `w-5 h-5` × icon
4. **Body:** `p-6 bg-base-100`
5. **Footer slot:** `px-6 py-4 bg-base-200 border-t border-base-content/10 flex justify-end gap-3`

---

### `<x-ui.alert>` — Alert Banner

Props: `type` (`success`, `warning`, `danger`, `info`), `title`, `dismissible`

Structure: `rounded-lg py-[14px] px-[16px] flex gap-3` + left `border-l-3` accent + tinted bg.

| Type | Background | Border | Text |
|---|---|---|---|
| `success` | `bg-success/10` | `border-l-success` | `text-success` |
| `warning` | `bg-warning/10` | `border-l-warning` | `text-warning` |
| `danger` | `bg-error/10` | `border-l-error` | `text-error` |
| `info` | `bg-info/10` | `border-l-info` | `text-info` |

Icon: `w-4 h-4 flex-shrink-0`. Title: `text-[13px] font-semibold`. Body: `text-[13px] font-normal leading-relaxed`.
Dismissible ×: `p-1 -mr-2 -mt-2 hover:bg-black/5 rounded-md opacity-60 hover:opacity-100`.

---

### `<x-ui.table>` — Data Table

Props: `search` (wire model string), `filters` slot, `actions` slot, `header` slot, `footer` slot, `pagination` slot.

Outer: `bg-white border border-base-content/5 shadow-sm rounded-lg flex flex-col overflow-hidden`

**Toolbar:**
- Container: `px-4 py-4 border-b border-base-content/5 flex flex-col md:flex-row md:items-center justify-between gap-4`
- Search: `pl-9 pr-4 py-2 bg-base-200 border border-base-content/10 rounded-lg text-[13px] placeholder:text-base-content/40 focus:border-primary focus:ring-3 focus:ring-primary/20 outline-none` with magnifier icon

**Head:** `bg-base-200 border-b border-base-content/5 sticky top-0 z-10`
**TH:** `text-[11px] text-base-content/60 font-bold uppercase tracking-widest`
**Body:** `text-[13px] text-base-content divide-y divide-base-content/5`
**Footer:** `bg-base-200/30 border-t border-base-content/10 text-[11px] font-bold text-base-content/60`
**Pagination:** `px-6 py-4 border-t border-base-content/5`

---

### `<x-ui.toggle>` — Toggle Switch

Props: `label`, `value` (bool). Supports `wire:model`.

Track: `h-6 w-11 rounded-full transition-colors duration-200`
- Off: no fill (relies on thumb visibility)
- On: `bg-secondary`

Thumb: `h-[18px] w-[18px] rounded-full bg-base-100 shadow-sm mt-[3px]` — `translate-x-1` → `translate-x-5`

Label: `text-[13px] font-medium` · off `text-base-content/60` · on `text-base-content`

---

### `<x-ui.dropdown>` — Dropdown Menu

FlyonUI `dropdown` wrapper. Props: `position`.

Panel: `min-w-48 rounded-xl bg-base-100 p-1.5 shadow-xl border border-base-content/10 z-[100]`

Items via `<x-ui.dropdown.item>` — FlyonUI `dropdown-item` pattern.

---

### `<x-auth.otp-grid>` — OTP Code Input

Single wide input. Props: `wireModel`, `wireSubmit`, `wireResend`, `label`, `compact`.

```
w-full px-[14px] py-[14px] text-2xl text-center font-bold tracking-[0.4em]
bg-base-100 border border-base-content/10 rounded-lg
focus:border-primary focus:ring-3 focus:ring-primary/20
placeholder:text-base-content/20
```
Compact: `py-[10px] text-xl`. Error: `border-error focus:ring-error/20`.

Auto-submits on 6 digits. Strips non-numeric input.

---

## Admin-Specific Patterns

### Page Header

```
flex flex-col md:flex-row md:items-end justify-between gap-6
```
Left: `h1 text-[28px] font-semibold text-base-content leading-tight` + `p text-[14px] text-base-content/50 mt-1`
Right: `flex items-center gap-3` row of `<x-ui.button>`

### Breadcrumb

```
flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-base-content/60
```
Links hover `text-error` (`#F96015`). Separator: `w-3 h-3` chevron-right. Current: `text-base-content`.

### Stat Cards

```
bg-white border border-base-content/5 rounded-xl p-4 flex items-center gap-4
```
Icon box: `w-10 h-10 rounded-xl bg-{color}/10 flex items-center justify-center` · Icon: `w-5 h-5 text-{color}`
Value: `text-[20px] font-bold text-base-content`
Label: `text-[10px] font-bold uppercase tracking-widest text-base-content/40`

Colours used in admin: `error` (`#F96015`), `accent` (`#FFC926`), `success` (`#9ABC05`), `primary` (`#D52518`). Use token names, not hex, where possible.

Active stat card: `bg-error text-white`, icon box `bg-white/20`.

### Filter Pills

```
px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wide transition-colors
```
- Inactive: `bg-base-200 text-base-content/60 hover:bg-base-300`
- Active (Today): `bg-error text-white` (`#F96015`)
- Active (This Week): `bg-[#A31C4E] text-white`

### Inline Filter Inputs (table toolbar)

```
bg-base-200 border-none text-[13px] rounded-lg px-3 py-2 outline-none font-medium
focus:ring-2 focus:ring-error/30
```

### Tab Switcher

Container: `flex p-1 bg-base-200 rounded-lg border border-base-content/5`
Tab: `flex-1 py-2.5 text-[13px] font-medium rounded-md transition-all duration-200`
- Active: `bg-base-100 shadow-sm text-base-content border border-base-content/10`
- Inactive: `text-base-content/40 hover:text-base-content/60 border border-transparent`

### Card Section Header

```
flex items-center gap-2.5 mb-6
```
Icon: `w-8 h-8 rounded-full bg-error/10 text-error flex items-center justify-center`
Title: `text-[13px] font-bold uppercase tracking-widest text-base-content/50`

### Remember Me / Checkbox

```html
<label class="flex items-center gap-2 cursor-pointer group">
    <input type="checkbox" class="checkbox checkbox-sm checkbox-primary rounded">
    <span class="text-[13px] text-base-content/50 font-medium group-hover:text-base-content transition-colors">
        Remember me
    </span>
</label>
```

### Footer Nav Links (auth)

Container: `text-center pt-6 border-t border-base-content/5`
Text: `text-[13px] text-base-content/40 font-medium`
Link: `text-primary font-semibold hover:text-primary/80 transition-colors ml-1`

### Field Help Text

```
text-[11px] text-base-content/40 -mt-2 ml-1
```

---

## Transitions

- **Auth tab panels:** enter `transition duration-300 ease-out` from `opacity-0 translate-y-2` → `opacity-100 translate-y-0`
- **Modals:** enter `ease-out duration-200`, panel slides from `translate-y-2`
- **Alert dismiss:** `ease-in duration-150` opacity fade

---

## Anti-Patterns

| Never | Instead |
|---|---|
| Any `dp-` utility class | See replacement table above |
| `rounded-full` on inputs | `rounded-lg` |
| `rounded-3xl` / `rounded-[32px]` on cards | `rounded-lg` |
| `bg-[#F4F4F6] shadow-inner` on inputs | `bg-base-100 border border-base-content/10` |
| `bg-black/60 backdrop-blur-sm` on overlays | `bg-black/40 backdrop-blur-[2px]` |
| `hover:scale-[1.02]` or `hover:-translate-y-1` on buttons | Remove |
| Arrow → inside buttons | Only if explicitly requested |
| `alert alert-success` DaisyUI class | `bg-success/10 border-l-3 border-l-success rounded-lg` |
| `text-4xl font-black` page headers | `text-[28px] font-semibold` (admin) / `text-xl font-semibold` (auth) |
| `font-black uppercase tracking-[0.2em]` on labels | `text-[13px] font-medium text-base-content` |
| Decorative pill badges on page headers | Remove |
| Hand-rolled buttons | `<x-ui.button>` or `<x-app.button>` |
| `env()` in Blade | `config()` |
