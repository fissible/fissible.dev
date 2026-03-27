// apps/docs/astro.config.mjs
import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

export default defineConfig({
  site: 'https://docs.fissible.dev',
  output: 'static',
  integrations: [
    starlight({
      title: 'fissible docs',
      social: {
        github: 'https://github.com/fissible',
      },
      components: {
        SiteTitle: './src/overrides/SiteTitle.astro',
      },
      sidebar: [
        { label: 'shellframe', autogenerate: { directory: 'shellframe' } },
        { label: 'seed',       autogenerate: { directory: 'seed' } },
        { label: 'ptyunit',    autogenerate: { directory: 'ptyunit' } },
        { label: 'shellql',    autogenerate: { directory: 'shellql' } },
        { label: 'accord',     autogenerate: { directory: 'accord' } },
        { label: 'drift',      autogenerate: { directory: 'drift' } },
        { label: 'forge',      autogenerate: { directory: 'forge' } },
        { label: 'watch',      autogenerate: { directory: 'watch' } },
        { label: 'fault',      autogenerate: { directory: 'fault' } },
      ],
    }),
  ],
});
