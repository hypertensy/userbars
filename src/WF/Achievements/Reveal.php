<?php declare(strict_types = 1);

namespace WF\Achievement;

class Reveal
{
    private array $base;
    public array $get;
    public const CACHE_CATALOG = '/Cache/Images/';

    /**
     * Reveal constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->base = json_decode((new Parser())->getControlCache(), true);
        $this->getAll($data);
    }

    /**
     * @param array $data
     * @return array
     */
    private function getAll(array $data): array
    {
        foreach ($data as $k => $v) {
            $this->defineAchievement($v, $k);
        }

        $this->performSaving();

        return $this->get;
    }

    /**
     * @param int $id
     * @param string $type
     */
    private function defineAchievement(int $id, string $type): void
    {
        $search = array_search($id, array_column($this->base, 'id'));

        if (($search === false) || ($type !== $this->base[$search]['type'])) {
            throw new \InvalidArgumentException('Invalid ID or type specified');
        }

        $this->get[$type] = $this->base[$search];
    }

    private function performSaving(): void
    {
        $mh = curl_multi_init();
        $fp = [];
        $ch = [];

        foreach (array_column(array_values($this->get), 'image') as $k => $el)
        {
            $file = __DIR__ . self::CACHE_CATALOG . basename($el);

            if (!file_exists($file))
            {
                $ch[$k] = curl_init(Parser::BASE_URL . $el);
                $fp[$k] = fopen($file, 'w+');

                curl_setopt($ch[$k], CURLOPT_FILE, $fp[$k]);
                curl_multi_add_handle($mh, $ch[$k]);

                do {
                    curl_multi_exec($mh, $running);
                } while ($running > 0);

                curl_multi_remove_handle($mh, $ch[$k]);
                curl_close($ch[$k]);
                fclose($fp[$k]);
            }
        }

        curl_multi_close($mh);
    }
}