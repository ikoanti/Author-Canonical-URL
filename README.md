Features & Functionality

   Adds a Canonical URL Field to User Profiles
        Uses show_user_profile and edit_user_profile actions to add a field for setting a canonical URL.
        The URL is stored as user metadata (canonical_url in wp_usermeta).

   Saves Canonical URL on Profile Update
        Hooks into personal_options_update and edit_user_profile_update to save the URL.
        Uses esc_url_raw() to sanitize input and validate it using FILTER_VALIDATE_URL.
        Shows an error message in the admin panel if an invalid URL is entered.

   Redirects Author Page to Canonical URL
        Uses template_redirect to check if the current page is an author archive page.
        Retrieves the stored canonical URL and issues a 301 permanent redirect if set.
