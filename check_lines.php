<?php
$dirs = ['src', 'includes', 'views', 'public/assets/css', 'public/assets/js'];
foreach ($dirs as $dir) {
    echo "=== $dir ===\n";
    if (!is_dir($dir)) {
        echo "  (directorio no existe)\n\n";
        continue;
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isFile()) {
            $ext = $file->getExtension();
            if (in_array($ext, ['php', 'css', 'js'])) {
                $lines = count(file($file->getPathname()));
                if ($lines > 500) {
                    echo $file->getFilename() . ': ' . $lines . " líneas\n";
                }
            }
        }
    }
    echo "\n";
}
