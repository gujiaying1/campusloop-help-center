# CampusLoop Help Center

**WordPress Support Platform for the CampusLoop Marketplace**

A custom WordPress plugin that provides a searchable public Help Center for the CampusLoop student marketplace. It is a support-content layer—not a replacement for the marketplace application—and implements custom content types, REST-powered search/filtering, templates, setup tooling, and accessibility-focused states.

**PHP · WordPress · REST API · JavaScript · CSS**

## What I Built

- A `support_article` Custom Post Type for Help Center content.
- A hierarchical `support_category` taxonomy for topic-based organisation.
- A `/support/` Help Center page powered by the `[support_search]` shortcode.
- Keyword search and topic filtering with the WordPress REST API.
- Custom templates for the Help Center and individual `/help/<slug>/` articles.
- An administrator-only setup screen that creates or updates four topics, twelve articles, and the Help Center page.
- Loading, no-results, API-error, duplicate-submission, focus, and screen-reader status states.

## How It Works

```text
User
  ↓
/support/ Help Center
  ↓
JavaScript search and topic filter
  ↓
WordPress REST API: /wp/v2/support_article
  ↓
support_article + support_category
  ↓
Custom Help Center / article template
```

## Technical Highlights

- **Structured WordPress content:** `register_post_type()` and `register_taxonomy()` expose support articles and categories to the REST API using `show_in_rest`.
- **REST API search:** the browser sends keyword searches through the `search` parameter and selected category Term IDs through `support_category`; requests limit fields and results for the Help Center UI.
- **Safe asynchronous UI:** JavaScript uses `fetch`, disables duplicate submissions while loading, and renders API-provided titles with `textContent` rather than HTML injection.
- **Template control:** a `template_include` filter routes the Help Center page and all support articles through the plugin template. A `the_content` filter adds topic metadata, a back link, and related articles.
- **Protected setup tooling:** the Tools → Help Center Setup action requires the `manage_options` capability and a WordPress nonce before seeding or updating content.
- **Accessibility-focused interaction:** visible labels, keyboard form submission, focus-visible styles, an `aria-live` results region, and `aria-busy` while a request is in progress.

## Screenshots

Real screenshots are not included yet. The original LocalWP site folder is present, but its local web server was not running during this documentation update, so no screenshots were fabricated.

Capture these desktop views after starting the existing **support-portal** site in LocalWP:

1. **Help Center:** `/support/`
2. **Search & Topic Filtering:** `/support/` after searching for `reservation` or selecting **Buying & Reservations**
3. **Help Article:** `/help/how-reservations-work/`

Save them as `docs/screenshots/help-center-home.png`, `docs/screenshots/help-center-search.png`, and `docs/screenshots/help-article.png`, then add them to this section.

## Quality & Testing

Functional QA covered keyword search, topic filtering, and article navigation. The repository’s prior local test record reports Lighthouse 13.4.1 results in Chrome’s emulated Desktop mode for both `/support/` and a representative article:

| Measure | Result |
| --- | ---: |
| Performance | 100 |
| Accessibility | 100 |
| Best Practices | 78 |
| SEO | 100 |

These were measured in the local LocalWP environment, not a production deployment. The Best Practices score was affected by local HTTP and deployment-level HTTPS/security-header checks.

## Debugging / Engineering Lessons

- A `/help/` routing issue was resolved by refreshing WordPress permalinks after registering the custom post type rewrite base.
- A duplicated JavaScript block and extra closing brace caused a category-filtering syntax error; the fix reinforced the value of testing each filter path after edits.
- The setup screen initially failed to appear because its registration was conditionally gated. Registering it through `admin_menu` with `add_management_page()` made the route reliable.

## Running Locally

1. Copy the plugin into `wp-content/plugins/` in a local WordPress installation.
2. Activate **CampusLoop Help Center** in WordPress Admin → Plugins.
3. Open **Tools → Help Center Setup** and select **Set up CampusLoop Help Center**.
4. Save **Settings → Permalinks** to refresh the `/help/` rewrite rules.
5. Open `/support/`.

The setup action matches seed articles by title. Matching titles may be updated; renamed seed articles should be reviewed before setup is run again.

## Limitations / Future Work

- Support content is currently English only.
- Multilingual, crawlable routes are not implemented.
- Production hosting and deployment configuration are outside this repository.

## Repository Contents

- `support-portal-tools.php` — plugin registration, content setup, templates, asset loading, and metadata.
- `support-search.js` — REST search/filter interaction.
- `support-search.css` — Help Center and article presentation.
- `templates/help-center.php` — shared front-end template.
- `docs/INTERVIEW_NOTES.md` — implementation-based interview preparation notes.
