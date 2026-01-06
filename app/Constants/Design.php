<?php

namespace App\Constants;

/**
 * Design System Constants
 * 
 * This class provides unified design constants for fonts, spacing, and typography
 * used throughout the project. Ensures consistency across admin dashboard and staff dashboard.
 */
class Design
{
    // ========== FONT FAMILIES ==========
    public const FONT_SANS = 'sans';
    public const FONT_SERIF = 'serif';
    public const FONT_MONO = 'mono';

    // ========== FONT SIZES (Tailwind: text-*) ==========
    public const TEXT_XS = 'text-xs';      // 12px
    public const TEXT_SM = 'text-sm';      // 14px
    public const TEXT_BASE = 'text-base';  // 16px
    public const TEXT_LG = 'text-lg';      // 18px
    public const TEXT_XL = 'text-xl';      // 20px
    public const TEXT_2XL = 'text-2xl';    // 24px
    public const TEXT_3XL = 'text-3xl';    // 30px

    // ========== FONT WEIGHTS ==========
    public const FONT_LIGHT = 'font-light';       // 300
    public const FONT_NORMAL = 'font-normal';     // 400
    public const FONT_MEDIUM = 'font-medium';     // 500
    public const FONT_SEMIBOLD = 'font-semibold'; // 600
    public const FONT_BOLD = 'font-bold';         // 700

    // ========== SPACING SYSTEM ==========
    // Universal base spacing unit: 4px
    // Compact spacing values from Tailwind
    public const SPACE_XS = 'px-2 py-1';         // 8px/4px
    public const SPACE_SM = 'px-3 py-1.5';       // 12px/6px
    public const SPACE_MD = 'px-4 py-2';         // 16px/8px
    public const SPACE_LG = 'px-6 py-3';         // 24px/12px
    public const SPACE_XL = 'px-8 py-4';         // 32px/16px

    // ========== PADDING CONSTANTS ==========
    public const PAD_XS = 'p-1';         // 4px
    public const PAD_SM = 'p-2';         // 8px
    public const PAD_MD = 'p-3';         // 12px
    public const PAD_LG = 'p-4';         // 16px
    public const PAD_XL = 'p-6';         // 24px
    public const PAD_2XL = 'p-8';        // 32px

    // Horizontal padding only
    public const PAD_X_SM = 'px-2';      // 8px
    public const PAD_X_MD = 'px-3';      // 12px
    public const PAD_X_LG = 'px-4';      // 16px
    public const PAD_X_XL = 'px-6';      // 24px

    // Vertical padding only
    public const PAD_Y_SM = 'py-1';      // 4px
    public const PAD_Y_MD = 'py-1.5';    // 6px
    public const PAD_Y_LG = 'py-2';      // 8px
    public const PAD_Y_XL = 'py-3';      // 12px

    // ========== MARGIN CONSTANTS ==========
    public const MARGIN_XS = 'm-1';      // 4px
    public const MARGIN_SM = 'm-2';      // 8px
    public const MARGIN_MD = 'm-3';      // 12px
    public const MARGIN_LG = 'm-4';      // 16px
    public const MARGIN_XL = 'm-6';      // 24px

    // Horizontal margin only
    public const MARGIN_X_SM = 'mx-1';   // 4px
    public const MARGIN_X_MD = 'mx-2';   // 8px
    public const MARGIN_X_LG = 'mx-3';   // 12px

    // ========== GAP SPACING (Flex & Grid) ==========
    public const GAP_SM = 'gap-1';       // 4px
    public const GAP_MD = 'gap-2';       // 8px
    public const GAP_LG = 'gap-3';       // 12px
    public const GAP_XL = 'gap-4';       // 16px
    public const GAP_2XL = 'gap-6';      // 24px

    // ========== SPECIFIC COMPONENT SPACING ==========
    // Navigation items
    public const NAV_ITEM_SPACING = 'gap-3 px-4 py-2.5';
    public const NAV_ITEM_TEXT = 'text-sm';

    // Sidebar
    public const SIDEBAR_SECTION_PADDING = 'p-5';
    public const SIDEBAR_ITEM_SPACING = 'space-y-2';
    public const SIDEBAR_ICON_SIZE = 'w-10 h-10';

    // Modal/Dialog
    public const MODAL_PADDING = 'p-6';
    public const MODAL_HEADER_PADDING = 'p-6';
    public const MODAL_BODY_SPACING = 'space-y-4';

    // Card/Panel
    public const CARD_PADDING = 'p-6';
    public const CARD_SPACING = 'space-y-3';

    // Button
    public const BTN_PADDING = 'px-4 py-2';
    public const BTN_SMALL_PADDING = 'px-3 py-1.5';
    public const BTN_LARGE_PADDING = 'px-6 py-3';

    // Input/Form
    public const INPUT_PADDING = 'px-3 py-2';
    public const INPUT_SM_PADDING = 'px-2 py-1';

    // ========== LINE HEIGHT ==========
    public const LEADING_TIGHT = 'leading-tight';
    public const LEADING_NORMAL = 'leading-normal';
    public const LEADING_RELAXED = 'leading-relaxed';
    public const LEADING_LOOSE = 'leading-loose';

    // ========== COMMON COMBINATIONS ==========
    // Sidebar menu item
    public const MENU_ITEM = 'flex items-center gap-3 px-4 py-2.5 text-sm rounded transition-colors';

    // Navigation link
    public const NAV_LINK = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium';

    // Form label
    public const FORM_LABEL = 'block font-medium text-sm text-slate-300';

    /**
     * Get combined spacing class
     * 
     * @param string $horizontal Horizontal spacing (px-*)
     * @param string $vertical Vertical spacing (py-*)
     * @return string Combined spacing class
     */
    public static function spacing(string $horizontal, string $vertical): string
    {
        return "$horizontal $vertical";
    }

    /**
     * Get combined text styling
     * 
     * @param string $size Text size constant (TEXT_*)
     * @param string $weight Font weight constant (FONT_*)
     * @return string Combined text styling
     */
    public static function text(string $size, string $weight): string
    {
        return "$size $weight";
    }
}
