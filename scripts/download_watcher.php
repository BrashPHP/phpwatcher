<?php

$root = dirname(__DIR__);
$targetDir = "{$root}/bin";
$targetExecutable = "{$targetDir}/wtr.watcher";

if (!(is_dir($targetDir) && is_file($targetExecutable))) {
    $zipFile = __DIR__ . '/zipfile.zip';

    $curl = curl_init('https://api.github.com/repos/e-dant/watcher/releases/latest');
    curl_setopt_array($curl, [
        CURLOPT_HEADER => 0,
        CURLOPT_TIMEOUT => 100,
        CURLOPT_CONNECTTIMEOUT => 100,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 1,
        CURLOPT_USERAGENT => 'Chrome/64.0.3282.186 Safari/537.36',
        CURLOPT_FOLLOWLOCATION => 1
    ]);

    $result = curl_exec($curl);

    if (!curl_errno($curl) && curl_getinfo($curl, CURLINFO_HTTP_CODE) === 200) {
        $response = json_decode($result, true)['assets'] ?? [];
        $filteredAssets = array_filter(
            $response,
            fn($asset) =>
            !str_ends_with($asset['name'], 'sha256sum') &&
            !str_ends_with($asset['name'], 'whl')
        );

        file_put_contents("watcher-response-assets.json", json_encode(array_values($filteredAssets)));
    }

    curl_close($curl);
}


/** @todo: download binary zip */
// curl_setopt($curl, CURLOPT_URL, 'https://github.com/e-dant/watcher/archive/refs/tags/0.13.2.zip');
// curl_setopt($curl, CURLOPT_HEADER, 0);
// curl_setopt($curl, CURLOPT_TIMEOUT, 100);
// curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);
// curl_setopt($curl, CURLOPT_AUTOREFERER, true);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
// curl_setopt($curl, CURLOPT_FILE, $zip);
// curl_setopt($curl, CURLOPT_USERAGENT, 'Chrome/64.0.3282.186 Safari/537.36');
// // curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: token PERSONAL_ACCESS_TOKEN'));
// curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
// $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
// $result = curl_exec($curl);
// curl_close($curl);
// echo "$result";
// }