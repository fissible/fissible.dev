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
        {
          label: 'station',
          items: [
            { slug: 'station' },
            {
              label: 'Getting Started',
              items: [
                { slug: 'station/installation' },
                { slug: 'station/configuration' },
              ],
            },
            {
              label: 'Content',
              items: [
                { slug: 'station/content-types' },
                { slug: 'station/content-blocks' },
                { slug: 'station/content-publishing' },
                { slug: 'station/content-scheduling' },
                { slug: 'station/entry-versioning' },
              ],
            },
            {
              label: 'Forms & Automation',
              items: [
                { slug: 'station/forms' },
                { slug: 'station/automations' },
              ],
            },
            {
              label: 'Workflows',
              items: [
                { slug: 'station/workflows' },
              ],
            },
            {
              label: 'Navigation',
              items: [
                { slug: 'station/menus' },
              ],
            },
            {
              label: 'Users & Access',
              items: [
                { slug: 'station/users' },
                { slug: 'station/roles-permissions' },
                { slug: 'station/account-self-service' },
              ],
            },
            {
              label: 'Platform',
              items: [
                { slug: 'station/multi-tenancy' },
                { slug: 'station/modules' },
                { slug: 'station/frontend-theming' },
                { slug: 'station/maintenance-mode' },
              ],
            },
            {
              label: 'Operations',
              items: [
                { slug: 'station/backup-restore' },
                { slug: 'station/backups-setup' },
                { slug: 'station/seeder-suites' },
              ],
            },
            { slug: 'station/reference' },
          ],
        },
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
