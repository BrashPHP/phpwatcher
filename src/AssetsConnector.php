<?php

namespace PhpWatcher;

class AssetsConnector
{
    /**
     * Loads assets from github releases
     *
     * @return AssetRelease[]
     */
    public function getAssets(): array
    {
        $assets = [];
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
                !(
                    str_ends_with($asset['name'], 'sha256sum') ||
                    str_ends_with($asset['name'], 'whl') ||
                    str_contains($asset['name'], "musl") ||
                    str_contains($asset['name'], "gnueabihf")
                )
            );
            $targetOss = ['linux' => OsEnum::LINUX, 'windows' => OsEnum::WINDOWS, 'darwin' => OsEnum::DARWIN];
            $assets = array_map(function (array $element) use ($targetOss) {
                ["name" => $name, "browser_download_url" => $url] = $element;
                $os = OsEnum::UNKNOWN;
                foreach ($targetOss as $targetOs => $osType) {
                    if (str_contains($name, $targetOs)) {
                        $os = $osType;
                        break;
                    }
                }
                $architecture = str_contains($name, "x86_64") ? ArchitectureEnum::X86 : ArchitectureEnum::ARM;

                return new AssetRelease($name, $url, $os, $architecture);
            }, $filteredAssets);
        }

        curl_close($curl);

        return $assets;
    }
}
