import { MaybeLocalizedContent } from "@/content/types";


export class ContentManager {
    maybeLocalized<Content extends MaybeLocalizedContent>(content: Content, callback: (content: string) => string): Content {
        if(!content) {
            return content;
        }
        if(typeof content === 'object') {
            return Object.fromEntries(
                Object.entries(content)
                    .map(([locale, localizedContent]) => [locale, callback(localizedContent ?? '')])
            ) as Content;
        }
        return callback(content) as Content;
    }

    allContent<Content extends MaybeLocalizedContent>(content: Content): string {
        if(!content) {
            return '';
        }
        if(typeof content === 'object') {
            return Object.values(content).join('\n');
        }
        return content;
    }

    contentDOM(content: string | null): Document {
        const parser = new DOMParser();
        return parser.parseFromString(content ?? '', 'text/html');
    }
}
