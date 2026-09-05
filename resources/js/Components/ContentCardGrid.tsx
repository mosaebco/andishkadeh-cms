import { Link } from '@inertiajs/react';
import fa from '../locales/fa';
import type { ContentCard } from '../types';

const kindLabels: Record<ContentCard['type'], string> = {
    post: fa.contentTypes.post,
    series: fa.contentTypes.series,
    course: fa.contentTypes.course,
    book: fa.contentTypes.book,
    announcement: fa.contentTypes.announcement,
};

export default function ContentCardGrid({ items }: { items: ContentCard[] }) {
    if (items.length === 0) {
        return <p className="empty-copy">محتوایی برای نمایش وجود ندارد.</p>;
    }

    return (
        <div className="content-card-grid">
            {items.map((item) => (
                <article className="content-card" key={item.type + '-' + item.id}>
                    <Link href={item.url} className="content-card-image">
                        {item.coverImageUrl ? (
                            <img src={item.coverImageUrl} alt={item.title} loading="lazy" />
                        ) : (
                            <span className="content-placeholder" aria-hidden="true">اندیشه</span>
                        )}
                        <span className="content-card-kind">{kindLabels[item.type]}</span>
                    </Link>
                    <div className="content-card-body">
                        <h3><Link href={item.url}>{item.title}</Link></h3>
                        {item.excerpt && <p>{item.excerpt}</p>}
                    </div>
                </article>
            ))}
        </div>
    );
}
