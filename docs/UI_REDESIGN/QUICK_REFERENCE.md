# Sidebar Navigation - Quick Reference

> **TL;DR**: Admin sidebar now uses collapsible modules. Click "Human Resources" or "Finance" to expand/collapse. State is saved automatically.

---

## 🚀 For Users

### How to Use

1. **Expand a Module**: Click on "👥 Human Resources" or "💼 Finance"
2. **Navigate**: Click on any menu item
3. **Collapse**: Click the module header again
4. **Auto-Expansion**: Module auto-expands when you visit a page in that section

### What Changed?

**Before:**
```
Dashboard
Sites
Service Leads
───────────────
Who is Clocked In
Employees
Organization
... (25+ more items)
```

**After:**
```
Dashboard
Sites  
Service Leads
───────────────
▼ Human Resources
▶ Finance
```

**Much cleaner!** ✨

---

## 👨‍💻 For Developers

### File Locations

```
resources/views/
├── components/navigation/
│   ├── module.blade.php       ← Collapsible module
│   ├── section.blade.php      ← Section header
│   ├── item.blade.php         ← Menu item
│   └── group.blade.php        ← Item group
└── admin/layouts/
    ├── app.blade.php          ← Updated (includes partial)
    └── partials/
        └── sidebar.blade.php  ← New sidebar
```

### Quick Examples

#### Add a New Module

```blade
<x-navigation-module name="projects" label="Projects" color="purple">
    <x-navigation-item 
        :href="route('admin.projects.index')" 
        label="All Projects" 
        indent
    />
</x-navigation-module>
```

#### Add a New Section

```blade
<x-navigation-section label="Settings">
    <x-navigation-item :href="..." label="..." indent />
</x-navigation-section>
```

#### Add a Single Item

```blade
<x-navigation-item 
    :href="route('admin.feature.index')" 
    label="My Feature" 
    :active="request()->routeIs('admin.feature.*')"
    indent
    :icon="'<svg>...</svg>'"
/>
```

---

## 🎨 Available Props

### Module Component

```blade
<x-navigation-module
    name="module-id"           ← Required: unique ID
    label="Module Name"        ← Required: display name
    color="blue|green|purple"  ← Optional: accent color
    active                     ← Optional: force active
>
```

### Section Component

```blade
<x-navigation-section
    label="Section Name"       ← Required: header text
>
```

### Item Component

```blade
<x-navigation-item
    :href="route(...)"        ← Required: link URL
    label="Item Name"         ← Required: display text
    :active="boolean"         ← Optional: active state
    :icon="'svg...'"          ← Optional: SVG icon
    :badge="'3'"              ← Optional: badge count
    indent                    ← Optional: add indentation
>
```

---

## 🎯 Common Tasks

### Task: Add Finance Item

```blade
<!-- In sidebar.blade.php, inside Finance module -->
<x-navigation-module name="finance" ...>
    <x-navigation-section label="Your Section">
        <x-navigation-item 
            :href="route('admin.finance.invoices.index')" 
            label="Invoices" 
            :active="request()->routeIs('admin.finance.invoices.*')"
            indent
        />
    </x-navigation-section>
</x-navigation-module>
```

### Task: Add HRM Item

```blade
<!-- In sidebar.blade.php, inside HRM module -->
<x-navigation-module name="hrm" ...>
    <x-navigation-section label="Your Section">
        <x-navigation-item 
            :href="route('admin.hrm.training.index')" 
            label="Training" 
            :active="request()->routeIs('admin.hrm.training.*')"
            indent
        />
    </x-navigation-section>
</x-navigation-module>
```

### Task: Create New Module

```blade
<!-- In sidebar.blade.php, after Finance module -->
<x-navigation-module name="inventory" label="Inventory" color="purple">
    <x-navigation-item 
        :href="route('admin.inventory.products.index')" 
        label="Products" 
        indent
    />
</x-navigation-module>
```

---

## 🔧 Customization

### Change Module Color

```blade
<!-- Available colors: blue, green, purple -->
<x-navigation-module name="custom" label="Custom" color="purple">
```

### Add Badge to Item

```blade
<x-navigation-item 
    label="Notifications" 
    :badge="'5'"  ← Shows badge with "5"
/>
```

### Custom Icon

```blade
:icon="'<svg class=\'w-4 h-4 mr-2.5\' ...>...</svg>'"
```

---

## 🐛 Troubleshooting

### Module Won't Expand

**Check:**
1. Alpine.js is loaded (check browser console)
2. No JavaScript errors
3. `x-data` directive is present

### State Not Saving

**Check:**
1. LocalStorage is enabled in browser
2. No browser errors in console
3. Module `name` attribute is unique

### Active State Not Working

**Check:**
1. Route name matches pattern
2. Using `:active` (not `active`)
3. Correct route helper used

---

## 📊 State Management

### How State is Saved

```javascript
// Automatically saves to localStorage
localStorage.setItem('nav_module_hrm', 'true');      // Expanded
localStorage.setItem('nav_module_finance', 'false'); // Collapsed
```

### Clear Saved State

```javascript
// In browser console
localStorage.removeItem('nav_module_hrm');
localStorage.removeItem('nav_module_finance');

// Or clear all
localStorage.clear();
```

---

## ✅ Testing Checklist

After making changes:

- [ ] Module expands/collapses smoothly
- [ ] Active state highlights correctly
- [ ] State persists after page refresh
- [ ] Auto-expands to active route
- [ ] No console errors
- [ ] Icons render properly
- [ ] Spacing looks correct
- [ ] Works in dark mode

---

## 💡 Tips & Best Practices

### DO ✅

- Use descriptive section labels
- Keep item labels concise (2-4 words)
- Group related items together
- Use consistent icon styles
- Test in both light and dark mode

### DON'T ❌

- Create deeply nested structures (max 3 levels)
- Use duplicate module names
- Put too many items in one section (max 5-7)
- Forget to test active states
- Skip the `indent` prop on nested items

---

## 📱 Responsive Behavior

### Desktop (>1024px)
- Full sidebar always visible
- Modules collapse/expand in place

### Tablet (768-1023px)
- Consider adding sidebar toggle
- May overlay content when open

### Mobile (<768px)
- Drawer-style recommended
- Full screen when open
- Hamburger menu trigger

---

## 🎯 Key Features

✅ **Collapsible Modules** - Save space, reduce clutter  
✅ **Persistent State** - Remembers your preferences  
✅ **Auto-Expansion** - Opens to current page automatically  
✅ **Smooth Animations** - Professional feel  
✅ **Color Coding** - Visual distinction between modules  
✅ **Accessibility** - Keyboard and screen reader friendly  

---

## 📞 Need Help?

1. **Check Documentation**:
   - [Complete Plan](SIDEBAR_NAVIGATION_REORGANIZATION_PLAN.md)
   - [Implementation Summary](SIDEBAR_IMPLEMENTATION_SUMMARY.md)
   - [Visual Comparison](SIDEBAR_VISUAL_COMPARISON.md)

2. **Common Issues**:
   - Module won't expand → Check Alpine.js
   - State not saving → Check localStorage
   - Active state wrong → Check route matching

3. **Contact Team**: If stuck, reach out to dev team

---

**Quick Links:**
- 📋 [Full Plan](SIDEBAR_NAVIGATION_REORGANIZATION_PLAN.md)
- ✅ [Implementation](SIDEBAR_IMPLEMENTATION_SUMMARY.md)
- 🎨 [Visual Guide](SIDEBAR_VISUAL_COMPARISON.md)
- 📚 [README](README.md)

---

**Last Updated**: January 6, 2026
