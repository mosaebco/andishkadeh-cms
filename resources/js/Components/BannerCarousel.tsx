import { useEffect, useState } from 'react';
import type { Banner } from '../types';

export default function BannerCarousel({ banners }: { banners: Banner[] }) {
    const [active, setActive] = useState(0);

    const showPrevious = () => {
        setActive((current) => (current - 1 + banners.length) % banners.length);
    };

    const showNext = () => {
        setActive((current) => (current + 1) % banners.length);
    };

    useEffect(() => {
        if (banners.length < 2) return;

        const timer = window.setInterval(() => {
            setActive((current) => (current + 1) % banners.length);
        }, 6500);

        return () => window.clearInterval(timer);
    }, [banners.length]);

    if (banners.length === 0) {
        return (
            <section className="hero reference-hero" aria-label="معرفی اندیشکده">
                <div className="hero-slide is-active placeholder-slide">
                    <div className="hero-art" aria-hidden="true"><span>اندیشه</span></div>
                    <div className="hero-content">
                        <span className="hero-category">اندیشکده</span>
                        <h1>رسانه، اندیشه و گفت‌وگو</h1>
                        <p>مجموعه‌ای از نوشته‌ها و محتوای چندرسانه‌ای برای فهم دقیق‌تر موضوعات امروز</p>
                        <a className="primary-action" href="#posts-series">ادامه</a>
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section className="hero reference-hero" aria-roledescription="carousel" aria-label="بنرهای برگزیده">
            {banners.map((banner, index) => (
                <article
                    className={`hero-slide ${index === active ? 'is-active' : ''}`}
                    key={banner.id}
                    aria-hidden={index !== active}
                >
                    <img src={banner.imageUrl} alt={banner.title} />
                    <div className="hero-content">
                        <span className="hero-category">{banner.title}</span>
                        <h1>{banner.title}</h1>
                        {banner.subtitle && <p>{banner.subtitle}</p>}
                        {banner.linkUrl && (
                            <a
                                className="primary-action"
                                href={banner.linkUrl}
                                target={/^https?:\/\//i.test(banner.linkUrl) ? '_blank' : undefined}
                                rel={/^https?:\/\//i.test(banner.linkUrl) ? 'noreferrer' : undefined}
                                tabIndex={index === active ? 0 : -1}
                            >
                                {banner.linkLabel || 'ادامه'}
                            </a>
                        )}
                    </div>
                </article>
            ))}

            {banners.length > 1 && (
                <>
                    <button
                        type="button"
                        className="carousel-arrow carousel-arrow-previous"
                        onClick={showPrevious}
                        aria-label="نمایش بنر قبلی"
                    >
                        <span aria-hidden="true">‹</span>
                    </button>
                    <button
                        type="button"
                        className="carousel-arrow carousel-arrow-next"
                        onClick={showNext}
                        aria-label="نمایش بنر بعدی"
                    >
                        <span aria-hidden="true">›</span>
                    </button>
                    <div className="carousel-controls">
                        <div className="carousel-dots">
                            {banners.map((banner, index) => (
                                <button
                                    type="button"
                                    key={banner.id}
                                    className={index === active ? 'is-active' : ''}
                                    onClick={() => setActive(index)}
                                    aria-label={`نمایش بنر ${index + 1}`}
                                    aria-current={index === active}
                                />
                            ))}
                        </div>
                    </div>
                </>
            )}
        </section>
    );
}
