<?php declare(strict_types = 1);

namespace WF\Client;

class Client
{
    public const API_HOST_RU = 'http://api.warface.ru/';
    public const API_HOST_EN = 'http://api.wf.my.com/';

    public bool $language = false;
    public array $user;

    /**
     * Client constructor.
     * @param string $nickname
     * @param int $server
     */
    public function __construct(string $nickname, int $server = 1)
    {
        $this->getProfile($nickname, $server);

        unset($this->user['full_response']);
    }

    /**
     * @param string $nickname
     * @param int $server
     */
    private function getProfile(string $nickname, int $server): void
    {
        if (!(($server >= 1 && $server <= 5) || (mb_strlen($nickname) >= 4 && mb_strlen($nickname) <= 16))) {
            throw new \DomainException('Invalid server range OR nickname');
        }

        $felt = [[1, 2], [4, 5]];
        $host = self::API_HOST_RU;

        if (in_array($server, $felt[1])) {
            $host = self::API_HOST_EN;
            $server = str_replace($felt[1], $felt[0], $server);
            $this->language = true;
        }

        $ch = curl_init($host . '/user/stat?' . http_build_query(['name' => $nickname, 'server' => $server]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            throw new \InvalidArgumentException('Unable to retrieve player information');
        }

        $this->user = json_decode($content, true);
        $this->user['server'] = $server;
    }
}