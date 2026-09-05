import { Link } from '@inertiajs/react';
import type { AnnouncementSummary } from '../types';

export default function AnnouncementList({ items }: { items: AnnouncementSummary[] }) {
    if (items.length === 0) {
        return <p className="empty-copy">اطلاعیه‌ای برای نمایش وجود ندارد.</p>;
    }

    return (
        <div className="announcement-list">
            {items.map((item) => (
                <Link href={item.url} key={item.id}>
                    <span>{item.title}</span>
                    <b aria-hidden="true">←</b>
                </Link>
            ))}
        </div>
    );
}
