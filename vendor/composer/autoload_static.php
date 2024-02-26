<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8a6d5656a0398b6ef880850b0e59cd66
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Predis\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8a6d5656a0398b6ef880850b0e59cd66::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8a6d5656a0398b6ef880850b0e59cd66::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8a6d5656a0398b6ef880850b0e59cd66::$classMap;

        }, null, ClassLoader::class);
    }
}
