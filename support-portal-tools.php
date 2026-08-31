<?php
/**
 * Plugin Name: CampusLoop Help Center
 * Description: Searchable CampusLoop help content and one-time setup tools.
 * Version: 2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function sp_register_support_content() {
    register_post_type( 'support_article', array(
        'labels' => array( 'name' => 'Help Articles', 'singular_name' => 'Help Article' ),
        'public' => true, 'show_in_rest' => true, 'has_archive' => true,
        'rewrite' => array( 'slug' => 'help' ), 'supports' => array( 'title', 'editor', 'excerpt' ),
    ) );
    register_taxonomy( 'support_category', 'support_article', array(
        'labels' => array( 'name' => 'Help Topics', 'singular_name' => 'Help Topic' ),
        'public' => true, 'show_in_rest' => true, 'hierarchical' => true,
    ) );
}
add_action( 'init', 'sp_register_support_content' );

function sp_support_topics() { return array(
    'getting-started' => 'Getting Started', 'selling-listings' => 'Selling & Listings',
    'buying-reservations' => 'Buying & Reservations', 'account-safety' => 'Account & Safety',
); }

function sp_support_search_shortcode() {
    $categories = get_terms( array( 'taxonomy' => 'support_category', 'hide_empty' => false ) );
    ob_start(); ?>
    <section id="sp-support-search" aria-labelledby="sp-help-title">
        <header class="sp-help-header"><a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><strong>CampusLoop</strong><span> / Help Center</span></a></header>
        <div class="sp-help-introduction"><h1 id="sp-help-title">CampusLoop Help Center</h1><p>Find answers about listings, buying, reservations and account safety.</p></div>
        <form id="sp-search-form" role="search">
            <div class="sp-search-field"><label for="sp-search-input">Search help articles</label><input type="search" id="sp-search-input" placeholder="What do you need help with?" autocomplete="off"></div>
            <div class="sp-search-field"><label for="sp-category-select">Topic</label><select id="sp-category-select"><option value="">All topics</option><?php if ( ! is_wp_error( $categories ) ) : foreach ( $categories as $category ) : ?><option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option><?php endforeach; endif; ?></select></div>
            <button type="submit" id="sp-search-button">Search</button>
        </form>
        <div id="sp-search-results" aria-live="polite" aria-busy="false"></div>
    </section>
    <?php return ob_get_clean();
}
add_shortcode( 'support_search', 'sp_support_search_shortcode' );

function sp_help_center_template( $template ) {
    if ( is_page( 'support' ) || is_singular( 'support_article' ) ) return plugin_dir_path( __FILE__ ) . 'templates/help-center.php';
    return $template;
}
add_filter( 'template_include', 'sp_help_center_template' );

function sp_enqueue_support_search_assets() {
    if ( ! is_page( 'support' ) && ! is_singular( 'support_article' ) ) return;
    wp_enqueue_style( 'sp-support-search', plugin_dir_url( __FILE__ ) . 'support-search.css', array(), filemtime( plugin_dir_path( __FILE__ ) . 'support-search.css' ) );
    if ( ! is_page( 'support' ) ) return;
    wp_enqueue_script( 'sp-support-search', plugin_dir_url( __FILE__ ) . 'support-search.js', array(), filemtime( plugin_dir_path( __FILE__ ) . 'support-search.js' ), true );
    wp_add_inline_script( 'sp-support-search', 'window.spSupportSearch = ' . wp_json_encode( array( 'articlesEndpoint' => rest_url( 'wp/v2/support_article' ) ) ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'sp_enqueue_support_search_assets' );

function sp_support_article_content( $content ) {
    if ( ! is_singular( 'support_article' ) || ! in_the_loop() || ! is_main_query() ) return $content;
    $terms = get_the_terms( get_the_ID(), 'support_category' ); $topic = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : 'CampusLoop Help';
    $related = get_posts( array( 'post_type' => 'support_article', 'posts_per_page' => 3, 'post__not_in' => array( get_the_ID() ), 'tax_query' => $terms ? array( array( 'taxonomy' => 'support_category', 'field' => 'term_id', 'terms' => $terms[0]->term_id ) ) : array() ) );
    $html = '<article class="sp-article"><a class="sp-back-link" href="' . esc_url( home_url( '/support/' ) ) . '">← Back to Help Center</a><p class="sp-article-topic">' . esc_html( $topic ) . '</p><h1>' . esc_html( get_the_title() ) . '</h1><div class="sp-article-content">' . $content . '</div>';
    if ( $related ) { $html .= '<aside class="sp-related"><h2>Related help</h2><ul>'; foreach ( $related as $post ) $html .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></li>'; $html .= '</ul></aside>'; }
    return $html . '</article>';
}
add_filter( 'the_content', 'sp_support_article_content' );

function sp_help_center_setup_page() {
    add_management_page(
        'CampusLoop Help Center Setup',
        'Help Center Setup',
        'manage_options',
        'sp-help-setup',
        'sp_help_center_setup_screen'
    );
}
add_action( 'admin_menu', 'sp_help_center_setup_page', 99 );

function sp_help_center_articles() { return array(
    'getting-started' => array(
        array( 'How CampusLoop works', '<p><strong>Quick answer:</strong> CampusLoop helps students find, discuss and reserve items listed by other students.</p><p>Search listings, use filters, message the seller when you have questions, and use reservations when you are ready to arrange the exchange.</p>' ),
        array( 'How to search and filter listings', '<p>Start with a clear keyword, then use the available filters to narrow results. Open a listing and check its details before messaging the seller.</p><p>If results are too narrow, remove one filter or try a broader keyword.</p>' ),
        array( 'How to contact a seller', '<p>Open the listing and use CampusLoop messaging to ask a specific question. Mention the item, your preferred timing and anything unclear in the listing.</p><p>Keep important arrangements in the app so there is a useful record of the conversation.</p>' ),
    ),
    'selling-listings' => array(
        array( 'How to create a useful listing', '<p>Add a specific title, clear photos, an honest condition description, a fair price and the exchange details buyers need.</p><p>Answer likely questions before publishing so buyers can decide with confidence.</p>' ),
        array( 'What information should I include in my listing?', '<p>Include what the item is, its condition, size or compatibility where relevant, what is included, the price and important limitations. Mention visible wear and anything the buyer needs to bring.</p>' ),
        array( 'How to edit or remove a listing', '<p>Open your listing in CampusLoop and use its available edit or remove action. Review changes before saving.</p><p>If the action is unavailable, check that you are signed in as the listing owner or contact the appropriate administrator.</p>' ),
    ),
    'buying-reservations' => array(
        array( 'How reservations work', '<p>A reservation signals that a buyer and seller are arranging an exchange. Use messaging to confirm practical details and do not assume an item is yours until the reservation is clearly agreed in the app.</p>' ),
        array( 'What to do if you need to cancel a reservation', '<p>If you can no longer continue with a reservation, contact the other person through CampusLoop messages as soon as possible.</p>' ),
        array( 'What to do if a buyer or seller stops replying', '<p>Send one clear follow-up with the key detail you need and give the other person reasonable time to respond.</p><p>If the exchange cannot proceed, do not share extra personal information or travel unnecessarily. Use reporting if the behavior is concerning.</p>' ),
    ),
    'account-safety' => array(
        array( 'How to report a listing or user', '<p>Use the reporting option on the relevant listing or user profile, and describe what happened accurately. Include useful context without sharing unrelated private information.</p>' ),
        array( 'What happens after I submit a report?', '<p>Your report records the information you submitted so the issue can be reviewed by an administrator.</p><p>Do not assume a particular outcome or response time.</p>' ),
        array( 'Tips for arranging a safer in-person exchange', '<p>Choose a public, well-lit place and tell someone you trust where you are going. Keep communication in CampusLoop, confirm the item and price beforehand, and avoid sharing unnecessary personal details.</p><p>If something feels unsafe, leave and use the reporting process.</p>' ),
    ),
); }

function sp_run_help_center_setup() {
    if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'sp_setup_help_center' ) ) wp_die( 'You are not allowed to do that.' );
    $terms = array(); foreach ( sp_support_topics() as $slug => $name ) { $term = term_exists( $name, 'support_category' ); $term = $term ? $term : wp_insert_term( $name, 'support_category', array( 'slug' => $slug ) ); if ( ! is_wp_error( $term ) ) $terms[ $slug ] = (int) ( is_array( $term ) ? $term['term_id'] : $term ); }
    foreach ( array( 'Software', 'Connectivity', 'Troubleshooting' ) as $old_name ) { $old_term = get_term_by( 'name', $old_name, 'support_category' ); if ( $old_term ) wp_delete_term( $old_term->term_id, 'support_category' ); }
    foreach ( sp_help_center_articles() as $slug => $articles ) foreach ( $articles as $article ) { $existing = get_page_by_title( $article[0], OBJECT, 'support_article' ); $post = array( 'post_title' => $article[0], 'post_content' => $article[1], 'post_status' => 'publish', 'post_type' => 'support_article' ); if ( $existing ) $post['ID'] = $existing->ID; $id = wp_insert_post( $post, true ); if ( ! is_wp_error( $id ) && isset( $terms[ $slug ] ) ) wp_set_post_terms( $id, array( $terms[ $slug ] ), 'support_category' ); }
    $page = get_page_by_path( 'support' ); $page_data = array( 'post_title' => 'CampusLoop Help Center', 'post_name' => 'support', 'post_content' => '[support_search]', 'post_status' => 'publish', 'post_type' => 'page' ); if ( $page ) $page_data['ID'] = $page->ID; wp_insert_post( $page_data );
    wp_safe_redirect( add_query_arg( 'sp_setup', 'success', admin_url( 'tools.php?page=sp-help-setup' ) ) ); exit;
}
add_action( 'admin_post_sp_run_help_center_setup', 'sp_run_help_center_setup' );

function sp_help_center_setup_screen() { ?><div class="wrap"><h1>CampusLoop Help Center Setup</h1><p>Creates or updates the four topics, twelve help articles and the <code>/support/</code> page. Safe to run again.</p><?php if ( isset( $_GET['sp_setup'] ) ) : ?><div class="notice notice-success"><p>CampusLoop Help Center setup completed.</p></div><?php endif; ?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="sp_run_help_center_setup"><?php wp_nonce_field( 'sp_setup_help_center' ); ?><p><button class="button button-primary" type="submit">Set up CampusLoop Help Center</button></p></form></div><?php }
