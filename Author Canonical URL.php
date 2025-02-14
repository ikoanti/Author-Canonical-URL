<?php
/*
 * Plugin Name: Author Canonical URL
 * Description: Sets canonical URL for author pages and provides a field to add canonical URL on the user edit page.
 * Version: 1.1
 * Author: Irakli Antidze
 * Author URI: https://www.irakli.life
 * License: CC0 1.0 Universal
 */

// Add canonical URL field to user profiles
function author_canonical_url_add_field($user)
{
    $canonical_url = get_user_meta($user->ID, 'canonical_url', true);
    ?>
    <h2><?php esc_html_e('Canonical URL', 'author-canonical-url'); ?></h2>
    <table class="form-table">
        <tr>
            <th><label for="canonical_url"><?php esc_html_e('Canonical URL', 'author-canonical-url'); ?></label></th>
            <td>
                <input type="url" name="canonical_url" id="canonical_url" value="<?php echo esc_attr($canonical_url); ?>" class="regular-text" /><br />
                <span class="description"><?php esc_html_e('Enter the canonical URL for this author.', 'author-canonical-url'); ?></span>
                <?php wp_nonce_field('save_canonical_url', 'canonical_url_nonce'); ?>
            </td>
        </tr>
    </table>
    <?php
}

// Save canonical URL when user updates profile
function author_canonical_url_save($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    if (!isset($_POST['canonical_url_nonce']) || !wp_verify_nonce($_POST['canonical_url_nonce'], 'save_canonical_url')) {
        return;
    }

    $canonical_url = isset($_POST['canonical_url']) ? esc_url_raw($_POST['canonical_url']) : '';

    // Validate URL format
    if (!empty($canonical_url) && !filter_var($canonical_url, FILTER_VALIDATE_URL)) {
        add_settings_error('canonical_url', 'invalid_url', __('Invalid canonical URL. Please enter a valid URL.', 'author-canonical-url'), 'error');
        return;
    }

    update_user_meta($user_id, 'canonical_url', $canonical_url);
}

add_action('show_user_profile', 'author_canonical_url_add_field');
add_action('edit_user_profile', 'author_canonical_url_add_field');
add_action('personal_options_update', 'author_canonical_url_save');
add_action('edit_user_profile_update', 'author_canonical_url_save');

// Redirect author archive page to canonical URL
function author_canonical_url_redirect()
{
    if (is_author()) {
        $author_id = get_queried_object_id();
        $canonical_url = get_user_meta($author_id, 'canonical_url', true);

        // Ensure the canonical URL is not the same as the current page
        if (!empty($canonical_url) && esc_url(home_url($_SERVER['REQUEST_URI'])) !== esc_url($canonical_url)) {
            wp_safe_redirect($canonical_url, 301);
            exit;
        }
    }
}

add_action('template_redirect', 'author_canonical_url_redirect');
?>
