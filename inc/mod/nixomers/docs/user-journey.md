# Nixomers Frontend & User Journey Guide (v2.3.0)

This guide documents the e-commerce experience from the perspective of the end-user (customer) and the visual feedback loops designed into the Nixomers framework.

## 1. The Purchase Flow Lifecycle

### 1.1 Checkout Phase
Nixomers integrates with the GeniXCMS cart system. Upon checkout:
- **Order Generation**: A unique alphanumeric `order_id` is generated.
- **Stock Protection**: Inventory is immediately deducted globally to prevent over-selling.
- **Initial State**: The order enters `pending` status.

### 1.2 Checkout Confirmation (The Modern Experience)
The confirmation page is the visual centerpiece of v2.3.0.
- **Real-time Totals**: Customers see a clear breakdown of Subtotal, Shipping, Admin Fees, and Taxes.
- **Interactive Forms**: The proof-of-payment upload form features real-time feedback. If the user selects a file, the label updates dynamically to show the filename.
- **Visual Validation**: Successful actions (like submitting a payment proof) trigger micro-animations and success badges using the `.gx-glass` aesthetic.

## 2. Customer Interactive Elements

### 2.1 Progress Tracking
The module provides a step-by-step visual tracker (Step 1: Order -> Step 2: Payment -> Step 3: Processing -> Step 4: Completion).
- **In-Transition State**: When a payment is "Pending Verification", the UI shifts to a high-contrast blue notification area to manage customer expectations.

### 2.2 Proof of Payment Requirements
- Users can upload common image formats (JPG, PNG).
- Max file size is controlled via `Asset` and `Upload` library settings.
- **Privacy**: Proofs are stored securely and are only accessible by authorized administrators.

## 3. Visual Feedback Design Tokens
Nixomers frontend components use these tokens which users experience as "premium cues":
- **Blur Depth**: 12px backdrop blur on payment summaries to imply depth and importance.
- **Typography Hierarchy**: Large, bold headings for totals (`h2.fw-bold`) to ensure financial clarity.
- **Hover States**: All primary buttons use a 0.2s transition with scale effects (`transform: translateY(-2px)`) to provide tactile digital feedback.
