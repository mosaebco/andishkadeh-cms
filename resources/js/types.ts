export type Banner = {
    id: number;
    title: string;
    subtitle: string | null;
    imageUrl: string;
    linkUrl: string | null;
    linkLabel: string | null;
};

export type PostSummary = {
    id?: number;
    type?: 'post';
    title: string;
    slug: string;
    excerpt: string | null;
    coverImageUrl: string | null;
    publishedAt: string | null;
    url?: string;
    seriesTitle?: string | null;
};

export type SeriesSummary = {
    id?: number;
    type?: 'series';
    title: string;
    slug: string;
    description: string | null;
    coverImageUrl: string | null;
    posts: PostSummary[];
    url?: string;
};

export type ContentBlock = {
    type: string;
    data: Record<string, unknown>;
};

export type ContentCard = {
    id: number;
    type: 'post' | 'series' | 'course' | 'book' | 'announcement';
    title: string;
    slug: string;
    excerpt: string | null;
    coverImageUrl: string | null;
    publishedAt: string | null;
    url: string;
    seriesTitle?: string | null;
};

export type AnnouncementSummary = ContentCard & {
    type: 'announcement';
};

export type SiteSection = {
    title: string;
    body: string;
    url?: string | null;
};

export type ContactMethod = {
    label: string;
    type: string;
    value: string;
    icon?: string | null;
};
