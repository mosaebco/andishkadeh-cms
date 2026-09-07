import AnnouncementList from '../Components/AnnouncementList';
import ContentCardGrid from '../Components/ContentCardGrid';
import SiteLayout from '../Components/SiteLayout';
import type { AnnouncementSummary, ContentCard } from '../types';

type Props = {
    title: string;
    eyebrow: string;
    kind: 'announcements' | 'cards';
    items: (AnnouncementSummary | ContentCard)[];
};

export default function Listing({ title, eyebrow, kind, items }: Props) {
    return (
        <SiteLayout title={title}>
            <section className="shell listing-page">
                <header className="section-heading listing-heading">
                    <div>
                        <span>{eyebrow}</span>
                        <h1>{title}</h1>
                    </div>
                </header>
                {kind === 'announcements' ? (
                    <AnnouncementList items={items as AnnouncementSummary[]} />
                ) : (
                    <ContentCardGrid items={items as ContentCard[]} />
                )}
            </section>
        </SiteLayout>
    );
}
