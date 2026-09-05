import AnnouncementList from '../Components/AnnouncementList';
import BannerCarousel from '../Components/BannerCarousel';
import ContentCardGrid from '../Components/ContentCardGrid';
import SiteLayout from '../Components/SiteLayout';
import fa from '../locales/fa';
import type { AnnouncementSummary, Banner, ContactMethod, ContentCard, SiteSection } from '../types';

type Props = {
    banners: Banner[];
    postsAndSeries: ContentCard[];
    announcements: AnnouncementSummary[];
    courses: ContentCard[];
    books: ContentCard[];
    about: SiteSection;
    registration: SiteSection;
    contactMethods: ContactMethod[];
};

const contactHref = (method: ContactMethod): string | null => {
    const value = method.value.trim();

    if (!value) {
        return null;
    }

    if (method.type === 'phone' || method.type === 'mobile') {
        return 'tel:' + value;
    }

    if (method.type === 'email') {
        return 'mailto:' + value;
    }

    if (/^https?:\/\//i.test(value)) {
        return value;
    }

    const handle = value.replace(/^@/, '');

    if (method.type === 'telegram') return `https://t.me/${handle}`;
    if (method.type === 'instagram') return `https://instagram.com/${handle}`;
    if (method.type === 'eitaa') return `https://eitaa.com/${handle}`;
    if (method.type === 'whatsapp') {
        const phone = value.replace(/\D/g, '');

        return phone ? `https://wa.me/${phone}` : null;
    }

    return null;
};

function SectionHeading({ eyebrow, title }: { eyebrow: string; title: string }) {
    return (
        <header className="section-heading">
            <span>{eyebrow}</span>
            <h2>{title}</h2>
        </header>
    );
}

export default function Home({
    banners,
    postsAndSeries,
    announcements,
    courses,
    books,
    about,
    registration,
    contactMethods,
}: Props) {
    return (
        <SiteLayout>
            <div id="top" />
            <BannerCarousel banners={banners} />

            <section className="home-section shell announcements-section" id="announcements">
                <SectionHeading eyebrow="تازه‌ها" title={fa.sections.announcements} />
                <AnnouncementList items={announcements} />
            </section>

            <section className="home-section shell" id="posts-series">
                <SectionHeading eyebrow="خواندنی‌ها" title={fa.sections.postsAndSeries} />
                <ContentCardGrid items={postsAndSeries.slice(0, 8)} />
            </section>

            <section className="home-section shell" id="courses">
                <SectionHeading eyebrow="آموزش" title={fa.sections.courses} />
                <ContentCardGrid items={courses} />
            </section>

            <section className="home-section shell" id="books">
                <SectionHeading eyebrow="مطالعه" title={fa.sections.books} />
                <ContentCardGrid items={books} />
            </section>

            <section className="info-section shell" id="about">
                <div>
                    <SectionHeading eyebrow="آشنایی" title={about.title} />
                    <div className="section-rich-text" dangerouslySetInnerHTML={{ __html: about.body }} />
                </div>
            </section>

            <section className="cta-section shell" id="registration">
                <div>
                    <SectionHeading eyebrow="همراهی" title={registration.title} />
                    <div className="section-rich-text" dangerouslySetInnerHTML={{ __html: registration.body }} />
                </div>
                {registration.url && (
                    <a className="primary-action" href={registration.url} target="_blank" rel="noreferrer">
                        تکمیل فرم ثبت‌نام
                    </a>
                )}
            </section>

            <section className="contact-section shell" id="contact">
                <SectionHeading eyebrow="در تماس باشیم" title={fa.sections.contact} />
                {contactMethods.length > 0 ? (
                    <div className="contact-grid">
                        {contactMethods.map((method) => {
                            const href = contactHref(method);

                            return href ? (
                                <a
                                    href={href}
                                    key={method.label + method.value}
                                    target={href.startsWith('http') ? '_blank' : undefined}
                                    rel={href.startsWith('http') ? 'noreferrer' : undefined}
                                >
                                    <strong>{method.label}</strong>
                                    <span dir="auto">{method.value}</span>
                                </a>
                            ) : (
                                <div key={method.label + method.value}>
                                    <strong>{method.label}</strong>
                                    <span dir="auto">{method.value}</span>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    <p className="empty-copy">اطلاعات تماس به‌زودی تکمیل می‌شود.</p>
                )}
            </section>

        </SiteLayout>
    );
}
