# Nixomers Visual & UI/UX Guide (v2.3.0)

This document outlines the design system and visual standards implemented in the Nixomers module to achieve a premium, modern e-commerce experience.

## 1. Design Philosophy
Nixomers uses the **"Industrial ERP"** aesthetic combined with modern **Glassmorphism** for its external-facing components (like checkout confirmations). The Goal is to provide high information density while maintaining a luxurious, breathable interface.

## 2. Core UI Components

### 2.1 The Glassmorphism Layer (`.gx-glass`)
Used primarily for the **Payment Summary** and **Status Cards**.
- **Styles**: Applied via `genixcms.css`.
- **Aesthetics**: Semi-transparent white background with a heavy backdrop blur (`backdrop-filter: blur(12px)`) and subtle white borders.
- **Usage**: Provides focus on financial totals without losing contact with the page context.

### 2.2 Dual-Card Layout (Confirmation Page)
The confirmation interface is split into two specialized zones:
- **Left Column**: Detailed order metadata, items table, and shipping logs. High contrast text on white.
- **Right Column**: Financial breakdown using the Glassmorphism card. Sticky positioning for persistent visibility during scroll.

### 2.3 Financial States & Colors
- **Success (Paid/Complete)**: Uses `#2ea26c` (Emerald) with soft green glows.
- **Processing**: Uses `#4788c7` (Sapphire) with pulse animations if active.
- **Pending/Alert**: Uses `#f44336` (Ruby) for urgent visibility.

## 3. CSS Utility Classes
Developers should use the following GeniXCMS standard classes for Nixomers UI:
- `.gx-card-premium`: Adds soft deep shadows and 16px border-radius.
- `.gx-input-group-premium`: Standardizes borders between text and input fields for a seamless appearance.
- `.gx-badge-status`: Rounded-pill badges with high-contrast text and low-opacity backgrounds.

## 4. Mobile Responsiveness
Nixomers UI is built on **Bootstrap 5.3 Flexbox**. 
- Cards stack vertically on screens `< 992px`.
- Tables use horizontal scrolling wrappers to prevent layout breakage on small devices.
