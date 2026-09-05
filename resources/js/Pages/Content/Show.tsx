import ContentBlocks from '../../Components/ContentBlocks';
import RelatedLinks, { type RelatedLink } from '../../Components/RelatedLinks';
import SiteLayout from '../../Components/SiteLayout';
import fa from '../../locales/fa';
import type { ContentBlock } from '../../types';

type ContentPage = {
    type: 'course' | 'book' | 'announcement';
    title: string;
    slug: string;
    excerpt: string | null;
    coverImageUrl: string | null;
    publishedAt: string | null;
    contentBlocks: ContentBlock[];
    links: RelatedLink[];
};

const labels = {
    course: fa.contentTypes.course,
    book: fa.contentTypes.book,
    announcement: fa.contentTypes.announcement,
};

export default function Show({ content }: { content: ContentPage }) {
    return (
        <SiteLayout title={content.title}>
            <article className="article-page shell">
                <header className="article-header">
                    <span className="article-kind">{labels[content.type]}</span>
                    <h1>{content.title}</h1>
                    {content.excerpt && <p>{content.excerpt}</p>}
                    {content.coverImageUrl && <img src={content.coverImageUrl} alt={content.title} />}
                </header>
                <ContentBlocks blocks={content.contentBlocks} />
                <RelatedLinks links={content.links} />
            </article>
        </SiteLayout>
    );
}
