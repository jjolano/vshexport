<?php
$xml_file = 'exports.xml';
$out_dir = './exports';

if(!file_exists($out_dir))
{
    if(!mkdir($out_dir))
    {
        die("Cannot create output directory\n");
    }
}

$exports = simplexml_load_file($xml_file);

if(!$exports)
{
    die("Cannot load " . $xml_file . "\n");
}

foreach($exports->Group as $export)
{
    // exclude: Sysmodule
    if($export[0]['name'] == "Sysmodule")
    {
        continue;
    }

    // config.h
    $config_h = "#define LIBRARY_NAME\t\t\"" . $export[0]['name'] . "\"
#define LIBRARY_SYMBOL\t\t" . $export[0]['name'] . "

#define LIBRARY_HEADER_1\t0x2c000001
#define LIBRARY_HEADER_2\t0x0009
";

    // exports.h
    $exports_h = "#ifndef __EXPORTS_H__
#define __EXPORTS_H__

";

    foreach($export->Entry as $entry)
    {
        // exclusions: avoid compile error 'already defined'
        if($entry[0]['name'] == "__raw_spu_printf"
        || $entry[0]['name'] == "__getpid"
        || $entry[0]['name'] == "__rename"
        || $entry[0]['name'] == "__spu_thread_printf")
        {
            continue;
        }

        $exports_h .= "EXPORT(" . $entry[0]['name'] . ", " . $entry[0]['id'] . ");
";
    }

    $exports_h .= "
#endif
";

    // create export directory
    $export_dir = $out_dir . '/' . $export[0]['name'];

    if(!mkdir($export_dir))
    {
        die('Failed to create directory for ' . $export[0]['name'] . "\n");
    }

    // write files
    $fd = fopen($export_dir . '/config.h', 'c');

    if(!$fd)
    {
        die('Failed to create config.h for ' . $export[0]['name'] . "\n");
    }

    fwrite($fd, $config_h);
    fclose($fd);

    $fd = fopen($export_dir . '/exports.h', 'c');

    if(!$fd)
    {
        die('Failed to create exports.h for ' . $export[0]['name'] . "\n");
    }

    fwrite($fd, $exports_h);
    fclose($fd);

    if(!copy('./Makefile.export', $export_dir . '/Makefile'))
    {
        die('Failed to copy Makefile.export\n');
    }

    echo('Exported: ' . $export[0]['name'] . "\n");
}
