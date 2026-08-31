# CampusLoop Help Center

CampusLoop Help Center is a small WordPress plugin for the public support content of the CampusLoop student marketplace. It covers listings, buying, reservations, messaging, reporting and safer exchanges without rebuilding the main application or adding AI/chat features.

## What I built

CampusLoop is the separate marketplace application where listings, messaging, reservations and reporting/admin workflows happen. This plugin is its public WordPress support-content layer: students can visit `/support/`, search by keyword and/or choose a topic, view matching results, and open an article at `/help/<slug>/`.

## Architecture

The plugin registers the `support_article` Custom Post Type and the hierarchical `support_category` taxonomy. The current Help Center contains 12 CampusLoop articles across Getting Started, Selling & Listings, Buying & Reservations, and Account & Safety. Articles use the `/help/` rewrite base and are available through the WordPress REST API. The `/support/` page uses the `[support_search]` shortcode; its vanilla JavaScript searches the REST endpoint with keyword and taxonomy Term ID filters, loading, no-results, API-error and duplicate-submission states.

The plugin owns the Help Center page and single-article template. A `template_include` filter selects `templates/help-center.php` for `/support/` and all `support_article` posts. The reusable `the_content` filter adds the article topic, title, body, back link and related articles.

## Setup

Activate the plugin, then open **Tools → Help Center Setup** and choose **Set up CampusLoop Help Center**. The setup action is administrator-only and nonce-protected. It matches seeded articles by title, so matching titles can be updated; renamed seeded articles need manual review before running setup again. Save **Settings → Permalinks** afterward to refresh rewrite rules.

## Accessibility

The search interface uses visible labels, keyboard submission, focus states, an `aria-live` result area and an `aria-busy` loading state. Search results use safe DOM text methods rather than injecting API text as HTML.

## Limitations and future work

Content is English only. A future bilingual version could use separate crawlable URLs such as `/en/help/` and `/zh/help/`; multilingual routing is not implemented. Lighthouse results have not been measured in this environment.

Known debugging examples include a PHP file accidentally saved as RTF, a `/help/` permalink issue resolved by refreshing rewrite rules, browser caching during JavaScript changes, and a duplicated JavaScript block that caused a category-filtering syntax error.

The **Tools → Help Center Setup** page initially did not appear because its registration was conditionally gated. It was corrected by registering the page with `admin_menu` and `add_management_page()`.

The custom plugin source is version-controlled with Git and maintained in a GitHub repository.
