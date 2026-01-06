# Quick Reference: Layout Structure

## 🎯 Current Layout Design

```
┌─────────────────────────────────────────────────────────────┐
│                                                               │
│  ┌────────────────────────────────────────────────────────┐  │
│  │ Header (Theme, Nepali Date, Notifications, Profile)   │  │
│  └────────────────────────────────────────────────────────┘  │
│  ┌──┬──────────────────────────────────────────────────────┐  │
│  │  │                                                       │  │
│  │  │          Main Content Area (flex-1)                  │  │
│  │  │          - Service Leads                             │  │
│  │  │          - Finance Reports                           │  │
│  │  │          - HRM Dashboards                            │  │
│  │  │          - etc.                                      │  │
│  │  │                                                       │  │
│  │  │                                                       │  │
│  │  │                                                       │  │
│  └──┴──────────────────────────────────────────────────────┘  │
│  └──────────────────────────────────────────────────────────┘  │
│
│  Icon Bar (w-20=80px) | Detail Panel (w-80=320px, hidden)     │
│  FIXED POSITION LEFT-0                                         │
└─────────────────────────────────────────────────────────────┘
```

## 🧩 Component Breakdown

### 1. Icon Sidebar (Fixed, w-20 = 80px)

**Always Visible**

```blade
<div x-data="{ activeNav: null }" class="fixed left-0 top-0 h-screen z-40 flex">
    <aside class="w-20 bg-slate-900 border-r border-slate-800 flex flex-col">
        <!-- Logo -->
        <!-- Navigation Icons (5 main icons) -->
        <!-- Dashboard, Sites, Service Leads, HRM, Finance -->
    </aside>
```

**Features:**
- Fixed positioning (left: 0, top: 0)
- Dark background (#0f172a)
- 5 main navigation icons
- Hover effects on icons
- Active state highlighting

**Navigation Icons:**
1. Dashboard (home icon)
2. Sites (globe icon)
3. Service Leads (list icon)
4. HRM (people icon)
5. Finance (money icon)

### 2. Detail Panel (Slides In, w-80 = 320px)

**Hidden by Default, Shown on Icon Click**

```blade
    <div x-show="activeNav" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         @click.away="activeNav = null"
         class="w-80 bg-slate-900 border-r border-slate-800 h-screen overflow-y-auto flex-shrink-0">
        
        <!-- HRM Detail Panel -->
        <div x-show="activeNav === 'hrm'" class="p-6">
            <!-- Sections:
                - People Management (Employees, Organization)
                - Payroll & Claims
                - Leave Management
                - Communication
            -->
        </div>
        
        <!-- Finance Detail Panel -->
        <div x-show="activeNav === 'finance'" class="p-6">
            <!-- Sections:
                - Configuration (Companies, Accounts)
                - Accounting (Transactions, Sales, Purchases)
                - Sales & Purchases
                - Analysis & Reports
            -->
        </div>
    </div>
</div>
```

**Features:**
- Slides in from left with smooth animation
- `-translate-x-full` → `translate-x-0` transition
- Closes when clicking away (`@click.away`)
- Contains collapsible sections
- Scrollable content area

### 3. Main Content Area (flex-1, ml-20)

**Takes All Remaining Width**

```blade
    <div class="flex-1 flex flex-col ml-20">
        <!-- Header Section -->
        <header class="bg-white/90 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
            <!-- Theme Toggle, Nepali Date, Notifications, Profile -->
        </header>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            @yield('content')
        </main>
    </div>
```

**Features:**
- Takes remaining horizontal space (`flex-1`)
- 80px left margin for icon bar (`ml-20`)
- Flex column for vertical stacking
- Scrollable content area
- Header with controls

## 🎨 Color Scheme (Dark Mode)

| Element | Color | Hex |
|---------|-------|-----|
| Background | Slate-900 | #0f172a |
| Border | Slate-800 | #1e293b |
| Text | Slate-300 | #cbd5e1 |
| Text (Active) | White | #ffffff |
| Hover BG | Slate-800/50 | rgba(30, 41, 59, 0.5) |

## 📐 Dimensions

| Element | Width | Height | Margin |
|---------|-------|--------|--------|
| Icon Bar | w-20 (80px) | h-screen | - |
| Detail Panel | w-80 (320px) | h-screen | - |
| Logo Area | - | h-20 (80px) | - |
| Content | flex-1 | - | ml-20 (80px) |
| Header | - | auto | - |

## ✨ Interactive States

### Icon Hover
- Background: `hover:bg-slate-800/50`
- Text: `hover:text-slate-200`
- Transition: smooth

### Icon Active
- Background: `bg-slate-800`
- Text: `text-white`

### Detail Panel Open
- Slides in from left (200ms ease-out)
- Full height, scrollable
- Click away to close

### Detail Panel Close
- Slides out to left (150ms ease-in)
- Smooth opacity fade

## 🔗 Alpine.js Integration

### State Management
```javascript
x-data="{ activeNav: null }"
```

### Toggle Actions
```javascript
// Open/Close detail panel
@click="activeNav = activeNav === 'hrm' ? null : 'hrm'"

// Close on click away
@click.away="activeNav = null"

// Direct navigation (close panel)
@click.prevent="activeNav = null; window.location.href = '...'"
```

### Conditional Display
```javascript
// Show panel based on state
x-show="activeNav === 'hrm'"
x-show="activeNav === 'finance'"

// Show icon if active
:class="activeNav === 'hrm' ? 'bg-slate-800 text-white' : '...'"
```

## 🎯 Navigation Flow

1. **User clicks icon** (e.g., HRM)
   - `activeNav` set to 'hrm'
   
2. **Detail panel slides in** (animation)
   - `x-transition` triggers 200ms animation
   - Panel shows from left with opacity

3. **User sees menu items**
   - People Management (expandable)
   - Payroll & Claims (expandable)
   - Leave Management (expandable)
   - Communication (expandable)

4. **User clicks menu item**
   - Icon closes panel (`activeNav = null`)
   - Navigation happens
   - New page loads with content

5. **Or user clicks away**
   - Panel closes with `@click.away`
   - `activeNav` becomes null
   - Detail panel slides out

## 📱 Responsive Behavior

- **Desktop**: Full layout with fixed sidebar + content
- **Tablet**: Same as desktop (icon bar + detail panel)
- **Mobile**: May need adjustment (future consideration)

## 🔧 CSS Framework Integration

**Tailwind CSS Classes Used:**

- `fixed`, `left-0`, `top-0` - Fixed positioning
- `w-20`, `w-80`, `flex-1` - Widths/flex
- `flex`, `flex-col` - Flex layout
- `h-screen` - Full height
- `overflow-y-auto` - Scrollable
- `transition` - Smooth transitions
- `dark:bg-slate-900`, `dark:border-slate-800` - Dark mode
- `z-40` - Z-index stacking
- `ml-20` - Left margin
- `rounded-lg` - Border radius
- `space-y-2` - Vertical spacing
- `gap-3` - Icon-text gap
- `px-4`, `py-2` - Padding

## 📝 Key Files

1. **Layout**: [app.blade.php](../resources/views/admin/layouts/app.blade.php)
   - Main wrapper
   - Header
   - Content container

2. **Navigation**: [sidebar.blade.php](../resources/views/admin/layouts/partials/sidebar.blade.php)
   - Icon bar
   - Detail panels
   - All menu items

3. **Styling**: [tailwind.config.js](../tailwind.config.js)
   - Dark mode config
   - Color scheme
   - Spacing scales

## 🚀 Implementation Notes

- **Alpine.js v3.x** required for `x-data`, `x-show`, `x-transition`
- **Tailwind CSS v3+** required for dark mode support
- **@alpinejs/collapse** plugin for section expanding
- LocalStorage used for persistent collapse states
- No JavaScript build required (CDN-based)

## 📖 Related Documentation

- [LAYOUT_RESTRUCTURING_FIX.md](LAYOUT_RESTRUCTURING_FIX.md) - Technical details
- [FIX_SUMMARY_LAYOUT_SEPARATION.md](FIX_SUMMARY_LAYOUT_SEPARATION.md) - Fix summary
- [UI_REDESIGN_IMPLEMENTATION.md](UI_REDESIGN_IMPLEMENTATION.md) - Full redesign context
- [QUICK_REFERENCE_UI_REDESIGN.md](QUICK_REFERENCE_UI_REDESIGN.md) - Original redesign reference
