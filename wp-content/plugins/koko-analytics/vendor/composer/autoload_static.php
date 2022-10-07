<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0f2aae94a53417f43ce1b2165e7b5130
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'KokoAnalytics\\Admin' => __DIR__ . '/../..' . '/src/class-admin.php',
        'KokoAnalytics\\Aggregator' => __DIR__ . '/../..' . '/src/class-aggregator.php',
        'KokoAnalytics\\Command' => __DIR__ . '/../..' . '/src/class-command.php',
        'KokoAnalytics\\Endpoint_Installer' => __DIR__ . '/../..' . '/src/class-endpoint-installer.php',
        'KokoAnalytics\\Migrations' => __DIR__ . '/../..' . '/src/class-migrations.php',
        'KokoAnalytics\\Plugin' => __DIR__ . '/../..' . '/src/class-plugin.php',
        'KokoAnalytics\\Pruner' => __DIR__ . '/../..' . '/src/class-pruner.php',
        'KokoAnalytics\\Rest' => __DIR__ . '/../..' . '/src/class-rest.php',
        'KokoAnalytics\\Script_Loader' => __DIR__ . '/../..' . '/src/class-script-loader.php',
        'KokoAnalytics\\Shortcode_Most_Viewed_Posts' => __DIR__ . '/../..' . '/src/class-shortcode-most-viewed-posts.php',
        'KokoAnalytics\\Widget_Most_Viewed_Posts' => __DIR__ . '/../..' . '/src/class-widget-most-viewed-posts.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit0f2aae94a53417f43ce1b2165e7b5130::$classMap;

        }, null, ClassLoader::class);
    }
}
