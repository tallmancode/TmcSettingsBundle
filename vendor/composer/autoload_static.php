<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8bee148a9061c5fad6914f150c02a40c
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tallmancode\\SettingsBundle\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tallmancode\\SettingsBundle\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit8bee148a9061c5fad6914f150c02a40c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8bee148a9061c5fad6914f150c02a40c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8bee148a9061c5fad6914f150c02a40c::$classMap;

        }, null, ClassLoader::class);
    }
}
