# Vibe Woo Bold: Deployment Guide

This theme is designed for a **bold, simple, and optimized** WooCommerce experience. It uses Tailwind CSS via CDN for rapid prototyping and "vibe coding," but can be easily compiled for production.

## Quick Installation

1.  **Zip the Theme**: Compress the `vibe-woo-theme` folder into a `.zip` file.
2.  **Upload to WordPress**:
    - Go to **Appearance > Themes > Add New**.
    - Click **Upload Theme** and select your `.zip` file.
    - **Activate** the theme.
3.  **Install WooCommerce**: Ensure the WooCommerce plugin is installed and active.

## Customization (Vibe Coding)

-   **Design System**: Open `header.php` and modify the `tailwind.config` object to change your primary colors and typography.
-   **Bold Elements**: The theme uses `font-black`, `uppercase`, and `border-black` classes to achieve its signature look.
-   **WooCommerce Hooks**: Use `functions.php` to add or remove WooCommerce features.

## Production Optimization

For a production environment, it is recommended to:
1.  Install Tailwind CSS locally via npm.
2.  Compile your CSS to remove unused utility classes.
3.  Replace the CDN link in `header.php` with your compiled `style.css`.

```bash
# Example compilation command
npx tailwindcss -i ./src/input.css -o ./style.css --watch
```

## Theme Structure

-   `functions.php`: Theme setup and WooCommerce support.
-   `header.php`: Global header with Tailwind configuration.
-   `footer.php`: Global footer with newsletter and links.
-   `woocommerce.php`: The main wrapper for WooCommerce pages.
-   `index.php`: The fallback template for blog posts.
