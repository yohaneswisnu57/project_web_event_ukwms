---
name: ui-ux-promax
description: "Guidelines for implementing high-end, premium, and state-of-the-art UI/UX in Laravel Filament projects. Focuses on rich aesthetics, glassmorphism, micro-animations, and dynamic interactions."
---

# UI/UX ProMax Skill

Apply these guidelines whenever creating or modifying the frontend, Filament resources, or custom components to ensure a "WOW" factor.

## 1. Rich Aesthetics & Color Theory

- **Avoid Defaults**: Never use plain `red-500` or `blue-500`. Use curated, harmonious palettes.
- **Gradients**: Use subtle, multi-stop gradients for backgrounds and primary buttons.
- **Dark Mode**: Always design with a premium dark mode in mind (using deep grays and slates instead of pure black).
- **Typography**: Prefer modern sans-serif fonts (e.g., Inter, Outfit, Roboto). Use tight tracking for headings.

## 2. Depth & Glassmorphism

- **Shadows**: Use layered box-shadows to create realistic depth.
- **Glassmorphism**: Use `backdrop-blur` and semi-transparent backgrounds for overlays and modals.
- **Borders**: Use subtle 1px borders with low opacity to define edges without adding visual noise.

## 3. Dynamic Design & Micro-animations

- **Hover States**: Every interactive element must have a smooth hover transition (scale, shadow, or color shift).
- **Alpine.js**: Use Alpine.js for lightweight UI transitions (e.g., `x-transition`, `x-collapse`).
- **Loading States**: Implement elegant skeleton loaders or custom spinners that match the theme.

## 4. Filament-Specific Enhancements

- **Custom Icons**: Use unique icons (e.g., Lucide or Heroicons) that fit the context perfectly.
- **Section Layouts**: Group related fields into `Section` components with clear headings and subtle icons.
- **Table Polish**: Use `BadgeColumn` for statuses and `ImageColumn` for visual records. Ensure empty states are helpful and visually appealing.

## 5. Mobile Optimization

- **Responsive First**: Ensure every UI element is touch-friendly and scales beautifully on mobile devices.
- **Spacing**: Use generous whitespace (padding/margins) to let the design "breathe."

---
*Follow these rules to transform a standard application into a ProMax experience.*
