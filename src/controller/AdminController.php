<?php

namespace Lunedev66\Tracingpost\Controller;

use lunedev66\tracingpost\TracingPostPlugin;

class AdminController
{
	const REDIRECT_TO_LIST = 0;
	const REDIRECT_TO_EDIT = 1;

	/**
	 * Init method
	 */
	public function __construct()
	{
		$this->init_hooks();
	}

	/**
	 * @return void
	 */
	public function init_hooks(): void
	{
        //Adds a callback function to an action hook
		add_action('admin_menu', [$this, 'admin_menu']);
		add_action('admin_init', [$this, 'admin_init']);
		add_action('post_row_actions', [$this, 'trancing_post_actions'], 10, 2);
		add_action('admin_action_duplicate', [$this, 'duplicate_post']);
	}

	/**
	 * @return void
     * Action hook admini_menu
	 */
	public function admin_menu(): void
	{
        // Adds a submenu page to the Settings main menu
		add_options_page('TracingPost', 'Tracing Post', 'manage_options', 'duplicate_post', [$this, 'config_page']);
	}

	/**
	 * @return void
     * Display config page with render method
	 */
	public function config_page(): void
	{
		TracingPostPlugin::render('config');
	}

	public function admin_init(): void
	{
        // Registers a setting and its data
		register_setting('duplicate_post_general', 'duplicate_post_general');
        // Adds a new section to a settings page
		add_settings_section('duplicate_post_main', null, null, 'duplicate_post');
		// Adds a new field to a section of a settings page
		add_settings_field('redirect_to', 'Après avoir cliqué sur "Dupliquer", rediriger vers:', [$this, 'redirect_to_render'], 'duplicate_post', 'duplicate_post_main');
	}

	/**
	 * @return void
     * Callback function for add_settings_field method
     * Display a select html
	 */
	public function redirect_to_render(): void
	{
        // Retrieves an option value based on an option name
		$general_options = get_option('duplicate_post_general', [
			'redirect_to' => 0
		]);

		$selectedValue = $general_options['redirect_to'];

?>
		<select name="duplicate_post_general[redirect_to]">
			<option value="<?= self::REDIRECT_TO_LIST ?>" <?= selected(self::REDIRECT_TO_LIST, $selectedValue) ?>>Vers la liste des articles</option>
			<option value="<?= self::REDIRECT_TO_EDIT ?>" <?= selected(self::REDIRECT_TO_EDIT, $selectedValue) ?>>Vers l'écran de modification de l'article dupliqué</option>
		</select>
<?php
	}

	/**
	 * @param array $actions
	 * @param \WP_Post $post
	 *
	 * @return array
     *
     * Callback method for action hook post_row_actions
	 */
	public function trancing_post_actions(array $actions, \WP_Post $post): array
	{
        // Returns whether the current user has the specified capability
		if (current_user_can('edit_posts')) {
			$post_id = $post->ID;
			$actions['duplicate_post'] = "<a href='admin.php?post=$post_id&action=duplicate'>Dupliquer</a>";
		}

		return $actions;
	}

	/**
	 * @return void
	 */
	public function duplicate_post(): void
	{
        // Retrieves an option value based on an option name
		$general_options = get_option('duplicate_post_general', [
			'redirect_to' => 0
		]);

        // Get the integer value of a variable
		$redirect_to = intval($general_options['redirect_to']);

		$post_id = (isset($_GET['post'])) ? intval($_GET['post']) : 0;

		$this->verify_request($post_id);

        // Retrieves post data given a post ID or post object
		$post = get_post($post_id);

        // Kills WordPress execution and displays HTML page with an error message
		if (!$post) {
			wp_die("Une erreur est survenue. L'article $post_id est introuvable !", "Article introuvable !");
		}

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

        // Basics components of an html Wordpress page
		$post_data = [
			'post_author' => $user_id,
			'post_content' => $post->post_content,
			'post_title' => $post->post_title,
			'post_excerpt' => $post->post_excerpt,
			'post_status' => $post->post_status,
			'comment_status' => $post->comment_status,
			'ping_status' => $post->ping_status,
			'post_password' => $post->post_password,
			'to_ping' => $post->to_ping,
			'post_parent' => $post->post_parent,
			'menu_order' => $post->menu_order
		];

        // Inserts or update a post
		$new_post_id = wp_insert_post($post_data);

		if ($redirect_to === self::REDIRECT_TO_LIST) {
            // redirect to list of post
			wp_safe_redirect(admin_url('edit.php'));
		} elseif ($redirect_to === self::REDIRECT_TO_EDIT) {
            // redirect to edit page of a tracing post
			wp_safe_redirect(admin_url("post.php?post=$new_post_id&action=edit"));
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return void
	 */
	public function verify_request($post_id)
	{
		$referer = wp_get_referer();
		$location = $referer ?: get_site_url();

		if (!current_user_can('edit_posts', $post_id)) {
			wp_safe_redirect($location);
		}
	}
}
