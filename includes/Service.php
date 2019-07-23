<?php

namespace MapRoute;

use MapRoute\Admin\Processor;
use MapRoute\Tools\Fenom;
use MapRoute\Utils\Options;

class Service {
    private static $instance = NULL;
    private $settings;

    public function __construct($file = '', $version = '1.0.0') {
        date_default_timezone_set('Europe/Moscow');

        // Load plugin environment variables
        Registry::instance()->add([
            'version' => $version,
            'token' => 'map_route',
            'file' => $file,
            'dir' => dirname($file),
            'shortcode' => 'map-route',
            'assets_dir' => trailingslashit(dirname($file)) . 'assets',
            'config_dir' => trailingslashit(dirname($file)) . 'config',
            'assets_url' => esc_url(trailingslashit(plugins_url('/assets/', $file))),
            'base_url' => esc_url(trailingslashit(plugins_url('/', $file)))
        ]);

        Registry::instance()->add('fenom', Fenom::instance([
            'force_compile' => MAP_ROUTE_DEBUG,
            'force_verify' => TRUE
        ]));

        add_action('init', [$this, 'init']);

        register_activation_hook(Registry::instance()['file'], [$this, 'install']);
    }

    public function init() {
        $this->output = new Output();
        $this->processor = Processor::instance();

        add_action('admin_enqueue_scripts', [$this, 'addAdminAssets'], 10);
        add_action('wp_enqueue_scripts', [$this, 'addWebAssets'], 10);

        add_shortcode(Registry::instance()['shortcode'], [$this->output, 'render']);
        add_action('save_post', [$this->processor, 'save'], 10, 2);
    }

    public function addAdminAssets() {
        $minPrefix = !MAP_ROUTE_DEBUG ? '.min' : '';

        wp_enqueue_style(Registry::instance()['token'] . '_admin', esc_url(Registry::instance()['assets_url']) . sprintf('build/admin%s.css', $minPrefix), [], Registry::instance()['version']);
    }

    public function addWebAssets() {
        $minPrefix = !MAP_ROUTE_DEBUG ? '.min' : '';

        wp_enqueue_script(Registry::instance()['token'] . '_ymaps', sprintf('//api-maps.yandex.ru/2.1/?apikey=%s&lang=ru_RU', $this->settings->getOption('map-api-key')));

        wp_enqueue_style(Registry::instance()['token'] . '_web', esc_url(Registry::instance()['assets_url']) . sprintf('build/web%s.css', $minPrefix), [], Registry::instance()['version']);
        wp_enqueue_script(Registry::instance()['token'] . '_web', esc_url(Registry::instance()['assets_url']) . sprintf('build/web%s.js', $minPrefix), ['jquery'], Registry::instance()['version']);
    }

    public static function instance($file, $version) {
        if (is_null(self::$instance)) {
            self::$instance = new self($file, $version);
        }

        return self::$instance;
    }

    public function setSettingsInstance(Admin\Settings $settings) {
        $this->settings = $settings;
    }

    public function install() {
        Options::update('version', Registry::instance()['version']);
    }

    public function uninstall() {
        foreach (['version'] as $option) {
            Options::delete($option);
        }
    }
}