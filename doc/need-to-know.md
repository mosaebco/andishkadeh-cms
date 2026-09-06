# Andishkadeh CMS — Need to Know and Decision Log

## Usage

Read this file with [the current blueprint](blueprint.md) before beginning a new milestone. Answer one question at a time with the project owner. When accepted, record the answer, date, and owner without deleting earlier decision history.

## Accepted decisions

| ID | Date | Decision |
|---|---|---|
| D-001 | 2026-08-22 | Use Laravel; the WordPress statement in the original brief is superseded. |
| D-002 | 2026-08-22 | Use React + TypeScript through Inertia SSR for the public website. |
| D-003 | 2026-08-22 | Use Filament 5 under `/admin` for administration. |
| D-004 | 2026-08-22 | Use PostgreSQL in production. |
| D-005 | 2026-09-04 | No mobile application or separately deployed public API is planned. |
| D-006 | 2026-09-04 | The site has one administrator initially and no public user accounts. |
| D-007 | 2026-09-04 | All published content is publicly accessible. |
| D-008 | 2026-09-04 | **Superseded by D-021:** the earlier assumption was that every post belonged to exactly one ordered series. |
| D-009 | 2026-09-04 | Posts use bounded ordered blocks for text, headings, images, galleries, audio, uploaded/external video, links, quotes, and files. |
| D-010 | 2026-09-04 | **Superseded by D-058:** use draft, scheduled, published, and archived content states. |
| D-011 | 2026-09-04 | Use Program / برنامه for external classes, seminars, workshops, and similar registration targets. |
| D-012 | 2026-09-04 | **Superseded by D-043:** an earlier plan allowed free/paid programs and program-specific discounts. |
| D-013 | 2026-09-04 | **Superseded by D-043:** an earlier plan combined discounts with a 100% cap. |
| D-014 | 2026-09-04 | Do not build an internal form maker; redirect to a replaceable external form provider and receive selected fields on return. |
| D-015 | 2026-09-04 | **Superseded by D-043 for current scope:** an earlier plan stored selected registration, pricing, and payment details locally. |
| D-016 | 2026-09-04 | **Superseded by D-043 for current scope:** server-to-server verification belonged to the earlier registration/payment plan. |
| D-017 | 2026-09-04 | Actual course delivery and LMS behavior are outside this website. The administrator contacts registrants manually. |
| D-018 | 2026-09-04 | Use `https://jahaadetabin.com/` as the UI/UX reference while retaining original project content and branding. |
| D-019 | 2026-09-05 | The homepage begins with navigation tabs followed by an admin-controlled album/carousel of banners; each banner has an image and short description. |
| D-020 | 2026-09-05 | Content is public and can contain text, uploaded voice/audio, uploaded video, multiple videos, images, and labeled related links. |
| D-021 | 2026-09-05 | Content has standalone posts and ordered posts grouped under a series; a series may contain one post. |
| D-022 | 2026-09-05 | Courses are public information pages with text, one or more videos, a banner, and labeled links; the actual course delivery is external. |
| D-023 | 2026-09-05 | Books are public information pages with a banner, description text, and optional videos. |
| D-024 | 2026-09-05 | Announcements/news are public items with a banner, text, and labeled links. |
| D-025 | 2026-09-05 | The site needs an admin-controlled institute-registration section with description text and an external form-service link. |
| D-026 | 2026-09-05 | Donation, About, and Contact sections are required; Contact must support dynamic phones and links. |
| D-027 | 2026-09-05 | Every post, series, course, book, and announcement must have a visible title/header. Titles are required shared content fields; series also retain their own parent title. |
| D-028 | 2026-09-05 | Homepage order is: banner album, announcements/news, posts and series, courses, books, About Us, institute registration, and Contact Us. Announcements are a one-title-per-line list linking to detail pages. Posts/series, courses, and books use banner-and-title cards, with posts/series displayed as an album/grid of roughly 3–4 cards per row and up to two rows. Donation is a separate page linked from the top navigation. Navigation tabs link to every homepage section and the donation page. |
| D-029 | 2026-09-05 | Recommended Persian UI labels are centralized in `resources/js/locales/fa.ts` so wording can be changed without editing page components. |
| D-030 | 2026-09-05 | Content detail pages render media in a fixed order: required title/header and optional banner at the top, then text, then provided videos and images, then provided voice/audio, then provided labeled links. Empty sections are omitted. |
| D-031 | 2026-09-05 | Only `post` content items may belong to a series. Courses, books, and announcements are always standalone content items. |
| D-032 | 2026-09-05 | Banner rules by type: posts and series have optional banners; courses and books require a banner; announcements have an optional banner. Homepage carousel banners remain separate admin-managed records. |
| D-033 | 2026-09-05 | Donation is one configurable public page linked to one payment gateway in the first release; multiple campaigns are not needed initially. |
| D-034 | 2026-09-05 | Institute registration is one configurable public section with one external form-service link in the first release; multiple campaigns/forms are deferred. |
| D-035 | 2026-09-05 | Contact supports landline/mobile phones, email, address/map, Telegram, Instagram, WhatsApp or Eitaa, and arbitrary labeled links. Each contact method has visibility and admin-controlled display order. |
| D-036 | 2026-09-06 | The first public release is Persian-only. The frontend remains translation-ready so English can be added later without redesigning the content model. |
| D-038 | 2026-09-06 | Uploaded media uses local server storage for the first release and external media URLs are allowed. Configurable upload limits are 2 MB per image, 50 MB per audio file, and 100 MB per video; values live in .env and config/media.php. |
| D-039 | 2026-09-06 | The initial administrator's production name and email will be finalized later; current development credentials remain temporary and must not be treated as production credentials. |
| D-040 | 2026-09-06 | Development may use placeholder logo, banners, images, and Persian content. Production assets will be supplied and replaced later. |
| D-041 | 2026-09-06 | The external registration form provider is not selected yet. Keep registration behind a replaceable provider adapter until a service/API is chosen. |
| D-043 | 2026-09-06 | Program/course payment, discount evaluation, and registration transaction processing are out of the current scope. Donation is the only payment flow for now; discount announcements are ordinary announcement content. |
| D-042 | 2026-09-06 | A course is only an informational single content item, using the shared content structure (title, required banner, text, media, and links). Course-specific capacity, schedules, completion states, and other management fields are not part of the course entity now; registration metadata remains a separate future concern. |
| D-037 | 2026-09-06 | Public content uses readable category-plus-slug routes (`/posts/{slug}`, `/series/{slug}`, `/courses/{slug}`, `/books/{slug}`, `/announcements/{slug}`). Slugs are generated from titles, editable by admin, unique within their category, and stored independently of numeric IDs. |
| D-044 | 2026-09-06 | The Donation page accepts a custom visitor-entered amount and also shows optional admin-configurable quick-select amounts. |
| D-045 | 2026-09-06 | Donation amounts use Iranian tomans. The gateway is not selected yet, and sandbox/test credentials will be available when it is chosen. |
| D-046 | 2026-09-06 | Donation amounts must be positive whole-toman values. The minimum is admin-configurable; no maximum is imposed unless required by the selected gateway. |
| D-047 | 2026-09-06 | Donations are anonymous. No donor name, mobile number, email, or other contact information is required or stored. |
| D-048 | 2026-09-06 | The admin panel shows Donation transaction records with amount, payment status, gateway reference, and created/verified timestamps only; no donor identity data is stored. |
| D-050 | 2026-09-06 | The Donation gateway is not selected yet. The project owner will provide its details later; implement the payment integration behind a replaceable gateway adapter. |
| D-051 | 2026-09-06 | After a Donation gateway return, show a public success/failure result page with the amount and transaction/reference number, without donor identity fields. |
| D-052 | 2026-09-06 | The official organization name is «اندیشکده علوم و فناوری‌های نرم انقلاب اسلامی» and may be used in the header, footer, About section, and page titles. |
| D-053 | 2026-09-06 | Keep the current placeholder logo during development until the project owner supplies the official logo file. |
| D-054 | 2026-09-06 | Use Vazirmatn as the temporary Persian web typeface. It can be replaced if the project owner supplies a preferred production typeface. |
| D-055 | 2026-09-06 | Site-wide search is deferred. The first release focuses on the homepage, publishing, content detail pages, site sections, and Donation. |
| D-056 | 2026-09-06 | Books are informational pages only. No product catalog, book shop, purchasing, or commerce flow is planned. |
| D-057 | 2026-09-06 | The single institute-registration section with its external form is sufficient; no separate membership-application workflow is planned. |
| D-058 | 2026-09-06 | Remove the unused `scheduled` content status. Use `published` with a future `published_at` for automatic publication at a selected time; valid statuses are `draft`, `published`, and `archived`. |

## Current milestone questions

These do not block the implemented functional foundation, but must be answered before declaring Milestone 1 final.

### Q-001: Production brand assets

- Status: **Partially answered — logo/typeface/imagery pending**
- Priority: High

Provide the production logo, preferred Persian typeface, official organization name, and real imagery. Who gives final approval of these brand details?

**Answer (2026-09-06):** The official organization name is confirmed as «اندیشکده علوم و فناوری‌های نرم انقلاب اسلامی». Keep the current placeholder logo until the official logo is supplied. Use Vazirmatn as the temporary Persian typeface; real imagery remains deferred.

### Q-002: Public language strategy

- Status: **Accepted for first release**
- Priority: High

Is the production website Persian-only or bilingual? If bilingual, should every series/post require translations and should URLs use `/fa` and `/en` prefixes?

**Answer (2026-09-06):** Persian-only for now. Keep the frontend translation-ready for a future English version; bilingual content and `/fa`/`/en` URL prefixes are deferred.

### Q-003: Slug policy

- Status: **Accepted**
- Priority: Medium

Should the admin enter slugs manually, or should they be generated automatically? Should public slugs be Persian or Latin/transliterated?

**Answer (2026-09-06):** Use readable category-plus-slug routes: `/posts/{slug}`, `/series/{slug}`, `/courses/{slug}`, `/books/{slug}`, and `/announcements/{slug}`. Generate editable Latin slugs from titles, enforce uniqueness within each category, and keep numeric IDs internal.

### Q-004: Media limits and hosting

- Status: **Accepted**
- Priority: High

Where will production run, and what storage/bandwidth is available? Confirm maximum image, audio, video, and downloadable-file sizes. Should uploads remain on the server or move to object storage?

**Answer (2026-09-06):** Use local server storage for the first release and allow external media URLs. Upload limits are configurable through .env/config/media.php: images 2 MB, audio 50 MB, and videos 100 MB. Downloadable-file uploads keep a separate configurable 50 MB default.

### Q-005: Initial administrator

- Status: **Deferred**
- Priority: High before handoff

What name and email should the initial administrator use? The password should be created securely at deployment rather than committed to source control.

**Answer (2026-09-06):** Defer the administrator's production name and email until a later discussion. The existing development account is temporary; production credentials must be set securely at deployment.

### Q-006: Real content for review

- Status: **Deferred**
- Priority: Medium

Provide representative banners, one single-post series, one multi-post series, and mixed Persian media content so the content model and responsive UI can be reviewed realistically.

**Answer (2026-09-06):** Proceed with placeholders for now. Production logo, imagery, and final Persian content will be supplied later and must be replaceable without structural changes.

### Q-007: Homepage section order

- Status: **Accepted**
- Priority: High

After the banner album, should the first page show posts/series, courses, books, announcements, institute registration, donation, About, and Contact in exactly that order, or should any group move?

**Answer (2026-09-05):** The homepage order is banner album → announcements/news → posts and series → courses → books → About Us → institute registration → Contact Us. Announcements show one title per line and link to their detail pages. Posts/series, courses, and books use banner/title cards; posts/series target approximately 3–4 cards per row and up to two rows. Donation is a separate page linked from the top tab bar. Navigation tabs must link to each homepage section and to Donation.

### Q-008: Production content naming

- Status: **Accepted (initial labels; editable in translation file)**
- Priority: High

What labels should visitors see for the shared types: `post/article`, `course/program`, `book`, and `announcement/news`? Provide the preferred Persian terms for navigation and admin screens.

**Answer (2026-09-05):** Use these initial Persian labels: «اخبار و اطلاعیه‌ها» (announcements/news), «مطالب» (posts), «مجموعه‌ها» (series), «دوره‌ها و برنامه‌ها» (courses/programs), «کتاب‌ها» (books), «درباره ما» (About Us), «ثبت‌نام در مؤسسه» (institute registration), «ارتباط با ما» (Contact Us), and «حمایت مالی» (Donation). They are suggestions and must remain editable through the frontend translation file `resources/js/locales/fa.ts`.

### Q-009: Shared content table approval

- Status: **Accepted**
- Priority: Critical

May posts, courses, books, and announcements share one `content_items` table with a `type` field, while type-specific data lives in small extension tables? Standalone posts would have a nullable `series_id`; related links would use a shared `content_links` table.

Recommendation: approve this unified model. It prevents four duplicate editors/tables, makes homepage grouping consistent, and keeps future content types inexpensive. Use separate extension tables only where behavior is genuinely unique, such as course registration/pricing.

**Answer (2026-09-05):** Yes. Use one `content_items` table with a `type` discriminator. Keep `series_id` nullable so posts may be standalone or grouped in a series, and use a shared `content_links` table for labeled links. Type-specific behavior such as course registration and pricing belongs in small extension tables.

### Q-010: Media ordering and storage

- Status: **Accepted**
- Priority: High

For each content item, should audio, videos, images, and links appear in one mixed reading order, or should they be separate media sections? The current recommendation is one bounded ordered block list, with repeatable media blocks and a separate labeled-links relation.

**Answer (2026-09-05):** Use a fixed presentation order: title and banner first; text second; videos and images next; voices/audio after media; and labeled related links last. Omit any section that has no content. The admin may manage multiple items within each media section, but does not interleave the sections.

### Q-011: Series membership

- Status: **Accepted**
- Priority: High

Should only `post` items be allowed inside a series, with courses/books/announcements always standalone? Recommendation: yes for the first release.

**Answer (2026-09-05):** Yes. Only `post` items may belong to a series. Courses, books, and announcements remain standalone.

### Q-012: Banner meaning

- Status: **Accepted**
- Priority: Medium

Should every content item have one optional hero/banner image, while homepage carousel banners remain separate records? Recommendation: yes; this prevents a post's image from automatically appearing in the top carousel.

**Answer (2026-09-05):** Posts and series may have an optional banner/cover. Courses and books require a banner. Announcements may have an optional banner. Homepage carousel banners are separate admin-managed records and are not automatically populated from content-item banners.

### Q-013: Donation section behavior

- Status: **Accepted**
- Priority: High before donation work

Is donation a single admin-configured page/CTA that sends users to one gateway, or will there be multiple campaigns with separate descriptions, amounts, and reporting?

**Answer (2026-09-05):** Use one configurable Donation page with one payment-gateway destination. Multiple campaigns and separate reporting are deferred.

### Q-014: Institute registration section

- Status: **Accepted**
- Priority: High before this section is built

Is there one permanent institute-membership form link, or should the admin be able to maintain multiple registration campaigns/forms over time?

**Answer (2026-09-05):** Use one admin-configurable institute-registration section with one external form-service URL. Multiple registration campaigns/forms are deferred.

### Q-015: Contact methods

- Status: **Accepted**
- Priority: High before Contact is built

Which contact types must be supported initially: landline/mobile phones, email, Telegram, Eitaa, Instagram, WhatsApp, address/map, and arbitrary labeled links? Should each have visibility and display-order controls?

**Answer (2026-09-05):** Support landline/mobile phones, email, address/map, Telegram, Instagram, WhatsApp or Eitaa, and arbitrary labeled links. Each contact method has visibility and admin-controlled display order.

### Q-016: Donation amount selection

- Status: **Accepted**
- Priority: High before Donation work

Should visitors enter a custom donation amount, choose predefined amounts, or have both?

**Answer (2026-09-06):** Support both: custom amount entry plus optional admin-configurable quick-select amounts.

### Q-017: Donation gateway and currency

- Status: **Deferred — gateway selection pending**
- Priority: High before Donation work

Which payment gateway should Donation use, and should amounts be entered in Iranian rials or tomans? Confirm whether sandbox/test credentials will be available.

**Answer (2026-09-06):** Use Iranian tomans. The gateway is not decided yet; the project owner will provide details later, along with sandbox/test credentials. Keep the integration replaceable.

### Q-018: Donation amount validation

- Status: **Accepted**
- Priority: High before Donation work

What validation should apply to custom and quick-select donation amounts: minimum amount, maximum amount, and whether amounts must be whole tomans? Recommendation: require positive whole-toman amounts, make the minimum admin-configurable, and leave the maximum unset unless the selected gateway requires one.

**Answer (2026-09-06):** Approved. Require positive whole-toman amounts, make the minimum admin-configurable, and leave the maximum unset unless required by the selected gateway.

### Q-019: Donation donor details

- Status: **Accepted**
- Priority: High before Donation work

Should donations support anonymous payment, or must the donor provide contact details such as name, mobile number, and email? Recommendation: allow anonymous donations and make optional contact fields available for receipts or follow-up.

**Answer (2026-09-06):** Donations are fully anonymous. Do not ask for or store donor contact information.

### Q-020: Donation transaction records

- Status: **Accepted**
- Priority: High before Donation work

Should the admin panel show a Donation transaction list containing only operational data such as amount, status, gateway reference, and created/verified timestamps? Recommendation: yes, without any donor identity fields.

**Answer (2026-09-06):** Yes. Store and show only amount, status, gateway reference, and created/verified timestamps in the admin panel.

### Q-021: Donation completion

- Status: **Accepted**
- Priority: Medium before Donation work

After returning from the gateway, should the donor see a public success/failure result page showing the amount and a transaction/reference number? Recommendation: show a clear result page without revealing or requesting donor identity information.

**Answer (2026-09-06):** Yes. Show a public success/failure result page with the amount and transaction/reference number, without requesting or revealing donor identity information.

### Q-022: Search scope

- Status: **Deferred**
- Priority: Medium

Should the first release include site-wide search across titles and content, or should search be deferred until after the homepage/content foundation? Recommendation: defer full search so the first release focuses on publishing and the approved homepage sections.

**Answer (2026-09-06):** Defer site-wide search for now.

### Q-023: Product/shop scope

- Status: **Accepted**
- Priority: Low

Should the website include products, a book shop, or any product/catalog purchasing flow, or are books only informational pages? Recommendation: keep books informational and omit product/shop functionality from the first release.

**Answer (2026-09-06):** Books remain informational pages only. Omit product catalog, shop, purchasing, and commerce functionality.

### Q-024: Membership applications

- Status: **Accepted**
- Priority: Low

Is a separate membership-application workflow needed, or is the single institute-registration section and its external form enough? Recommendation: do not add a separate membership workflow unless you explicitly need it later.

**Answer (2026-09-06):** The single institute-registration section with its external form is enough. Do not add a separate membership-application workflow.

## Registration milestone questions

These are deliberately deferred until registration work begins.

### Q-101: External form provider

- Status: **Deferred**

Which form service/API will be used? It must support redirect correlation and return the minimum required fields. Signed return data or server-side lookup is strongly recommended before real discounts affect payment.

**Answer (2026-09-06):** Not decided yet. Defer provider selection and keep the integration replaceable.

### Q-102: Program fields and availability

- Status: **Accepted for current scope**

Confirm program fields: title, description, image, free/paid price, registration window, optional capacity, external platform details, and draft/open/closed/completed states.

**Answer (2026-09-06):** Do not add those program-management fields to the course entity now. A course is a single informational content item with the shared title/banner/text/media/links structure. Registration and payment metadata, if needed later, will be added separately.

### Q-103: Discount field contract

- Status: **Accepted for current scope**

How will each external form field map to configurable discount conditions? Are discounts always percentages, or may fixed-amount discounts exist? How are missing or unexpected return fields handled?

**Answer (2026-09-06):** No discount engine is needed now. Discounts may be mentioned as news in announcement items only. Program discount rules and external-form field mapping are deferred with program registration/payment.

### Q-104: Return security and correlation

- Status: **Deferred**

How does Laravel prove that a returning visitor completed the correct form for the correct program, and prevent replay or modification? If provider verification remains deferred, is the business willing to accept tamperable discounts?

**Answer (2026-09-06):** Deferred because program registration/payment is out of the current scope. Revisit verification before implementing any future paid registration.

### Q-105: Payment gateway and money unit

- Status: **Deferred**

Which Iranian gateway will be used? Does it expect rials or tomans? Is sandbox access available, and what are its verification/refund capabilities?

**Answer (2026-09-06):** Deferred. The only current payment integration is the Donation gateway; program payment gateway details will be decided later.

### Q-106: Registration lifecycle

- Status: **Deferred**

What admin states are required after registration—for example new, contacted, enrolled externally, completed, cancelled, and refunded? What happens immediately after a free or successful paid registration?

**Answer (2026-09-06):** Deferred with program registration/payment.

### Q-107: Cancellation, capacity, and refund rules

- Status: **Deferred**

Can programs fill up? When is a seat reserved? Can users cancel, and what refund rules apply?

**Answer (2026-09-06):** Deferred with program registration/payment.

## Scope confirmation for later

### Q-201: Remaining original features

- Status: **Deferred**

Are products, donations, membership applications, custom-course requests, social links, and advanced search still wanted? They are neither removed nor planned until reconfirmed.

**Answer (2026-09-06):** Do not generate further clarification questions from the original brief. The current agreed scope is authoritative; any remaining original feature is deferred unless the project owner explicitly requests it later. Products, membership applications, and search have already been excluded or deferred; Donation and Contact/social links are part of the current scope.
