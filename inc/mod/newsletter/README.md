# Newsletter Module — GeniXCMS

This module is used to manage email campaigns, subscribers, and provide a newsletter subscription form on the frontend.

## Theme Implementation

There are three ways to integrate the newsletter subscription form into your theme:

### 1. Using Hooks (Recommended for Templates)
You can call the standard form anywhere in your theme files.

**Latte Template (.latte):**
```html
{Hooks::run('newsletter_form')|noescape}
```

**PHP Template (.php):**
```php
<?php echo Hooks::run('newsletter_form'); ?>
```

---

### 2. Using Shortcodes (For Page/Post Content)
If you want to insert the form within the content of a page or post via the admin editor, use the following shortcode:

```text
[newsletter]
```

---

### 3. Using Widgets
The system provides a built-in Widget that can be managed via the Dashboard:

1. Go to **Admin Dashboard** > **Appearance** > **Widgets**.
2. Select a widget location (e.g., Sidebar or Footer).
3. Add a new widget with the type **"Newsletter: Subscription Form"**.

---

## Custom Form Integration (Advanced)

If you want to create a custom form design that matches your theme's aesthetics (e.g., using Tailwind CSS), you can still use the processing engine from this module. Ensure the `name` attributes match exactly:

```html
<form method="post" action="">
    <!-- Security Token (Required) -->
    <input type="hidden" name="ns_token" value="{Token::create()}">

    <!-- Name Input (Optional) -->
    <input type="text" name="ns_name" placeholder="Your Name">

    <!-- Email Input (Required) -->
    <input type="email" name="ns_email" placeholder="Email Address" required>

    <!-- Submit Button (Must have this name to trigger processing) -->
    <button type="submit" name="sm_subscribe_newsletter">
        Subscribe
    </button>
</form>
```

### Processing Logic
The module automatically handles:
- Email format validation.
- Duplicate email checks.
- Data storage in the `newsletter_subscribers` table.
- Status messages (success/failure) will be displayed automatically via hooks if you are using the standard form. If using a custom form, the status message is stored in `NewsletterModule::$statusMsg`.

## Developer Hooks

- `newsletter_form`: Hook to render the form (Action).
- `widget_render_newsletter`: Hook to render content within the widget.
- `post_content_filter`: Used to process shortcodes.
