# Think Tank Website Design Requirements

# Historical brief — superseded by [blueprint.md](blueprint.md)

The active Laravel/React/Filament scope, including the removal of course registration/payment, book commerce, membership workflows, and WordPress-specific requirements, is maintained in `blueprint.md` and `need-to-know.md`.

In the name of God

## Website Objectives

- Communicate the think tank's activities, courses, and publications effectively and professionally
- Provide a platform for publishing multimedia content (text, video, and podcasts)
- Streamline course registration and user interaction
- Grow the think tank's audience and membership community
- Provide the infrastructure needed to attract financial support through cultural donations
- Enable electronic payments for product purchases and course enrollment

## Website Structure and Main Pages

### Home Page

- Announcement banner
- Display announcements, active courses, and events
- Allow banner management through the admin panel
- Provide a brief introduction to the think tank and its mission
- Display the latest articles and media productions
- Highlight courses currently open for registration
- Showcase products and support online sales
- Support course registration and online payment
- Provide quick access to social media accounts

### Search and Content Access

- Advanced site-wide search covering articles, courses, and products
- Table of contents for long-form articles
- Content categories and tags
- Content filters based on:
  - Publication date
  - Tags

### Articles and Content Production

- Support multiple content types within articles:
  - Text
  - Video
  - Podcasts
- Embed or upload video clips through Aparat
- Allow navigation to the previous and next articles
- Display related tags and categories
- Allow sharing on social media

### Courses and Education

#### Course Features

- Course introduction
- Brief explanation of the course objectives and intended audience
- Course syllabus
- Course trailer hosted on Aparat
- Course status: open for registration, in progress, or completed

#### Course Registration

- Smart registration form
- Conditional, dynamic pricing based on form responses
- Automatic final-price calculation displayed before the user submits the form
- Online registration and payment

Example discount logic:

| Participant category | Price or discount |
|---|---:|
| Base course price | 1,000,000 tomans |
| Homemaker with children | 20% discount |
| Homemaker | 15% discount |
| Teenager | 12% discount |

#### Custom Course Request Form

A dedicated form for organizations, groups, or other entities, with the following suggested fields:

- Name of the requesting organization or individual
- Course topic (for example, Palestinian studies or media literacy)
- Number of participants
- Additional details
- Contact information

### Think Tank Products

- Introduce and sell cultural products:
  - Books
  - Documentaries, if added in the future
- A dedicated page for each product containing:
  - Product description
  - Introductory image or video
  - Availability status or fulfillment instructions
- Online product sales and payment

### Think Tank Membership Form

The membership application form should collect:

- Full name
- Professional and academic background
- Skills and area of expertise
- Phone number
- Additional information

Administrators must be able to review and manage applications through the admin panel.

### Financial Support (Cultural Donations)

- A dedicated page for making monetary contributions to the think tank as cultural donations
- User-defined donation amounts
- Integration with a secure payment gateway

### Social Media

- Introduce and link to the think tank's social media accounts
- Display social media icons in:
  - The site header
  - The site footer
  - Content pages

## Technical and Design Considerations

- Responsive design for mobile, tablet, and desktop devices
- UX/UI principles appropriate for a formal think tank setting
- SEO-friendly structure for articles and course pages
- A simple, extensible admin panel
- Design and implementation using the WordPress content management system

## Support and Future Development

Following final delivery, the project will include three months of free support covering technical issue resolution and guidance on using the website.

The website will serve as a comprehensive media, educational, and interactive platform for the think tank. It will support future expansion, including additional courses, products, and documentaries. Proper implementation of this structure will help increase the think tank's impact, credibility, and audience engagement.
