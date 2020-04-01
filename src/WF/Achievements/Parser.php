<?php declare(strict_types = 1);

namespace WF\Achievement;

use DOMDocument;
use DOMXPath;

class Parser
{
    public const BASE_URL = 'https://wfts.su/';
    private const CATALOG = 'achievements';

    private string $cache_file;
    private int $update;

    /**
     * ParseAchievements constructor.
     * @param string $filename
     * @param int $storage_time
     */
    public function __construct(string $filename = 'Base', int $storage_time = 604800)
    {
        $this->cache_file = __DIR__ . '/Cache/' . $filename;
        $this->update = $storage_time;
    }

    /**
     * @return string
     */
    public function getControlCache(): string
    {
        if (!(file_exists($this->cache_file) && (filemtime($this->cache_file) > (time() - $this->update)))) {
            file_put_contents($this->cache_file, json_encode($this->actionParse()), LOCK_EX);
        }

        return file_get_contents($this->cache_file);
    }

    /**
     * @return array
     */
    private function actionParse(): array
    {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML(file_get_contents(self::BASE_URL . self::CATALOG));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $page = $xpath->query('//div[not(contains(@id, "new"))]/div[starts-with(@class,"achievement ")]');

        $resource = [];
        foreach ($page as $key => $value)
        {
            $resource[] = [
                'id' => str_replace('id_', '', $xpath->query('./@id', $value)->item(0)->nodeValue),
                'type' => explode(' ', $xpath->query('./@class', $value)->item(0)->nodeValue)[1],
                'image' => $xpath->query('./div/img/@src', $value)->item(0)->nodeValue,
                'title' => $xpath->query('./div/a', $value)->item(0)->nodeValue,
                'desc' => $xpath->query('./div/text()', $value)->item(0)->nodeValue
            ];
        }

        return $resource;
    }

    public function clearCache(): void
    {
        unlink($this->cache_file);
    }
}