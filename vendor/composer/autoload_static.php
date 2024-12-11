<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb0bd2dc2c720f3989d547184a90b1e5f
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Atlantic\\AtlanticGateway\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Atlantic\\AtlanticGateway\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb0bd2dc2c720f3989d547184a90b1e5f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb0bd2dc2c720f3989d547184a90b1e5f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb0bd2dc2c720f3989d547184a90b1e5f::$classMap;

        }, null, ClassLoader::class);
    }
}
