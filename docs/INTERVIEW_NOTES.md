# CampusLoop Help Center: interview notes

**What problem does it solve?** It gives students a searchable place to understand common buying, selling, reservation and safety tasks.

**Why is WordPress separate?** CampusLoop owns application workflows. WordPress is useful for public, structured support content that can be edited without changing the marketplace app.

**What is `support_article`?** It is a Custom Post Type: a WordPress content type with its own name and URLs, separate from ordinary blog posts.

**What is `support_category`?** It is the taxonomy used to group support articles. A taxonomy is a classification system, and a Term ID is the database ID for one topic.

**Why does `show_in_rest` matter?** It exposes articles and topics to WordPress’s REST API, which lets the small frontend search interface request articles.

**How does PHP pass configuration to JavaScript?** PHP creates the REST URL with `rest_url()` and adds a small JSON configuration object before the enqueued script.

**How does search work?** JavaScript sends the keyword as the REST API `search` parameter. When a topic is selected, it sends that topic’s Term ID as `support_category`.

**How is article presentation reused?** The plugin selects one template for every `support_article`. A reusable content filter adds the topic, title, body, back link and same-topic related links, so no article needs its own layout file.

**What accessibility choices were made?** The form has labels, keyboard submission, visible focus, a live results region and a loading state. Results are created safely with DOM methods.

**What are the limitations?** English content and neutral wording are used for now. Multilingual URLs, automated browser checks and Lighthouse results remain future work.

## Debugging examples

Known examples are a PHP fatal error caused by saving PHP as RTF in TextEdit, a 404 fixed by refreshing permalinks and using `/help/`, browser caching during JavaScript changes, and a category-filtering syntax error caused by duplicated code and an extra closing brace.
