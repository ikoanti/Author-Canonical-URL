<?php
/*
 * Plugin Name: Author Canonical URL
 * Description: Sets canonical URL for author pages and provides a field to add canonical URL on the user edit page.
 * Version: 1.0
 * Author: Irakli Antidze
 * Author URI: https://www.irakli.life
 * License: CC0 1.0 Universal
 */

function author_canonical_url_add_canonical_url_field($user)
{
    $canonical_url = get_user_meta($user->ID, 'canonical_url', true);
    ?>
    <h2><?php _e('Canonical URL', 'author-canonical-url'); ?></h2>
    <table class="form-table">
        <tr>
            <th><label for="canonical_url"><?php _e('Canonical URL', 'author-canonical-url'); ?></label></th>
            <td>
                <input type="url" name="canonical_url" id="canonical_url" value="<?php echo esc_attr($canonical_url); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('Enter the canonical URL for this author.', 'author-canonical-url'); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

function author_canonical_url_save_canonical_url($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    $canonical_url = isset($_POST['canonical_url']) ? esc_url_raw($_POST['canonical_url']) : '';

    // Validate the URL
    if ($canonical_url && !filter_var($canonical_url, FILTER_VALIDATE_URL)) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' . __('Invalid canonical URL. Please enter a valid URL.', 'author-canonical-url') . '</p></div>';
        });
        return;
    }

    update_user_meta($user_id, 'canonical_url', $canonical_url);
}

add_action('show_user_profile', 'author_canonical_url_add_canonical_url_field');
add_action('edit_user_profile', 'author_canonical_url_add_canonical_url_field');
add_action('personal_options_update', 'author_canonical_url_save_canonical_url');
add_action('edit_user_profile_update', 'author_canonical_url_save_canonical_url');

function author_canonical_url_redirect_author_page()
{
    if (is_author()) {
        $author_id = get_queried_object_id();
        $canonical_url = get_user_meta($author_id, 'canonical_url', true);

        if (!empty($canonical_url)) {
            wp_redirect($canonical_url, 301);
            exit;
        }
    }
}

add_action('template_redirect', 'author_canonical_url_redirect_author_page');
