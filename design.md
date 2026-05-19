# Design System Inspired by Sauce Labs

## 1. Visual Theme & Atmosphere

Sauce Labs embodies a bold, enterprise-grade design language rooted in dark, high-contrast aesthetics with vibrant accents. The visual identity conveys precision, reliability, and cutting-edge technology through a sophisticated palette of deep charcoals and crisp whites, punctuated by an energetic emerald green. Monospace and geometric typefaces reinforce the technical foundation, while generous whitespace and minimal ornamentation create breathing room in a data-heavy environment. The design exudes confidence and professionalism, suitable for global enterprises and engineering teams who demand both performance and clarity.

**Key Characteristics**
- Deep, dark neutral foundation with high contrast for readability
- Vibrant emerald accent for calls-to-action and highlights
- Clean geometric typography with minimal serif elements
- Ample whitespace and breathing room between components
- Minimalist button treatments with generous padding
- Enterprise-grade, trustworthy aesthetic
- Strong emphasis on visual hierarchy through scale and weight

## 2. Color Palette & Roles

### Primary
- **Deep Charcoal** (`#132322`): Primary background color for hero sections, dark surfaces, and dominant brand presence across the platform
- **Near Black** (`#0E1A1A`): Deepest neutral for maximum contrast text and critical UI elements

### Accent Colors
- **Emerald Green** (`#3DDC91`): Primary accent for call-to-action buttons, interactive highlights, and brand activation; conveys growth and forward momentum

### Interactive
- **Sky Blue** (`#007AFF`): Secondary interactive state for links and alternative actions; provides visual contrast to primary emerald

### Neutral Scale
- **Pure White** (`#FFFFFF`): Primary text on dark backgrounds, card backgrounds, and light surfaces
- **Jet Black** (`#000000`): Primary text on light backgrounds and maximum contrast overlays
- **Stone Gray** (`#434E4E`): Secondary text, disabled states, and subtle UI elements
- **Light Gray** (`#D0D3D3`): Borders, dividers, and minimal contrast elements

### Surface & Borders
- **Medium Gray** (`#434E4E`): Border colors, subtle dividers, and tertiary text layers

## 3. Typography Rules

### Font Family
**Primary Display Font:** AeonikFono (geometric, technical sans-serif)
Fallback stack: `AeonikFono, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`

**Secondary Font:** Aeonik (refined geometric sans-serif for body content)
Fallback stack: `Aeonik, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`

**Button/Icon Font:** Arial (universal system font for icons and UI controls)
Fallback stack: `Arial, sans-serif`

### Hierarchy

| Role | Font | Size | Weight | Line Height | Letter Spacing | Notes |
|------|------|------|--------|-------------|----------------|-------|
| Display / Hero | AeonikFono | 64px | 400 | 78.08px | 0 | Largest heading, reserved for main page titles |
| Heading H2 | AeonikFono | 48px | 400 | 52.8px | 0 | Major section headers and feature titles |
| Heading H5 | AeonikFono | 32px | 400 | 41.6px | 0 | Subsection headers and card titles |
| Input / Large Label | AeonikFono | 52px | 400 | 57.2px | 0 | Form input text and prominent labels |
| Heading H3 / Small Title | AeonikFono | 14px | 400 | normal | 0 | Subheadings and category labels |
| Body / Standard Text | Aeonik | 16px | 400 | 20px | 0 | Primary body copy, navigation links, descriptions |
| Button / Icon | Arial | 28px | 400 | normal | 0 | Icon buttons and interactive elements |
| List Item | AeonikFono | 16px | 400 | normal | 0 | List entries and navigation menu items |

### Principles
- **Geometric clarity:** All fonts prioritize clean lines and technical precision over decorative flourish
- **Scale-driven hierarchy:** Dramatic size shifts (64px → 48px → 32px) create immediate visual scanning order
- **Minimal weight variation:** Consistent use of 400 weight maintains visual simplicity; hierarchy driven by size, not boldness
- **Line height breathing:** Generous line heights (78.08px, 52.8px) ensure readability in hero sections and long-form content
- **Input prominence:** Large 52px input fields signal interactive engagement and reduce cognitive friction

## 4. Component Stylings

### Buttons

**Primary CTA Button (Emerald)**
- Background: `#3DDC91`
- Text Color: `#000000`
- Font Family: Aeonik
- Font Size: 16px
- Font Weight: 400
- Padding: `12px 32px`
- Border Radius: `32px`
- Border: none
- Box Shadow: none
- Hover: Darken background to `#2EC977`, increase shadow to `0px 8px 16px rgba(61, 220, 145, 0.3)`
- Active: Scale `0.98`, reduce shadow

**Secondary Button (Outline)**
- Background: transparent
- Text Color: `#FFFFFF`
- Font Family: Aeonik
- Font Size: 16px
- Font Weight: 400
- Padding: `12px 32px`
- Border Radius: `32px`
- Border: `2px solid #FFFFFF`
- Box Shadow: none
- Hover: Background `rgba(255, 255, 255, 0.1)`
- Active: Background `rgba(255, 255, 255, 0.2)`

**Ghost Button (Text Only)**
- Background: transparent
- Text Color: `#132322`
- Font Family: AeonikFono
- Font Size: 16px
- Font Weight: 400
- Padding: `0px 0px`
- Border Radius: `0px`
- Border: none
- Box Shadow: none
- Hover: Text Color: `#3DDC91`, add underline `1px solid #3DDC91`
- Active: Text Color: `#2EC977`

**Icon Button (Circular)**
- Background: transparent
- Text Color: `rgba(0, 0, 0, 0.54)`
- Font Family: Arial
- Font Size: 28px
- Font Weight: 400
- Padding: `12px`
- Border Radius: `50%`
- Width: `50px`
- Height: `50px`
- Border: none
- Box Shadow: none
- Hover: Background: `rgba(0, 0, 0, 0.08)`
- Active: Background: `rgba(0, 0, 0, 0.12)`

### Cards & Containers

**Default Card**
- Background: `#FFFFFF`
- Text Color: `#132322`
- Font Family: Aeonik
- Font Size: 16px
- Font Weight: 400
- Padding: `24px`
- Border Radius: `16px`
- Border: `1px solid #D0D3D3`
- Box Shadow: `0px 4px 12px rgba(0, 0, 0, 0.08)`
- Hover: Box Shadow: `0px 8px 24px rgba(0, 0, 0, 0.12)`, Border: `1px solid #3DDC91`

**Dark Card (Deep Charcoal)**
- Background: `#132322`
- Text Color: `#FFFFFF`
- Font Family: Aeonik
- Font Size: 16px
- Font Weight: 400
- Padding: `24px`
- Border Radius: `16px`
- Border: `1px solid rgba(61, 220, 145, 0.3)`
- Box Shadow: `0px 4px 12px rgba(0, 0, 0, 0.2)`
- Hover: Border: `1px solid #3DDC91`, Box Shadow: `0px 8px 24px rgba(61, 220, 145, 0.15)`

**Hero Container**
- Background: `#132322`
- Padding: `40px 40px`
- Border Radius: `0px` (full-width or section edge-to-edge)
- Text Color: `#FFFFFF`
- Min Height: `400px`

### Inputs & Forms

**Text Input (Large)**
- Background: transparent
- Text Color: `#FFFFFF`
- Font Family: AeonikFono
- Font Size: 52px
- Font Weight: 400
- Padding: `4px 0px 16px 0px`
- Border: none
- Border Bottom: `2px solid #FFFFFF`
- Border Radius: `0px`
- Line Height: `57.2px`
- Height: `74.75px`
- Width: `100%`
- Placeholder Color: `rgba(255, 255, 255, 0.5)`
- Focus: Border Bottom: `2px solid #3DDC91`, Box Shadow: `0px 4px 0px rgba(61, 220, 145, 0.2)`
- Error: Border Bottom: `2px solid #FF4B4B`, Text Color: `#FF4B4B`

**Standard Input**
- Background: `#FFFFFF`
- Text Color: `#132322`
- Font Family: Aeonik
- Font Size: 14px
- Font Weight: 400
- Padding: `12px 16px`
- Border: `1px solid #D0D3D3`
- Border Radius: `8px`
- Height: `44px`
- Focus: Border: `2px solid #3DDC91`, Box Shadow: `0px 0px 0px 4px rgba(61, 220, 145, 0.1)`
- Disabled: Background: `#F5F5F5`, Text Color: `rgba(0, 0, 0, 0.38)`, Border: `1px solid #D0D3D3`

**Checkbox / Radio**
- Width: `20px`
- Height: `20px`
- Border: `2px solid #434E4E`
- Border Radius: `4px` (checkbox), `50%` (radio)
- Checked: Background: `#3DDC91`, Border: `2px solid #3DDC91`

### Navigation

**Header Navigation Link**
- Background: transparent
- Text Color: `rgba(0, 0, 0, 0.87)`
- Font Family: AeonikFono
- Font Size: 16px
- Font Weight: 400
- Padding: `0px 0px`
- Border: none
- Border Radius: `0px`
- Hover: Text Color: `#3DDC91`, underline: `1px solid #3DDC91`
- Active: Text Color: `#3DDC91`, Border Bottom: `2px solid #3DDC91`

**Navigation Menu (Vertical)**
- Background: `#132322`
- Padding: `16px 0px`
- Border Radius: `8px`
- Box Shadow: `0px 16px 32px rgba(0, 0, 0, 0.1)`

**Menu Item**
- Padding: `12px 16px`
- Text Color: `#FFFFFF`
- Font Family: Aeonik
- Font Size: 14px
- Hover: Background: `rgba(61, 220, 145, 0.1)`, Text Color: `#3DDC91`

### Badges

**Default Badge**
- Background: `#3DDC91`
- Text Color: `#000000`
- Font Family: Aeonik
- Font Size: 12px
- Font Weight: 400
- Padding: `4px 12px`
- Border Radius: `16px`
- Border: none

**Secondary Badge**
- Background: `#D0D3D3`
- Text Color: `#132322`
- Font Family: Aeonik
- Font Size: 12px
- Font Weight: 400
- Padding: `4px 12px`
- Border Radius: `16px`

## 5. Layout Principles

### Spacing System
**Base Unit:** 8px

**Scale & Usage:**
- `8px`: Tight spacing within components, icon padding
- `12px`: Gap between form elements, button padding (vertical)
- `16px`: Standard padding inside cards and containers, horizontal button padding
- `24px`: Section padding, card padding
- `32px`: Large gap between sections, margin between major content blocks
- `40px`: Hero section padding, max-width container edge padding
- `160px`: Full-width section padding (hero sections with breathing room on desktop)

### Grid & Container
- **Max Width:** `1440px` (desktop container)
- **Columns:** 12-column grid, 8-column (tablet), 4-column (mobile)
- **Gutter:** `24px` between columns
- **Hero Section:** Full-width or edge-to-edge with `40px` or `160px` horizontal padding
- **Content Width:** 80% max-width for centered text blocks, full-width for hero imagery

### Whitespace Philosophy
Abundant whitespace is the foundation of the design language. Spacing between sections (32px–160px) allows dark and light backgrounds to create visual breathing room. Within cards, generous padding (24px–40px) prevents cognitive overload. Navigation and button elements are surrounded by whitespace to emphasize hierarchy and reduce visual clutter.

### Border Radius Scale
- `0px`: Large containers, hero sections, navigation bars (sharp edges for architectural feel)
- `8px`: Standard cards, standard inputs, subtle rounding
- `16px`: Card containers, elevated surfaces
- `32px`: Primary buttons, pill-shaped elements
- `50%`: Perfect circles for icon buttons and avatars

## 6. Depth & Elevation

| Level | Treatment | Use |
|-------|-----------|-----|
| Flat (L0) | No shadow | Background surfaces, base layer, typography |
| Raised (L1) | `0px 4px 12px rgba(0, 0, 0, 0.08)` | Standard cards, containers |
| Elevated (L2) | `0px 8px 24px rgba(0, 0, 0, 0.12)` | Hovered cards, modals, dropdowns on light bg |
| Prominent (L3) | `0px 16px 32px rgba(0, 0, 0, 0.1)` | Dropdown menus, floating panels, popovers |
| Deep (L4) | `0px 24px 48px rgba(0, 0, 0, 0.2)` | Full-screen overlays, modal backgrounds |

**Shadow Philosophy:**
Shadows are subtle and restrained, emphasizing functional hierarchy without overwhelming the minimalist aesthetic. Shadows increase slightly on hover to signal interactivity. Dark surfaces (charcoal backgrounds) use warmer shadow tones with reduced opacity (`rgba(61, 220, 145, 0.1)` to `rgba(61, 220, 145, 0.3)`), while light surfaces use neutral black shadows. The shadow scale reinforces information architecture—critical interactive elements receive proportional depth.

## 7. Do's and Don'ts

### Do
- **Use emerald (`#3DDC91`) sparingly** for primary CTAs, hover states, and accents that demand attention
- **Prioritize high contrast** for accessibility; ensure text color meets WCAG AAA standards (white on dark, dark on light)
- **Maintain generous padding** in cards and containers (minimum `24px`) to prevent visual cramping
- **Apply consistent border radius** across component families (0px for sharp edges, 32px for buttons, 50% for circles)
- **Layer shadows subtly** on hover to communicate interactivity without overwhelming the design
- **Use AeonikFono for headings** and AeonikFono/Aeonik for body text to maintain technical clarity
- **Center align large input fields** with dramatic typography (52px) for hero sections; use left-align for standard forms
- **Embrace whitespace** between sections; use 32px–160px margins between major content blocks
- **Create visual hierarchy through scale** alone; weight remains consistent at 400 throughout

### Don't
- **Avoid bright secondary colors** on primary action buttons; reserve emerald for brand moments
- **Don't overuse shadows** on dark backgrounds; use transparent emerald tints instead
- **Avoid rounded corners on hero sections** and full-width containers; keep edges sharp for architectural impact
- **Don't mix font families within a single hierarchy level** (e.g., don't alternate Aeonik and AeonikFono in body text)
- **Avoid padding below `16px`** in interactive elements; minimum touch target is 44px
- **Don't nest more than two levels of cards** without increasing shadow depth significantly
- **Avoid low-contrast text colors** like stone gray (`#434E4E`) on dark charcoal backgrounds; use white for readability
- **Don't justify text alignment** in body copy; use left-align or center for clarity
- **Avoid animating shadows**; use opacity and border color changes for hover feedback instead

## 8. Responsive Behavior

### Breakpoints

| Breakpoint | Width | Key Changes |
|------------|-------|-------------|
| Mobile | 320px–639px | Single-column layout, full-width cards, reduced padding (`16px`), font sizes decrease 8%, buttons full-width |
| Tablet | 640px–1023px | Two-column grid, 50% image/content split, padding `24px`, font sizes unchanged, condensed navigation |
| Desktop | 1024px–1439px | 12-column grid, 2–3 column layouts, padding `40px`, full typography scale, expanded navigation |
| Large Desktop | 1440px+ | Max-width container `1440px`, centered layout, padding `160px` on hero sections, sidebar navigation |

### Touch Targets
- **Minimum size:** `44px × 44px` for all interactive elements
- **Button height:** `44px` minimum (desktop), `48px` (touch devices)
- **Icon buttons:** `50px × 50px` (from existing design)
- **Link underlines:** Add `2px` underline on hover for clarity
- **Spacing between interactive elements:** Minimum `12px` to prevent accidental activation

### Collapsing Strategy
- **Cards:** Stack vertically on mobile; grid layout on tablet (2-col), desktop (3-col)
- **Hero sections:** Full-width image above text on mobile; side-by-side (50/50 split) on desktop
- **Navigation:** Hamburger menu on mobile; horizontal nav on desktop
- **Input fields:** Full-width on all breakpoints; large 52px inputs only on hero sections (desktop)
- **Padding reduction:** 40px desktop padding → 24px tablet → 16px mobile
- **Font size scaling:** Reduce display fonts 8% on tablets, 12% on mobile; body text remains `16px`
- **Spacing scale:** 32px gaps become 24px (tablet), 16px (mobile); 160px hero padding becomes 40px (tablet), 16px (mobile)

## 9. Agent Prompt Guide

### Quick Color Reference
- **Primary CTA:** Emerald Green (`#3DDC91`)
- **Primary Background:** Deep Charcoal (`#132322`)
- **Secondary Background:** Pure White (`#FFFFFF`)
- **Heading Text:** Jet Black (`#000000`) on light, Pure White (`#FFFFFF`) on dark
- **Body Text:** Jet Black (`#000000`) on light, Pure White (`#FFFFFF`) on dark
- **Secondary Text:** Stone Gray (`#434E4E`)
- **Borders:** Light Gray (`#D0D3D3`) on light backgrounds, `rgba(61, 220, 145, 0.3)` on dark
- **Accent Link:** Sky Blue (`#007AFF`)

### Iteration Guide
1. **Always apply high contrast** for accessibility: white/black text on colored backgrounds, dark/light text on neutral surfaces
2. **Buttons use emerald (`#3DDC91`) with black text**, not white; hover state darkens to `#2EC977` with increased shadow depth
3. **Large hero inputs (52px)** use white text, transparent background, bottom border only; smaller inputs use charcoal background with rounded corners
4. **Card padding is always `24px` minimum**; hero section padding is `40px`–`160px` depending on viewport
5. **Border radius follows the scale:** 0px (edges), 8px (cards), 16px (elevated cards), 32px (buttons), 50% (icons)
6. **Shadows increase on hover** (L1 → L2) to signal interactivity; use `rgba(0, 0, 0, 0.1)` to `rgba(0, 0, 0, 0.12)` progression
7. **Typography hierarchy relies on size alone** (64px, 48px, 32px, 16px); weight remains 400 throughout
8. **Whitespace between major sections** should be 32px (standard), 40px (prominent), or 160px (hero breathing room)
9. **Mobile layout is always single-column** with full-width components; tablets use 2-column grid; desktop uses 12-column grid with max-width `1440px`
10. **Navigation links turn emerald on hover/active** state; all interactive elements have visible feedback (color change, shadow, or underline)