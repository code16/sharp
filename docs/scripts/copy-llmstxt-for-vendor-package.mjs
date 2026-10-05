// Copies the generated llms.txt to docs/llms.txt, rewriting its site-absolute links
// ("/docs/guide/x.md", "/guide/x.md") to paths relative to docs/, so that they resolve
// when the docs are shipped in the composer package (vendor/code16/sharp/docs/).
import { readdirSync, readFileSync, writeFileSync } from 'node:fs';

const distDir = new URL('./.vitepress/dist/', import.meta.url);
const [slug] = readdirSync(distDir);

const content = readFileSync(new URL(`${slug}/llms.txt`, distDir), 'utf8')
    .replace(/\]\(\/(?:docs\/)?([^)]+)\)/g, (_, path) => `](${path === 'guide.md' ? 'guide/index.md' : path})`);

writeFileSync(new URL('./llms.txt', import.meta.url), content);
