# Andishkadeh CMS — Current Blueprint

## Document status

- Status: **Active working blueprint — first public content release under implementation**
- Revised scope agreed through: 2026-09-06
- Original brief: [English website requirements](website-requirements-en.md)
- Decisions and remaining questions: [Need to know](need-to-know.md)

> Before starting another feature, read this file and [Need to know](need-to-know.md). This blueprint supersedes conflicting course/LMS and WordPress statements in the original brief.

> **Scope revision notice (2026-09-05):** The homepage prototype originally used separate `posts` and `series` records. The requested product now uses the broader shared content model for posts, courses, books, and announcements. The shared model in section 3 was approved through Q-009 and is now the primary write/read model; the legacy `posts` table remains for compatibility.

## 1. Confirmed architecture

| Layer | Choice |
|---|---|
| Backend and business logic | Laravel 13 |
| Public website | React + TypeScript + Inertia SSR |
| Admin panel | Filament 5 at `/admin` |
| Production database | PostgreSQL |
| Application shape | One modular Laravel application |
| Public user accounts | None |
| Staff accounts | One administrator initially |
| Mobile application/API | Not planned |

The first public release is Persian-only (RTL). UI copy is kept in translation resources so an English locale can be added later without changing the content model; bilingual content and locale-prefixed URLs are deferred.

The local environment may temporarily use SQLite, but production and concurrency-sensitive integration tests must use PostgreSQL.

Uploaded media uses the local server's public disk for the first release, while external media URLs remain supported. Upload limits are deployment configuration, not hardcoded form rules: MEDIA_IMAGE_MAX_MB=2, MEDIA_AUDIO_MAX_MB=50, and MEDIA_VIDEO_MAX_MB=100 in .env, exposed through config/media.php. A separate MEDIA_DOWNLOAD_MAX_MB default controls downloadable files.

## 2. Revised product scope

The website now has these public responsibilities:

1. Show an admin-controlled album/carousel of banners immediately below the navigation tabs.
2. Publish flexible multimedia content, either as one standalone item or as ordered items in a series.
3. Introduce external classes, seminars, workshops, and similar programs through informational content pages. Program registration/payment and discount processing are not part of the current release.
4. Introduce books through content pages.
5. Publish announcements such as registration openings, discounts, and other news.
6. Provide a donation entry point connected to a payment gateway.
7. Show an administrator-controlled institute-registration section linking to an external form service.
8. Show dynamic About and Contact sections, including configurable contact links and phone numbers.

The website is **not** a course delivery platform. Courses are public informational pages only. It will not host lessons, learning progress, quizzes, certificates, student accounts, a learning dashboard, program payments, or discount evaluation. Discount offers may be published as ordinary announcements. Donation is the only payment flow in the current release.

The official organization name is «اندیشکده علوم و فناوری‌های نرم انقلاب اسلامی». The approved reference for the public UI remains [jahaadetabin.com](https://jahaadetabin.com/). The first page should follow its high-level composition—header/tabs, banner album, content groups/cards, registration/donation calls to action, About, and Contact—while using Andishkadeh's own content, logo, and assets.

Use Vazirmatn as the temporary Persian web typeface. The placeholder logo and demo imagery remain replaceable until production assets are supplied.

Every post, series, course, book, and announcement has a required visible title/header. Shared content records store this in `content_items.title`; series store their own parent title.

Initial Persian navigation, section, and content-type labels are centralized in [`resources/js/locales/fa.ts`](../resources/js/locales/fa.ts). These are editable copy defaults, not immutable domain names; the first release is Persian-only and a future English locale remains possible.

Public detail URLs use readable category-plus-slug routes (`/posts/{slug}`, `/series/{slug}`, `/courses/{slug}`, `/books/{slug}`, `/announcements/{slug}`). Slugs are generated from titles, editable by the administrator, and unique within their category; numeric IDs remain internal.

## 3. Approved unified content model (Q-009)

The repeated fields across posts, courses, books, and announcements indicate one shared **content item** table rather than four nearly identical tables. A type discriminator controls labels, listing placement, and type-specific behavior.

### 3.1 Shared `content_items` record

Proposed common fields:

- `id`
- `type`: `post`, `course`, `book`, or `announcement`
- `title` (required; shown as the item's header)
- `slug`
- `summary`/short description
- `body` or ordered `content_blocks` JSONB
- Optional `banner_image_path` for the item hero/banner; validation requires it for courses and books, while posts and announcements may omit it
- Publication status: `draft`, `scheduled`, `published`, `archived`
- `published_at`
- `sort_order`
- SEO fields when SEO work begins

Content data supports the media combinations requested, but the public detail-page renderer uses a fixed section order:

- Required title/header and optional banner image
- Text/rich text
- One or more videos and/or images (including external video links where needed)
- One or more uploaded voice/audio files
- Related links with a label explaining each link

The current bounded Filament Builder can remain the storage mechanism, but the editor and renderer must group blocks into the fixed sections above rather than allowing arbitrary interleaving. Multiple videos/images/audio items are supported within their respective sections without introducing a general page-builder product.

### 3.2 Series

Keep `series` as a separate parent entity because it has its own title, description, optional cover/banner, publication state, and ordering. Add a nullable `series_id` to `content_items`:

- `series_id = null`: standalone post
- `series_id != null`: ordered post in that series

Only `content_item.type = post` may belong to a series. Courses, books, and announcements are always standalone. A series may contain one post, so a standalone post can be promoted into a series without changing its content structure.

### 3.3 Related links

Use a shared `content_links` table rather than embedding links in every type-specific table:

- `content_item_id`
- `label`
- `url`
- `sort_order`
- Optional `is_external`/link target policy

This supports labeled links on posts, courses, books, and announcements consistently and allows Filament reordering.

### 3.4 Course/program registration extension

Treat a course page as `content_items.type = course`, using only the shared informational content structure. Do not add capacity, schedules, completion states, or other program-management fields to the course entity now. If registration is built later, keep its data in a one-to-one `program_details` extension (or an equivalent dedicated table) rather than changing the shared content record. That future extension may hold:

- Free/paid status and base price
- Registration opening/closing dates
- External form URL/provider key
- Discount-rule configuration
- Registration/payment enabled state

This keeps the public course page and future paid registration connected without turning the general content table into a course table.

### 3.5 Shared site sections

- **Top banner album:** keep a dedicated `banners` table; these are homepage carousel records, not content-item hero images.
- **Donation:** use one configurable public page backed by one payment-gateway destination and donation transaction records, not a fake post. Visitors may enter a custom amount or choose from optional admin-configurable quick-select amounts. Amounts are entered and displayed as positive whole Iranian tomans. The minimum is admin-configurable; no maximum is imposed unless required by the selected gateway. Donations are fully anonymous; no donor contact fields are collected or stored. The admin sees only amount, status, gateway reference, and created/verified timestamps. After a gateway return, show a public success/failure result page with the amount and transaction/reference number. The gateway is not selected yet; sandbox credentials are expected. Multiple campaigns are deferred.
- **Institute registration:** use one configurable singleton section with description text and one external form URL. Multiple campaigns/forms are deferred.
- **About:** use a configurable singleton page/section with rich text and optional media.
- **Contact:** use a settings record plus repeatable `contact_methods` (label, type, value, icon/order/visibility) for landline/mobile phones, email, address/map, Telegram, Instagram, WhatsApp/Eitaa, and arbitrary labeled links. Admin controls visibility and display order.

### 3.6 Homepage data groups

The homepage should query each group intentionally:

1. Active banner album
2. Published announcements/news (a one-title-per-line list; each title links to its detail page)
3. Latest/featured posts and series (banner/title cards, approximately 3–4 cards per row and up to two rows)
4. Published courses/programs (banner/title cards)
5. Published books (banner/title cards)
6. About section
7. Institute-registration CTA
8. Contact methods and footer

Donation is a separate public page, not a homepage section. The top navigation/tab bar must include links to every homepage section and to the Donation page. The exact number of cards shown and responsive column count can be finalized during UI review.

The admin must be able to publish/unpublish and order each group without editing code.

## 4. Current implementation milestone

The first public content milestone is implemented and being polished:

- A public Persian RTL homepage
- An admin-controlled sliding banner area at the top
- An announcements list with one title per line
- Post/series, course, and book card groups
- Public series, post, course, book, and announcement detail pages
- A bounded multimedia block editor with fixed public section ordering
- Filament resources for banners, series, shared content items, site sections, contact methods, and read-only donation transactions
- Draft, scheduled, published, and archived content states
- Configurable About, institute-registration, Contact, and Donation sections

The Donation gateway adapter and external registration provider are intentionally pending their real provider details. Program registration/payment, search, and book commerce remain out of scope. The existing separate `posts` implementation is retained as a compatibility route/resource while the unified Content resource is the primary admin workflow.

## 5. Content model (legacy prototype mapping)

### Banner

A banner contains:

- Title
- Optional subtitle
- Required image
- Optional button label and URL
- Display order
- Active/inactive state
- Optional start and end times

Only active banners inside their visibility window appear publicly. The homepage rotates through multiple visible banners.

### Series / مجموعه

A series is a titled, ordered collection of posts. It contains:

- Title
- Stable unique slug
- Optional description
- Optional cover image
- Display order
- Publication status and time

A series may contain only one post. This provides a consistent model for what would otherwise be standalone content.

### Current post prototype

The current prototype stores every post in `posts` and requires a series. The target model changes this to a nullable `series_id` on shared `content_items`, where a `post` can be standalone or grouped.

- Title
- Stable unique slug
- Optional excerpt and cover image
- Order within its series
- Publication status and time
- An ordered list of content blocks

Supported blocks in the first version:

- Rich text
- Heading
- Image
- Image gallery
- Uploaded audio/podcast
- Uploaded video
- External Aparat/YouTube video link
- General link card
- Quote
- Downloadable file

The block editor uses a bounded Filament Builder stored as PostgreSQL `jsonb`/Laravel JSON data. It is not a general-purpose page builder.

### Publication rules

- A record is public only when its status is `published` and its publication time is not in the future.
- A post is public only when its status/time are public and, if it has a `series_id`, its parent series is also public. Standalone posts with `series_id = null` do not require a series.
- Draft, future, and archived content must return `404` publicly.
- All published series and posts are public; there is no paid or account-restricted content.

## 6. Homepage blueprint

The public visual direction follows [jahaadetabin.com](https://jahaadetabin.com/) as the approved UI/UX reference: pale mint background, white RTL header, dotted teal separators, teal search treatment, a large centered image carousel, elevated series cards, a teal/white content panel, category tabs, and three-column post cards.

The target homepage should render these groups in the approved order:

1. Site header and navigation tabs
2. Sliding banner album
3. Announcements/news list
4. Posts/series card album
5. Courses/programs card area
6. Books card area
7. About Us
8. Institute-registration section
9. Contact Us and footer

Donation has its own public page and is linked in the top navigation. Navigation tabs also link to each homepage section.

Filament controls all data. The implementation reproduces the reference's layout system with original placeholder branding and demo content; production logo, wording, and real imagery remain to be supplied.

## 7. Deferred program registration/payment blueprint

Program/course registration transactions, discount evaluation, and program payments are outside the current release. Donation is the only active payment scope. The notes below preserve earlier discussions for possible future reconsideration and must not be implemented until the project owner explicitly reactivates this scope.

Use **Program / برنامه** as the shared term for a class, seminar, workshop, or similar registration target. The actual educational activity lives on another platform.

Previously discussed flow (deferred; not current scope):

```text
Visitor selects a program on this website
    → redirected to an external form provider
    → completes that program's form
    → redirected back with selected response details
    → Laravel calculates all eligible discounts
    → final price is capped at a minimum of zero
    → free registration completes immediately, or paid registration goes to the gateway
    → administrator sees the registrant and contacts them manually
```

Earlier provisional rules (all deferred):

- Programs may be free or paid.
- Each program may define different discount conditions.
- All matching discounts are combined.
- Combined discounts are capped at 100%; the website never produces a negative price.
- No eligibility evidence is required in the currently envisioned flow.
- Visitors register as guests; no public accounts exist.
- The external provider stores the full form response.
- Laravel stores only the required registration, pricing, contact, and payment fields.
- Initial local data: full name, mobile number, program, final price, applied discounts, external submission reference, payment status, and payment reference.
- A form builder will not be developed inside this application.
- The external form provider is not selected yet.
- Server-to-server form verification is deferred. Until a provider supports signed callbacks or verification, returned discount data is tamperable and must be treated as a known financial risk.

No registration code should be built until the relevant open questions in [Need to know](need-to-know.md) are resolved.

## 8. Security and operational rules

- Filament has login only; there is no public registration route.
- The administrator must have a verified email to access Filament.
- All public content queries enforce publication visibility server-side.
- Uploads use explicit types, sizes, paths, and the public storage disk.
- Rich content is authored only by the trusted administrator. Sanitization must be revisited before additional editor roles or imported HTML are introduced.
- Production must supervise the Inertia SSR Node process and Laravel queue workers.
- Production needs backups, restore testing, HTTPS, application monitoring, and PostgreSQL.
- Donation payment callbacks must be server-verified and idempotent.

## 9. Roadmap

### Milestone 1 — Homepage and publishing foundation

Status: **Prototype implemented; shared content refactor and expanded homepage pending**

- Laravel/Inertia/React SSR foundation
- Filament admin panel
- Banner management and carousel
- Shared content-item management for posts, courses, books, and announcements
- Series management for ordered post content
- Multimedia post blocks
- Public homepage with all approved content groups
- Public detail pages for content items and series
- Visibility and route tests
- Production client and SSR builds

Next review/implementation items:

- Run the interface with real sample content.
- Agree on visual identity and Persian typography.
- Review the content editor with the administrator.
- Validate the approved configurable upload limits against the production server before deployment.
- Implement the approved generated/editable Latin slugs.
- Migrate the prototype to the approved shared `content_items` model.
- Supply the production logo, banner images, and representative content for each type.

### Milestone 2 — Editorial completeness

- Final UI/UX system and responsive review
- SEO metadata, canonical links, Open Graph, sitemap, and redirects
- Search and content filtering (deferred; revisit after the first release)
- Shared related-links management and multi-media block review
- Categories/tags if series alone is insufficient
- Media cleanup and unused-upload handling
- Preview links and publication workflow refinements
- Accessibility and performance audit

### Milestone 3 — Program registration and payment (deferred)

No program registration transactions, discount engine, program payments, or registration records are planned in the current release. Revisit Q-101 through Q-107 only if the project owner explicitly reactivates this scope.

### Milestone 4 — Donation payment

- One configurable Donation page
- Iranian donation gateway adapter
- Donation amount input and validation
- Payment initiation, callback verification, reconciliation, and receipt/result page
- Failure and expiry handling
- Minimal admin donation transaction list

### Milestone 5 — Institute registration, About, and Contact

- Admin-controlled institute-registration section and external form URL
- Admin-controlled About section
- Dynamic contact methods (phones, links, email/social destinations)

### Milestone 6 — Remaining business features (only if later approved)

Products and book commerce are explicitly out of scope. Membership applications, custom requests, and any other remaining features must be reconfirmed before being placed into active scope.

## 10. Definition of done

A feature is complete when:

- Server-side authorization, validation, and visibility rules are enforced.
- Relevant PHP and frontend types/tests pass.
- Client and SSR production builds pass.
- RTL, keyboard access, responsive behavior, empty states, and failures are reviewed.
- Filament operations are usable without direct database access.
- Logs do not expose secrets or unnecessary personal data.
- Documentation and accepted decisions are updated.
- The feature is reviewed with representative real content.
