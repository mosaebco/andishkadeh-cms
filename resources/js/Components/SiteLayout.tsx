import { Head, Link } from '@inertiajs/react';
import { useState, type PropsWithChildren } from 'react';
import fa from '../locales/fa';

type Props = PropsWithChildren<{ title?: string }>;

export default function SiteLayout({ children, title }: Props) {
    const [menuOpen, setMenuOpen] = useState(false);

    return (
        <>
            <Head title={title} />
            <header className="site-header">
                <div className="shell header-inner">
                    <Link href="/" className="brand" aria-label="صفحه اصلی اندیشکده">
                        <span className="brand-seal" aria-hidden="true">
                            <span>تبیین</span>
                        </span>
                        <strong>اندیشکده علوم و فناوری‌های نرم</strong>
                    </Link>

                    <button
                        className="menu-button"
                        type="button"
                        aria-expanded={menuOpen}
                        aria-controls="main-navigation"
                        onClick={() => setMenuOpen((open) => !open)}
                    >
                        <span />
                        <span />
                        <span />
                        <b>منو</b>
                    </button>

                    <nav id="main-navigation" className={menuOpen ? 'is-open' : ''} aria-label="ناوبری اصلی">
                        <a href="/#announcements">{fa.nav.announcements}</a>
                        <a href="/#posts-series">{fa.nav.postsAndSeries ?? fa.nav.posts}</a>
                        <a href="/#courses">{fa.nav.courses}</a>
                        <a href="/#books">{fa.nav.books}</a>
                        <a href="/#about">{fa.nav.about}</a>
                        <a href="/#registration">{fa.nav.registration}</a>
                        <a href="/#contact">{fa.nav.contact}</a>
                        <Link href="/donation">{fa.nav.donation}</Link>
                    </nav>

                    <div className="header-search" role="search">
                        <span>جستجو</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.35-4.35m2.35-5.15a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" /></svg>
                    </div>
                </div>
            </header>
            <main>{children}</main>
            <footer className="site-footer">
                <div className="shell footer-inner">
                    <p>اندیشکده علوم و فناوری‌های نرم انقلاب اسلامی</p>
                    <a href="#top">بازگشت به بالا ↑</a>
                </div>
            </footer>
        </>
    );
}
