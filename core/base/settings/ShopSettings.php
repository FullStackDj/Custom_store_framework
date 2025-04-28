<?php

namespace core\base\settings;

use core\base\controller\Singleton;
use core\base\settings\Settings;

class ShopSettings {

    use Singleton {
        instance as traitInstance;
    }

    private $baseSettings;

    private $routes = [
        'plugins' => [
            'dir' => false,
            'routes' => [
            ]
        ],
    ];

    private $templateArr = [
        'text' => ['price', 'short', 'name'],
        'textarea' => ['goods_content']
    ];

    static public function get($property) {
        return self::instance()->$property;
    }

    static public function instance() {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }

        self::traitInstance()->baseSettings = Settings::instance();
        $baseProperties = self::instance()->baseSettings->clueProperties(get_class());
        self::$_instance->setProperty($baseProperties);

        return self::$_instance;
    }

    protected function setProperty($properties) {
        if ($properties) {
            foreach ($properties as $name => $property) {
                $this->$name = $property;
            }
        }
    }
}