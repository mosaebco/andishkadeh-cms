import type { ContentBlock } from '../types';

const publicFile = (path: unknown): string => `/storage/${String(path).replace(/^\/+/, '')}`;

function embedUrl(url: string): string | null {
    try {
        const parsed = new URL(url);

        if (parsed.hostname.includes('youtube.com')) {
            const id = parsed.searchParams.get('v');
            return id ? `https://www.youtube.com/embed/${id}` : null;
        }

        if (parsed.hostname === 'youtu.be') {
            return `https://www.youtube.com/embed/${parsed.pathname.replace('/', '')}`;
        }

        if (parsed.hostname.includes('aparat.com')) {
            const match = parsed.pathname.match(/\/v\/([^/]+)/);
            return match ? `https://www.aparat.com/video/video/embed/videohash/${match[1]}/vt/frame` : null;
        }
    } catch {
        return null;
    }

    return null;
}

export default function ContentBlocks({ blocks }: { blocks: ContentBlock[] }) {
    const orderedBlocks = [
        ...blocks.filter((block) => ['rich_text', 'heading', 'quote'].includes(block.type)),
        ...blocks.filter((block) => ['video', 'image', 'gallery'].includes(block.type)),
        ...blocks.filter((block) => block.type === 'audio'),
        ...blocks.filter((block) => ['link', 'download'].includes(block.type)),
    ];

    return (
        <div className="article-blocks">
            {orderedBlocks.map((block, index) => {
                const data = block.data ?? {};

                switch (block.type) {
                    case 'rich_text':
                        return <div className="rich-text" key={index} dangerouslySetInnerHTML={{ __html: String(data.body ?? '') }} />;
                    case 'heading': {
                        const level = Number(data.level) === 3 ? 'h3' : Number(data.level) === 4 ? 'h4' : 'h2';
                        const Heading = level;
                        return <Heading key={index}>{String(data.text ?? '')}</Heading>;
                    }
                    case 'image':
                        return (
                            <figure key={index}>
                                <img src={publicFile(data.path)} alt={String(data.alt ?? '')} loading="lazy" />
                                {data.caption ? <figcaption>{String(data.caption)}</figcaption> : null}
                            </figure>
                        );
                    case 'gallery':
                        return (
                            <div className="content-gallery" key={index}>
                                {(Array.isArray(data.paths) ? data.paths : []).map((path) => <img src={publicFile(path)} alt="" loading="lazy" key={String(path)} />)}
                            </div>
                        );
                    case 'audio':
                        return (
                            <figure className="audio-block" key={index}>
                                <audio controls preload="metadata" src={publicFile(data.path)} />
                                {data.caption ? <figcaption>{String(data.caption)}</figcaption> : null}
                            </figure>
                        );
                    case 'video': {
                        const localPath = data.path ? publicFile(data.path) : null;
                        const externalUrl = data.url ? String(data.url) : null;
                        const embedded = externalUrl ? embedUrl(externalUrl) : null;
                        return (
                            <figure className="video-block" key={index}>
                                {localPath && <video controls preload="metadata" src={localPath} />}
                                {!localPath && embedded && <iframe src={embedded} title={String(data.caption ?? 'ویدئو')} allowFullScreen loading="lazy" />}
                                {!localPath && externalUrl && !embedded && <a className="external-media" href={externalUrl} rel="noreferrer">مشاهده ویدئو در وب‌سایت اصلی ←</a>}
                                {data.caption ? <figcaption>{String(data.caption)}</figcaption> : null}
                            </figure>
                        );
                    }
                    case 'link':
                        {
                            const url = String(data.url ?? '#');
                            const external = /^https?:\/\//i.test(url);

                        return (
                            <a className="link-card" href={url} key={index} target={external ? '_blank' : undefined} rel={external ? 'noreferrer' : undefined}>
                                <strong>{String(data.label ?? '')}</strong>
                                {data.description ? <span>{String(data.description)}</span> : null}
                                <b aria-hidden="true">←</b>
                            </a>
                        );
                        }
                    case 'quote':
                        return <blockquote key={index}><p>{String(data.text ?? '')}</p>{data.citation ? <cite>{String(data.citation)}</cite> : null}</blockquote>;
                    case 'download':
                        return <a className="download-card" href={publicFile(data.path)} download key={index}>↓ {String(data.label ?? 'دریافت فایل')}</a>;
                    default:
                        return null;
                }
            })}
        </div>
    );
}
