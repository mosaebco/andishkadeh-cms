import PostList from '../../Components/PostList';
import SiteLayout from '../../Components/SiteLayout';
import type { SeriesSummary } from '../../types';

export default function Show({ series }: { series: SeriesSummary }) {
    return (
        <SiteLayout title={series.title}>
            <section className="page-hero">
                <div className="shell page-hero-grid">
                    <div>
                        <span className="eyebrow">مجموعه</span>
                        <h1>{series.title}</h1>
                        {series.description && <p>{series.description}</p>}
                    </div>
                    {series.coverImageUrl && <img src={series.coverImageUrl} alt={series.title} />}
                </div>
            </section>
            <section className="shell listing-page">
                <h2>نوشته‌های این مجموعه</h2>
                {series.posts.length > 0 ? <PostList posts={series.posts} /> : <p className="muted">هنوز نوشته‌ای منتشر نشده است.</p>}
            </section>
        </SiteLayout>
    );
}
