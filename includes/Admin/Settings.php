<?php

namespace MapRoute\Admin;

use MapRoute\Admin\Settings\AbstractSettings;
use MapRoute\Admin\Settings\ChartSettings;
use MapRoute\Admin\Settings\DescSettings;
use MapRoute\Admin\Settings\MapSettings;
use MapRoute\Registry;

class Settings {

    private static $instance = NULL;
    private $pool = [];

    public function __construct() {
        add_action('plugins_loaded', array($this, 'loadTitalLocale'));

        add_action('tf_create_options', array($this, 'registerOptions'));

        $this->titan = \TitanFramework::getInstance(Registry::instance()['token']);
    }

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function register(AbstractSettings $setting) {
        $this->pool[] = $setting;

        $setting->create();
    }

    public function registerOptions() {
        $panel = $this->titan->createContainer([
            'parent' => 'options-general.php',
            'name' => 'Маршруты',
            'icon' => 'dashicons-location',
            'id' => Registry::instance()['token'],
            'type' => 'admin-page'
        ]);


        $this->register(new MapSettings($panel));
        $this->register(new ChartSettings($panel));
        $this->register(new DescSettings($panel));
    }

    public function loadTitalLocale() {
        global $wp_version;

        if (class_exists('\TitanFrameworkPlugin')) {
            $reflector = new \ReflectionClass(\TitanFrameworkPlugin::class);

            $locale = version_compare($wp_version, '5', '>=') ? determine_locale() : (is_admin() ? get_user_locale() : get_locale());
            $mofile = \TF_I18NDOMAIN . '-' . apply_filters('plugin_locale', $locale, \TF_I18NDOMAIN) . '.mo';

            load_textdomain(\TF_I18NDOMAIN, dirname($reflector->getFileName()) . '/languages/' . $mofile);
        }
    }

    public function getOption($name) {
        $value = $this->titan->getOption($name) ?: NULL;

        foreach ($this->pool as $instance) {
            if (($extraValue = $instance->getExtraOption($name, $value)) !== NULL) {
                return $extraValue;
            }
        }

        return $value;
    }

    public function getAllOptions() {
        $fields = array_reduce($this->pool, function ($accumulator, $instance) {
            return array_merge($accumulator, $instance::USED_OPTIONS);
        }, []);

        $options = [];
        foreach ($fields as $option) {
            $options[$option] = $this->getOption($option);
        }

        return $options;
    }
}