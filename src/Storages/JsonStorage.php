<?php

namespace Asvae\ApiTester\Storages;

use Asvae\ApiTester\Contracts\StorageInterface;
use Illuminate\Filesystem\Filesystem;

/**
 * Class JsonStorage
 *
 * @package \Asvae\ApiTester
 */
class JsonStorage implements StorageInterface
{
    const ROW_DELIMITER = "\n";

    /**
     * @type \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @type string
     */
    protected $path;

    /**
     * @type string
     */

    /**
     * @type string
     */
    protected $filename;

    /**
     * Storage constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     *
     * @param                                   $path
     * @param                                   $filename
     */
    public function __construct(Filesystem $files, $path, $filename)
    {
        $this->files = $files;
        $this->path = $path;
        $this->filename = $filename;
    }

    /**
     * Return path to folder that can contain file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Return full file path.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->getPath() . '/' . $this->getFilename();
    }

    /**
     * Return array parsed from file content.
     *
     * @return array
     */
    public function get()
    {
        $fullPath = $this->getFilePath();

        if ($this->files->exists($fullPath)) {

            $content = $this->files->get($fullPath);

            return $this->parseResult($content);
        }

        return [];
    }

    /**
     * Convert data array to json rows and put to file.
     *
     * @param \Traversable|array $data
     */
    public function put($data)
    {
        $this->createDirectoryIfNotExists();

        $content = $this->prepareContent($data);

        $this->files->put($this->getFilePath(), $content);
    }

    /**
     * Make directory path if not exists
     */
    protected function createDirectoryIfNotExists()
    {
        if (!is_dir($this->getPath())) {
            $this->files->makeDirectory($this->getPath(), 0755, true);
        }
    }

    /**
     * Parse result form given string
     *
     * @param $content
     *
     * @return array
     */
    protected function parseResult($content)
    {
        $data = [];

        foreach (explode(static::ROW_DELIMITER, $content) as $row) {
            if (empty($row)) {
                continue;
            }

            $data[] = json_decode($row, true);
        }

        return $data;
    }

    /**
     * Prepare content string from given data
     *
     * @param \Traversable|array $data
     *
     * @return string
     */
    private function prepareContent($data)
    {
        $content = '';

        foreach ($data as $row) {
            $content .= json_encode($row) . static::ROW_DELIMITER;
        }

        return $content;
    }
}
