<?php

/**
 * Created by PhpStorm.
 * User: benoti
 * Date: 20/02/2017
 * Time: 18:21
 */

defined( 'ABSPATH' ) || exit;

class brozzme_switch_duplicate_duplicator
{

    /**
     * brozzme_switch_duplicate_duplicator constructor.
     */
    public function __construct(){

        $this->general_options = get_option('bsd_settings');
        $this->duplicate_options = get_option('bsd_duplicate_settings');

        if($this->general_options['bsd_enable_duplicate'] != 'true'){
            return;
        }

        $this->_init();
    }

    /**
     *
     */
    public function _init(){
        add_action( 'admin_action_duplicate_post_as_draft', array($this, 'duplicate_post_as_draft') );

        add_filter( 'post_row_actions', array($this, 'duplicate_post_link'), 10, 2 );
        add_filter( 'page_row_actions', array($this, 'duplicate_post_link'), 10, 2 );

        add_action('admin_head', array($this, 'confirm_duplicate'));
    }

    /**
     *
     */
    public function duplicate_post_as_draft(){
        global $wpdb;
        if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
            wp_die(__('No post to duplicate has been supplied!', B7E_SD_TEXT_DOMAIN));
        }

        /*
         * get the original post id
         */
        $post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
        /*
         * and all the original post data then
         */
        $post = get_post( $post_id );

        /*
         * if you don't want current user to be the new post author,
         * then change next couple of lines to this: $new_post_author = $post->post_author;
         */
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;

        /*
         * if post data exists, create the post duplicate
         */
        if (isset( $post ) && $post != null) {

            /*
             * new post data array
             */
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_author'    => $new_post_author,
                'post_content'   => $post->post_content,
                'post_excerpt'   => $post->post_excerpt,
                'post_name'      => '',
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'post_status'    => 'draft',
                'post_title'     => $post->post_title .' '. __('Duplicated from #', B7E_SD_TEXT_DOMAIN) . ' ' .$post_id,
                'post_type'      => $post->post_type,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order
            );

            /*
             * insert the post by wp_insert_post() function
             */
            $new_post_id = wp_insert_post( $args );

            /*
             * get all current post terms ad set them to the new post draft
             */
            if($this->duplicate_options['bsd_copy_taxonomy'] == 'true'){
                $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
                foreach ($taxonomies as $taxonomy) {
                    $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                    wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                }
            }
            else{
                // let's remove uncategorized category
                $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
                foreach ($taxonomies as $taxonomy) {
                    $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                    wp_set_object_terms($new_post_id, null, $taxonomy, false);
                }
            }


            /*
             * duplicate all post meta just in two SQL queries
             */
            if($this->duplicate_options['bsd_copy_custom_fields'] == 'true'){
                $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                if (count($post_meta_infos)!=0) {
                    $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                    foreach ($post_meta_infos as $meta_info) {
                        $meta_key = $meta_info->meta_key;
                        $meta_value = addslashes($meta_info->meta_value);
                        $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                    }
                    $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                    $wpdb->query($sql_query);
                }
            }

            /*
             * finally, redirect to the edit post screen for the new draft
             */
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
            exit;
        } else {
            wp_die(__('Post creation failed, could not find original post: ' . $post_id, B7E_SD_TEXT_DOMAIN));
        }
    }

    /**
     * @param $actions
     * @param $post
     * @return mixed
     */
    public function duplicate_post_link($actions, $post ) {
        if (current_user_can('edit_posts')) {
            $actions['duplicate'] = '<a href="admin.php?action=duplicate_post_as_draft&amp;post=' . $post->ID . '" class="duplicator" title="'.__('Duplicate this item', B7E_SD_TEXT_DOMAIN).'" rel="permalink">'.__('Duplicate', B7E_SD_TEXT_DOMAIN).'</a>';
        }
        return $actions;
    }

    /**
     *
     */
    public function confirm_duplicate(){
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery(".duplicator").click(function(){
                    if(confirm("<?php _e('Are you sure you want to duplicate this?', B7E_SD_TEXT_DOMAIN);?>")){
                        setTimeout(function(){
                            alert("<?php _e('Duplicate in progress, you\'ll be redirect!', B7E_SD_TEXT_DOMAIN);?>");
                        }, 0);
                    }
                    else{
                        return false;
                    }
                });

            });
        </script>
        <?php
    }


}

