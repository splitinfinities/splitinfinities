<?php
/**
 * Rename some post formats
 */
function rename_post_formats( $safe_text ) {
    if ( $safe_text == 'Link' )
        return 'Article';

    if ( $safe_text == 'Aside' )
        return 'News';

    return $safe_text;
}
// add_filter( 'esc_html', 'rename_post_formats' );

//rename Aside in posts list table
function live_rename_formats() {
    global $current_screen;

    if ( $current_screen->id == 'edit-post' ) { ?>
        <script type="text/javascript">
        jQuery('document').ready(function() {

            jQuery("span.post-state-format").each(function() {
                if ( jQuery(this).text() == "Link" )
                    jQuery(this).text("Article");

                if ( jQuery(this).text() == "Aside" )
                    jQuery(this).text("News");
            });

        });
        </script>
<?php }
}
// add_action('admin_head', 'live_rename_formats');
