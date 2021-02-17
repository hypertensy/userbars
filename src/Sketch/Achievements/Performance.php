<?php

namespace WFub\Sketch\Achievements;

use GuzzleHttp\{Client, Exception\GuzzleException};
use WFub\{Enums\Achievement, Enums\Failure, Sketch\Involved};

class Performance
{
    use Involved;

    private Client $client;

    private array $catalog, $config;

    /**
     * Performance constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->client = new Client();

        $this->tmp['dir'] = $this->getPath(join(DIRECTORY_SEPARATOR, $this->tmp['path']), []);
        $cat = $this->open(['catalog'], 2, $this->tmp['path']);

        if (!(file_exists($cat) && (filemtime($cat) > (time() - $this->tmp['time'])))) {
            try {
                $get = $this->client->get($this->data['catalog']);
                $content = $get->getBody()->getContents();

                file_put_contents($cat, $content, LOCK_EX);
            }
            catch (GuzzleException $e) {
                throw new \DomainException(Failure::ERR_CATALOG);
            }
        } else {
            $content = file_get_contents($cat);
        }

        $this->catalog = json_decode($content, true);
        $this->config = $config;
    }

    /**
     * @param array $list
     * @return array
     */
    public function detected(array $list): array
    {
        $result = [];
        $_types = [1 => Achievement::MARK, Achievement::BADGE, Achievement::STRIPE];

        foreach ($list as $type => $id)
        {
            $search = array_search($id, array_column($this->catalog, 'id'));
            $find = $this->catalog[$search];

            if ($type !== $_types[$find['type']]) {
                throw new \InvalidArgumentException(Failure::ERR_TYPE);
            }

            if (!$search || !$find)  {
                throw new \InvalidArgumentException(Failure::ERR_IMAGE);
            }

            $result[$type] = $this->procedure($find);
        }

        return $result;
    }

    /**
     * @param array $find
     * @return string
     */
    private function procedure(array $find): string
    {
        $getFile = fn (string $catalog): string => $catalog . $find['id'] . '.png';

        if (isset($this->config['dir']))
        {
            $file = str_replace('/', DIRECTORY_SEPARATOR, $this->config['dir']);
            return $this->getExist($getFile($file));
        }
        else
        {
            $file = $getFile($this->tmp['dir']);

            try {
                $file = $this->getExist($file);
            }
            catch (\UnexpectedValueException $e) {
                try {
                    $this->client->get($getFile($this->data['images']), ['sink' => fopen($file, 'w+')]);
                }
                catch (GuzzleException $e) {
                    throw new \DomainException(Failure::ERR_IMAGE);
                }
            } finally {
                return $file;
            }
        }
    }
}