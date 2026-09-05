export type RelatedLink = {
    label: string;
    url: string;
    description: string | null;
};

export default function RelatedLinks({ links }: { links: RelatedLink[] }) {
    if (links.length === 0) {
        return null;
    }

    return (
        <section className="related-links" aria-labelledby="related-links-title">
            <h2 id="related-links-title">پیوندهای مرتبط</h2>
            {links.map((link) => {
                const external = /^https?:\/\//i.test(link.url);

                return (
                    <a
                        className="link-card"
                        href={link.url}
                        target={external ? '_blank' : undefined}
                        rel={external ? 'noreferrer' : undefined}
                        key={link.url + link.label}
                    >
                        <strong>{link.label}</strong>
                        {link.description && <span>{link.description}</span>}
                        <b aria-hidden="true">←</b>
                    </a>
                );
            })}
        </section>
    );
}
