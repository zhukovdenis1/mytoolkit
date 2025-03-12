import type { LinkData } from './types';

export type LinkAttributes = {
    href: string;
    target?: string | null;
    rel?: string | null;
    class?: string | null;
};


export const buildLinkAttributes = (data: LinkData): LinkAttributes => {
    const attributes: LinkAttributes = {
        href: data.href,
        target: data.target,
        class: data.class,
    };

    if (data.target === '_blank') {
        attributes.rel = 'nofollow noindex';
    }

    return attributes;
};
