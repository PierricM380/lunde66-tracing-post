<?php

namespace Lunedev66\Tracingpost;

use Lunedev66\Tracingpost\Controller\AdminController;

class TracingPostPlugin
{
	const TRANSIENT_TRACING_POST_ENABLED = 'lunedev66_tracing_post_enabled';

	/**
	 * @param string $file
	 */
	public function __construct(string $file)
	{
		// Set the activation hook
		register_activation_hook($file, [$this, 'plugin_activation']);
		// Adds a callback function to an action hook
		add_action('admin_notices', [$this, 'notice_activation']);

		// Shake if user is an admin
		if (is_admin()) {
			$adminController = new AdminController();
		}
	}

	/**
	 * @return void
	 * Callback function
	 */
	public function plugin_activation(): void
	{
		// Allow to temporarily cache information
		set_transient(self::TRANSIENT_TRACING_POST_ENABLED, true);
	}

	/**
	 * @return void
	 * Callback function to display message activation status
	 */
	public function notice_activation(): void
	{
		// Retrieves the value of a transient
		if (get_transient(self::TRANSIENT_TRACING_POST_ENABLED)) {
			self::render('notices', [
				'message' => "Merci d'avoir activé <strong>LuneDev66 Tracing Post</strong> !"
			]);

			// Delete a transient
			delete_transient(self::TRANSIENT_TRACING_POST_ENABLED);
		}
	}

	/**
	 * @param string $name
	 * @param array $args
	 *
	 * @return void
	 * Rendering template stored in views folder
	 */
	public static function render(string $name, array $args = []): void
	{
		extract($args);

		$file = TRACING_POST_PLUGIN_DIR . "views/$name.php";

		ob_start();

		include_once($file);

		echo ob_get_clean();
	}
}
