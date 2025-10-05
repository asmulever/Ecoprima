# ASSETS.md

## 🎨 Purpose
This document defines how **Joules** and developers should handle static assets (images, icons, backgrounds, and media) in the EcoPrima repository.  
Its goal is to ensure **visual consistency**, **maintainability**, and **safe integration** of assets in the frontend.

---

## 🗂️ Folder Structure
All static resources are stored inside:

```
/htdocs/images/
```

**Subfolders:**
| Path | Purpose |
|------|----------|
| `/htdocs/images/backgrounds/` | Backgrounds and hero section images. |
| `/htdocs/images/icons/` | Interface icons and small decorative assets. |
| `/htdocs/images/logos/` | Brand logos and partner marks. |
| `/htdocs/images/uploads/` | User-uploaded media (if any). |

---

## 🖼️ Asset Usage Rules

1. **File Formats**
   - Prefer `.webp` for backgrounds (lightweight and modern).
   - Use `.png` for logos with transparency.
   - Use `.jpg` for photos and `.svg` for vector icons.

2. **Resolution**
   - Backgrounds: 1920×1080 or larger.
   - Logos/Icons: scalable, ≤512 KB each.
   - Avoid uncompressed assets.

3. **Naming Convention**
   ```
   bg_<page>.webp          → background for a specific page
   icon_<action>.svg       → icon for UI action
   logo_<organization>.png → company or partner logo
   ```
   Example:
   ```
   bg_marketplace.webp
   icon_upload.svg
   logo_ecoprima.png
   ```

---

## 🧭 Integration Guidelines

### CSS Integration
```css
body.marketplace {
  background-image: url('/htdocs/images/backgrounds/bg_marketplace.webp');
  background-size: cover;
  background-position: center;
}
.overlay {
  background-color: rgba(0, 0, 0, 0.4);
}
```

### PHP/HTML Reference
```php
<div class="page-header" style="background-image:url('/htdocs/images/backgrounds/bg_main.webp');">
  <div class="overlay">
    <h1>Bienvenido a EcoPrima</h1>
  </div>
</div>
```

### Accessibility
- Add descriptive `alt` text for all images.
- Avoid text embedded in images.

---

## ⚙️ Joules Usage Rules

When instructed to enhance or redesign the frontend:

- Joules **can use** any image located in `/htdocs/images/**`.  
- **Do not** rename or move existing images unless explicitly requested.  
- If new images are referenced, Joules must:
  1. Declare them in the generated code.
  2. Describe the intended visual context (e.g., “hero banner”, “footer texture”).
  3. Keep contrast high for text readability (use CSS overlays or gradients).
- All paths must be relative (`/htdocs/images/...`).

---

## 🧾 Licensing and Attribution
All images in this repository are internal to the EcoPrima project and not subject to external redistribution unless otherwise stated.  
Third-party assets must include attribution in `htdocs/images/README.md`.
