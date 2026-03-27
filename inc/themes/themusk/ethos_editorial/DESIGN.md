# Design System Specification: Editorial Serenity

## 1. Overview & Creative North Star: "The Quiet Editor"
This design system is not a template; it is a digital gallery. The Creative North Star is **The Quiet Editor**—a philosophy that prioritizes the hierarchy of thought over the noise of interface. By utilizing intentional asymmetry, expansive breathing room, and a sophisticated serif-on-sans-serif typographic architecture, we move away from "standard web design" into the realm of high-end editorial publishing.

The goal is to create a sense of "digital permanence." We achieve this through the **Layering Principle**, where depth is communicated via tonal shifts rather than artificial shadows, and through **Asymmetric Rhythm**, where whitespace is used as a functional element to guide the eye through long-form content.

## 2. Colors & Surface Philosophy
The palette is rooted in a "New Neutral" foundation—soft off-whites and deep charcoals—anchored by a singular, sophisticated Forest Green (`primary: #45655b`).

### The "No-Line" Rule
Standard UI relies on borders to define containers. This design system prohibits the use of 1px solid borders for sectioning. Boundaries must be defined solely through background color shifts or tonal transitions. To separate a sidebar from a main feed, transition from `surface` (#f9f9fc) to `surface-container-low` (#f2f3f7). This creates a "soft edge" that feels integrated rather than walled off.

### Surface Hierarchy & Nesting
Treat the UI as a physical stack of fine paper. 
- **Base Level:** Use `surface` (#f9f9fc) for the primary application background.
- **Content Blocks:** Use `surface-container-lowest` (#ffffff) for article cards or content blocks to create a "highlighted" lift.
- **Global Navigation/Footers:** Use `surface-container` (#ebeef3) to ground the interface.
- **Nesting:** An inner container (like a pull-quote) should use a tier one step higher or lower than its parent (e.g., a `surface-container-high` block sitting on a `surface-container` background).

### The "Glass & Gradient" Rule
To add a signature "High-End" polish, floating elements (like sticky headers or navigation overlays) must use **Glassmorphism**. Apply `surface` at 80% opacity with a `20px` backdrop-blur. 
For main CTAs or hero backgrounds, use a subtle linear gradient transitioning from `primary` (#45655b) to `primary_dim` (#39584f) at a 135-degree angle. This adds a "visual soul" that flat colors lack.

## 3. Typography: The Editorial Voice
Typography is the cornerstone of this system. We pair the geometric authority of **Manrope** (Sans-Serif) with the literary warmth of **Newsreader** (Serif).

*   **Display & Headlines (Manrope):** Use `display-lg` (3.5rem) for hero titles. The high x-height of Manrope provides a modern, architectural feel. Letter spacing should be tightened slightly (-0.02em) for large scales to feel "locked in."
*   **Body Text (Newsreader):** Use `body-lg` (1rem) for all long-form reading. Newsreader is designed for legibility; use a generous line-height (1.6 to 1.8) to allow the reader’s eye to flow effortlessly.
*   **Captions & Labels (Manrope):** Use `label-md` (0.75rem) in all-caps with increased letter spacing (+0.05em) for metadata, tags, and small UI hints. This creates a functional contrast against the organic serif body text.

## 4. Elevation & Depth
Depth in this system is organic, mimicking natural light hitting stacked materials.

*   **Tonal Layering:** Avoid the "floating box" look. Place a `surface-container-lowest` card on a `surface-container-low` section. The 2-3% difference in hex value is enough to define the shape without the clutter of a stroke.
*   **Ambient Shadows:** If an element must float (e.g., a Modal or Popover), use an extra-diffused shadow. 
    *   *Formula:* `0px 24px 48px rgba(45, 51, 57, 0.06)` (using a tinted version of `on_surface`).
*   **The "Ghost Border" Fallback:** If accessibility requires a border, use the **Ghost Border**: `outline-variant` (#acb3b9) at 15% opacity. Never use 100% opaque borders.

## 5. Components

### Buttons
- **Primary:** Background `primary` (#45655b), text `on_primary` (#defff3). Use `md` (0.375rem) roundedness. 
- **Secondary:** Background `secondary_container` (#e1e3e0), text `on_secondary_container` (#4f5251).
- **Tertiary (Ghost):** No background. Text `primary` (#45655b). Use for low-emphasis actions.
- **Interaction:** On hover, primary buttons should shift to `primary_dim` (#39584f) with a subtle `2px` vertical lift.

### Cards & Feed Items
- **Structure:** No borders or dividers. Use `spacing-6` (2rem) between cards.
- **Styling:** Use a background of `surface-container-lowest` (#ffffff). The separation is achieved by the contrast against the `surface` background.

### Input Fields
- **Styling:** Use `surface_container_low` (#f2f3f7) as the field background. 
- **Border:** Use the "Ghost Border" (15% opacity `outline-variant`).
- **Focus State:** Transition the ghost border to 100% `primary` (#45655b) and add a `2px` soft glow in the same color.

### Chips & Tags
- **Styling:** Use `secondary_fixed_dim` (#d2d5d2) with `full` (9999px) roundedness. 
- **Text:** `label-sm` (Manrope) for a crisp, functional look.

### The "Editorial Quote" (Signature Component)
- **Design:** Use `title-lg` (Newsreader) in an italic style. 
- **Accent:** A `2px` thick vertical line of `primary` (#45655b) to the left of the text, offset by `spacing-4`.

## 6. Do’s and Don’ts

### Do:
- **Embrace the Void:** Use `spacing-16` (5.5rem) or `spacing-20` (7rem) between major sections to let the content "breathe."
- **Use Tonal Shifts:** If a design feels "flat," change the background color of a section rather than adding a shadow.
- **Prioritize the Serif:** Ensure the article body is always in Newsreader. It is the "human" element of the system.

### Don’t:
- **Don't use 1px Borders:** Avoid "boxing in" content. Let the edges be defined by the negative space.
- **Don't use Pure Black:** Always use `on_background` (#2d3339) for text. Pure black (#000000) is too harsh for an editorial experience.
- **Don't Over-Animate:** Transitions should be slow and "weighted" (e.g., 300ms ease-out). Avoid "bouncy" or "snappy" animations that break the serene mood.