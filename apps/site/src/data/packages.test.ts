// apps/site/src/data/packages.test.ts
import { describe, it, expect } from 'vitest';
import {
  tuiPackages,
  phpPackages,
  paidProducts,
  allOssPackages,
} from './packages';

describe('tuiPackages', () => {
  it('has exactly 4 packages', () => {
    expect(tuiPackages).toHaveLength(4);
  });

  it('each package has all required fields', () => {
    for (const pkg of tuiPackages) {
      expect(pkg.slug, `${pkg.slug}.slug`).toBeTruthy();
      expect(pkg.name, `${pkg.slug}.name`).toBeTruthy();
      expect(pkg.tagline, `${pkg.slug}.tagline`).toBeTruthy();
      expect(pkg.install, `${pkg.slug}.install`).toMatch(/^brew install /);
      expect(pkg.installLabel, `${pkg.slug}.installLabel`).toBe('brew');
      expect(pkg.features, `${pkg.slug}.features`).toHaveLength(4);
      expect(pkg.docsUrl, `${pkg.slug}.docsUrl`).toMatch(/^https:\/\/docs\.fissible\.dev\//);
      expect(pkg.githubUrl, `${pkg.slug}.githubUrl`).toMatch(/^https:\/\/github\.com\/fissible\//);
      expect(pkg.codeExample, `${pkg.slug}.codeExample`).toBeTruthy();
      expect(pkg.suite, `${pkg.slug}.suite`).toBe('tui');
    }
  });
});

describe('phpPackages', () => {
  it('has exactly 3 packages', () => {
    expect(phpPackages).toHaveLength(3);
  });

  it('each package has a composer install command', () => {
    for (const pkg of phpPackages) {
      expect(pkg.install, `${pkg.slug}.install`).toMatch(/^composer require /);
      expect(pkg.installLabel, `${pkg.slug}.installLabel`).toBe('composer');
      expect(pkg.suite, `${pkg.slug}.suite`).toBe('php');
    }
  });
});

describe('paidProducts', () => {
  it('has exactly 4 products', () => {
    expect(paidProducts).toHaveLength(4);
  });

  it('sigil is purchase-pending; guit, station, conduit are coming-soon', () => {
    const sigil = paidProducts.find(p => p.slug === 'sigil');
    expect(sigil?.status).toBe('purchase-pending');
    for (const slug of ['guit', 'station', 'conduit']) {
      const p = paidProducts.find(p => p.slug === slug);
      expect(p?.status, `${slug}.status`).toBe('coming-soon');
    }
  });

  it('each product has all required fields', () => {
    for (const p of paidProducts) {
      expect(p.slug).toBeTruthy();
      expect(p.name).toBeTruthy();
      expect(p.tagline).toBeTruthy();
      expect(p.description).toBeTruthy();
    }
  });
});

describe('allOssPackages', () => {
  it('contains all 7 OSS packages', () => {
    expect(allOssPackages).toHaveLength(7);
  });

  it('has no duplicate slugs', () => {
    const slugs = allOssPackages.map(p => p.slug);
    expect(new Set(slugs).size).toBe(slugs.length);
  });
});
