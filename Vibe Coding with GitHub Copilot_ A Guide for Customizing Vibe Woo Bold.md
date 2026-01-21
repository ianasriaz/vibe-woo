# Vibe Coding with GitHub Copilot: A Guide for Customizing Vibe Woo Bold

This guide provides a structured approach to leveraging GitHub Copilot within VS Code to rapidly customize your **Vibe Woo Bold** theme. This process, which we call "Vibe Coding," focuses on speed, bold design, and efficient WooCommerce optimization.

## 1. Setup and Workflow

To begin Vibe Coding, ensure you have the following setup:

| Component | Requirement | Purpose |
| :--- | :--- | :--- |
| **Editor** | VS Code | The primary development environment. |
| **Extension** | GitHub Copilot | Provides AI-powered code suggestions and chat. |
| **Theme** | Vibe Woo Bold | The custom theme scaffold. |
| **Context** | Local WordPress/WooCommerce Install | Essential for testing and providing Copilot with file context. |

**Workflow Tip**: For the best results, keep the files you are working on open in VS Code. Copilot uses the open files to understand the context of your project, including the custom Tailwind configuration in `header.php` and the WooCommerce hooks in `functions.php` [1].

## 2. Vibe Coding Techniques: Prompt Engineering

The key to Vibe Coding is writing clear, descriptive comments that guide Copilot to generate the exact code you need.

### A. Contextual Prompts (The "Vibe")

Use comments to establish the *intent* and *style* before writing the code.

| Goal | Prompt Example | Copilot Output Focus |
| :--- | :--- | :--- |
| **Bold Styling** | `// Tailwind: Apply a bold, high-contrast style to the product title, using the 'accent' color on hover.` | Generates `class="text-4xl font-black text-vibe hover:text-accent transition-colors"` |
| **WooCommerce Hook** | `// WooCommerce: Add a function to display a "New Arrival" badge on products added in the last 7 days.` | Generates the correct `woocommerce_before_shop_loop_item_title` hook and logic. |
| **Theme Feature** | `// WordPress: Register a custom post type for 'Lookbooks' with a public UI.` | Generates the `register_post_type` function with all necessary arguments. |

### B. Iterative Refinement with Copilot Chat

Use the Copilot Chat feature (accessible via the sidebar or `Ctrl+Shift+I`) to refine existing code or ask for complex solutions.

1.  **Select Code**: Highlight the code block you want to change (e.g., the `vibe_woo_add_to_cart_classes` function in `functions.php`).
2.  **Ask for Refinement**: Prompt the chat with a request like:
    > "Refactor this function to also remove the default WooCommerce 'button' class and add a subtle box shadow on hover."

## 3. Optimizing WooCommerce with Copilot

The Vibe Woo Bold theme is built to be fast. Use Copilot to maintain this performance.

| Optimization Task | Copilot Prompt Strategy | File Location |
| :--- | :--- | :--- |
| **Remove Bloat** | `// Remove all default WooCommerce CSS styles to ensure only Tailwind is used.` | `functions.php` |
| **Custom Template** | `// Create a minimalist single product template that wraps the main content in a max-w-4xl container.` | `woocommerce/single-product.php` (Requires creating this file) |
| **Performance** | `// Add a transient cache for the 'New Arrival' product query, expiring after 1 hour.` | `functions.php` |

## 4. Troubleshooting and Best Practices

1.  **Break it Down**: If Copilot suggests a massive block of code, delete it and try again with a smaller, more specific prompt [2].
2.  **Check the Docs**: Always verify the generated code against the official [WooCommerce Developer Documentation](https://developer.woocommerce.com/docs/theming/theme-development/theme-design-ux-guidelines/). Copilot is a co-pilot, not the pilot.
3.  **Tailwind Context**: If Copilot is suggesting incorrect Tailwind classes, ensure your `header.php` is open so it can read your custom color and font definitions.

## References

[1] [Copilot + WordPress: A Few Tips - Brian Coords](https://www.briancoords.com/copilot-wordpress-a-few-tips/)
[2] [Best Practices for Using GitHub Copilot - GitHub Docs](https://docs.github.com/en/copilot/get-started/best-practices)
