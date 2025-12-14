# Dashboard Visual Guide

## Dashboard Layout Structure

```
┌─────────────────────────────────────────────────────────────────┐
│  NAVIGATION BAR (Dark with Lime Green Accent)                   │
│  [Logo] Dashboard Projects Knowledge Users Analytics  [Actions] │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                                                                   │
│  DASHBOARD                                                        │
│                                                                   │
│  ┌────────────────────────┐  ┌────────────────────────┐         │
│  │ 💰 Total Budget        │  │ ✅ Completed Tasks     │         │
│  │ $21,339  +14% week     │  │ 21,339  +178 today     │         │
│  └────────────────────────┘  └────────────────────────┘         │
│                                                                   │
│  PROJECTS (88)                                          [+]       │
│                                                                   │
│  ┌────────────────┐ ┌────────────────┐ ┌────────────────┐      │
│  │ #Finance       │ │ #Education     │ │ #Finance       │      │
│  │ Decem App   ↗  │ │ SkyLux      ↗  │ │ DushMash    ↗  │      │
│  │ Completed: 988 │ │ Completed: 12  │ │ Completed: 32  │      │
│  │ $391,991       │ │ $51,792        │ │ $31,955        │      │
│  │ 👤👤👤 +12      │ │ 👤👤👤         │ │ 👤👤👤         │      │
│  └────────────────┘ └────────────────┘ └────────────────┘      │
│                                                                   │
│  ┌────────────────┐ ┌────────────────┐ ┌────────────────┐      │
│  │ #Healthcare    │ │ #Travel        │ │ #Logistics     │      │
│  │ Biofarm     ↗  │ │ PAD move    ↗  │ │ Getstats    ↗  │      │
│  │ Completed: 19  │ │ Completed: 35  │ │ Completed: 88  │      │
│  │ $11,538        │ │ $21,688        │ │ $92,581        │      │
│  │ 👤👤 +4         │ │ 👤👤 +2         │ │ 👤👤👤         │      │
│  └────────────────┘ └────────────────┘ └────────────────┘      │
│                                                                   │
│  ┌──────────────────────────┐  ┌──────────────────────────┐    │
│  │ Projects This Year       │  │ Yearly Profit (96%)      │    │
│  │                          │  │                          │    │
│  │ Average tasks value      │  │  ██                      │    │
│  │ $568,338                 │  │  ██  ██                  │    │
│  │                          │  │  ██  ██  ██  ██          │    │
│  │ Average tasks/project    │  │ ─┼──┼──┼──┼──┼──┼──┼─   │    │
│  │ 89.3                     │  │  M  A  M  J  J  A  S     │    │
│  │                          │  │                          │    │
│  │ New projects             │  │                          │    │
│  │ 76                       │  │                          │    │
│  └──────────────────────────┘  └──────────────────────────┘    │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Color Scheme

### Background

```
Background: Dark navy/slate gradient
  - slate-950 (#020617) - Darkest
  - slate-900 (#0f172a) - Dark
```

### Cards

```
Card Background: slate-800/50 with backdrop blur
Border: slate-700 (#334155)
```

### Accent Colors

```
Primary: lime-400 (#a3e635) - Buttons, highlights
```

### Project Categories

```
Finance:    Blue     #4169E1  ██████
Education:  Orange   #FF7F3F  ██████
Healthcare: Green    #3EAF7C  ██████
Travel:     Red      #E74C3C  ██████
Logistics:  Cyan     #17A2B8  ██████
```

## Component Breakdown

### 1. Navigation Bar

-   Height: 64px (h-16)
-   Background: slate-900/50 with backdrop blur
-   Logo: Lime-green square with icon
-   Active state: Lime-400 pill background
-   Right actions: Invite, Notifications, Avatar

### 2. Statistics Cards

-   Layout: 2 columns on desktop
-   Background: Dark with blur effect
-   Icon badges: Circular with lime accent
-   Change indicators: Green (+) or Red (-)

### 3. Project Cards

-   Layout: 3 columns on desktop, responsive
-   Gradient backgrounds based on category
-   Rounded corners: 1rem (rounded-2xl)
-   Hover: Scale 1.05 with shadow
-   Elements:
    -   Category tag (top-left)
    -   Arrow icon (top-right)
    -   Project name (bold, large)
    -   Tasks count
    -   Budget (very large, bold)
    -   Avatar group (overlapping)
    -   Member count badge

### 4. Avatar Groups

-   Overlapping circles: -space-x-2
-   Border: 2px white/20%
-   Size: 32px (w-8 h-8)
-   Source: UI Avatars API

### 5. Stats Section

-   2 columns: Stats on left, Chart on right
-   Dark card backgrounds
-   Large numbers in lime-400
-   Small comparison text in slate-500

### 6. Yearly Chart

-   Type: Bar chart
-   Color: Lime-400 (#a3e635)
-   Rounded bars: 8px
-   Grid: Subtle white/5%
-   Labels: Slate-400

## Typography

### Headings

```
Dashboard: 4xl (2.25rem), bold, white
Projects:  2xl (1.5rem), bold, white
Cards:     xl (1.25rem), bold, white
```

### Stats

```
Big numbers:   3xl (1.875rem), bold, white/lime-400
Small numbers: sm (0.875rem), semibold
Labels:        sm (0.875rem), slate-400
```

### Body

```
Regular text: base (1rem), white/slate-300
Muted text:   sm (0.875rem), slate-500
```

## Spacing

### Padding

```
Cards:     p-6 (1.5rem)
Container: px-4 sm:px-6 lg:px-8
Sections:  py-8 (2rem)
```

### Gaps

```
Grid:   gap-6 (1.5rem)
Flex:   space-x-4, space-y-4
```

### Border Radius

```
Cards:   rounded-2xl (1rem)
Pills:   rounded-full
Buttons: rounded-full
Bars:    8px (in Chart.js)
```

## Responsive Breakpoints

```
Mobile (<768px):
  - 1 column grid
  - Stacked stats
  - Smaller text

Tablet (768px-1024px):
  - 2 column grid
  - Side-by-side stats

Desktop (>1024px):
  - 3 column grid
  - Full layout
```

## Interactive States

### Hover Effects

```
Project Cards:
  - transform: scale(1.05)
  - shadow: 2xl
  - transition: 200ms

Buttons:
  - Background lighter
  - Text color change
```

### Loading States

```
Initial data load via Alpine.js
Skeleton loaders (not yet implemented)
```

## Data Flow

```
┌──────────────┐
│   Database   │
│   (MySQL)    │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   Laravel    │
│  Controller  │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  JSON API    │
│  Response    │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Alpine.js   │
│  Component   │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   Blade      │
│  Template    │
└──────────────┘
```

## Browser Requirements

-   ES6+ JavaScript
-   CSS Grid support
-   Fetch API support
-   CSS backdrop-filter support (for blur effects)

## Performance Notes

-   Images: Loaded from external API (UI Avatars)
-   Charts: Rendered client-side with Chart.js
-   Data: Fetched on component initialization
-   No lazy loading implemented yet
-   No image optimization yet

---

**This visual guide complements the implementation documentation.**
