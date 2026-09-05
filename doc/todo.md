# Andishkadeh CMS — Coding TODO

Read [blueprint.md](blueprint.md) and [need-to-know.md](need-to-know.md) before changing this list.

## Completed decisions

- [x] Answer Q-009: approved the shared `content_items` model before adding course, book, and announcement code.

## Shared content foundation

- [x] Design the migration from prototype `posts` to shared `content_items`.
- [x] Preserve the prototype `posts` table and model compatibility while the shared table becomes the write target.
- [x] Add `content_items.type` with server-side type validation.
- [x] Make `series_id` nullable and allow standalone posts.
- [x] Add shared banner/hero media field, including required-banner validation for courses and books.
- [x] Expand the bounded block editor for repeatable images, audio, videos, external media, and links.
- [x] Make media storage and upload limits environment-configurable (image 2 MB, audio 50 MB, video 100 MB).
- [x] Enforce the approved detail-page order: title/banner, text, videos/images, audio, then labeled links.
- [x] Add `content_links` with label and ordering.
- [x] Create a unified Filament resource for post, course, book, and announcement variants.
- [x] Add type-aware public queries, cards, and detail routes.
- [x] Add tests proving unpublished items stay private, standalone posts render, slugs are generated, and type rules are enforced.
- [x] Keep search deferred from the first release.

## Homepage groups

- [x] Refactor homepage data into banners, announcements, posts/series, courses, books, registration CTA, About, and Contact; keep Donation on its own page.
- [x] Implement approved homepage order: banners, announcements list, posts/series cards, course cards, book cards, About, registration, Contact; keep Donation as a separate page linked in the top navigation.
- [x] Add admin ordering/visibility controls per group.
- [x] Use the editable Persian labels in `resources/js/locales/fa.ts` throughout public navigation and section headings.
- [x] Keep public UI Persian-only for the first release while retaining translation-ready structure.
- [x] Match the approved reference layout at desktop, tablet, and mobile widths.
- [ ] Replace demo branding/content with supplied production assets.
- [ ] Replace placeholders with production logo, imagery, and approved Persian copy when supplied.

## Deferred program registration and payment — out of current scope

- [ ] If this scope is explicitly reactivated, resolve the external form provider and return-data contract.
- [ ] If this scope is explicitly reactivated, design separate registration data without expanding the informational course entity.
- [ ] If this scope is explicitly reactivated, revisit free/paid registration, discounts, verification, and lifecycle rules.
- [x] Keep program payment code out of the current release; Donation is the only payment integration.

## Site sections and operations

- [x] Add one configurable Donation page and gateway-pending state (multiple campaigns deferred).
- [x] Add custom donation amount input and admin-configurable quick-select amounts.
- [x] Expose positive whole-toman validation rules and an admin-configurable minimum in the donation UI; gateway-side submission remains pending gateway selection.
- [x] Keep Donation checkout anonymous; store no donor contact information.
- [ ] Confirm the Donation gateway before implementation; currency is Iranian tomans and sandbox credentials will be available.
- [x] Add one configurable institute-registration section with external form URL (multiple campaigns deferred).
- [x] Add About section editing.
- [x] Add repeatable Contact methods (phones, email, address/map, Telegram, Instagram, WhatsApp/Eitaa, arbitrary links) with visibility and display order.
- [ ] Add PostgreSQL integration environment and concurrency-sensitive tests.
- [ ] Add SEO, sitemap, redirects, accessibility, monitoring, backups, and deployment documentation.
- [ ] Keep book/product commerce out of scope unless explicitly requested later.
