import { Link } from '@inertiajs/react';
import ContentBlocks from '../../Components/ContentBlocks';
import RelatedLinks, { type RelatedLink } from '../../Components/RelatedLinks';
import SiteLayout from '../../Components/SiteLayout';
import type { ContentBlock } from '../../types';

type Props = {
    post: {
        title: string;
        excerpt: string | null;
        coverImageUrl: string | null;
        publishedAt: string | null;
        contentBlocks: ContentBlock[];
        links: RelatedLink[];
        series: { title: string; slug: string } | null;
    };
};

export default function Show({ post }: Props) {
    return (
        <SiteLayout title={post.title}>
            <article className="article-page shell">
                <header className="article-header">
                    {post.series && <Link href={`/series/${post.series.slug}`} className="article-series">{post.series.title}</Link>}
                    <h1>{post.title}</h1>
                    {post.excerpt && <p>{post.excerpt}</p>}
                    {post.coverImageUrl && <img src={post.coverImageUrl} alt={post.title} />}
                </header>
                <ContentBlocks blocks={post.contentBlocks} />
                <RelatedLinks links={post.links} />
            </article>
        </SiteLayout>
    );
}
