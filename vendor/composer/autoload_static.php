<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit390e01648ac119b99c6e1f897a6f6e05
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit390e01648ac119b99c6e1f897a6f6e05::$classMap;

        }, null, ClassLoader::class);
    }
}
