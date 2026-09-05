import { Link } from '@inertiajs/react';
import type { PostSummary } from '../types';

const persianDate = new Intl.DateTimeFormat('fa-IR', { day: 'numeric', month: 'long', year: 'numeric' });

export default function PostList({ posts }: { posts: PostSummary[] }) {
    return (
        <div className="post-grid">
            {posts.map((post) => (
                <article key={post.slug}>
                    <Link href={`/posts/${post.slug}`} className="post-card-image">
                        {post.coverImageUrl ? (
                            <img src={post.coverImageUrl} alt={post.title} loading="lazy" />
                        ) : (
                            <span className="post-placeholder" aria-hidden="true"><b>اندیشه</b></span>
                        )}
                        <span className="post-kind">مطلب</span>
                    </Link>
                    <div className="post-card-body">
                        {post.publishedAt && (
                            <span className="post-meta">
                                <time dateTime={post.publishedAt}>{persianDate.format(new Date(post.publishedAt))}</time>
                            </span>
                        )}
                        <h3><Link href={`/posts/${post.slug}`}>{post.title}</Link></h3>
                        {post.excerpt && <p>{post.excerpt}</p>}
                        <Link href={`/posts/${post.slug}`} className="continue-button">ادامه ...</Link>
                    </div>
                </article>
            ))}
        </div>
    );
}
