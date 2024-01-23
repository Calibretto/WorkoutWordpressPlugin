<?php
/**
 * Class to display notices.
 */
class BHWorkoutPlugin_Notice {
	private string $message;
    private string $notice_class;
    private string $notice_prefix;

    public static function warning(string $message) : BHWorkoutPlugin_Notice {
        return new BHWorkoutPlugin_Notice($message, "notice-warning", "Warning");
    }

    public static function error(string $message) : BHWorkoutPlugin_Notice {
        return new BHWorkoutPlugin_Notice($message, "notice-error", "Error");
    }

    public static function success(string $message) : BHWorkoutPlugin_Notice {
        return new BHWorkoutPlugin_Notice($message, "notice-success", "Success");
    }

	private function __construct(string $message, string $notice_class, string $notice_prefix) {
		$this->message = $message;
        $this->notice_class = $notice_class;
        $this->notice_prefix = $notice_prefix;

		add_action('admin_notices', array($this, 'render'));
	}

	public function render() {
		printf('<div class="notice %s is-dismissible"><p>%s: %s</p></div>', 
            $this->notice_class,
            $this->notice_prefix,
            esc_html($this->message)
        );
	}
}
?>