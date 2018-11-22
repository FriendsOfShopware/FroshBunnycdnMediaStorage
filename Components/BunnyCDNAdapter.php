<?php

namespace TinectMediaBunnycdn\Components;

use Doctrine\Common\Cache\FilesystemCache;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BunnyCDNAdapter implements AdapterInterface
{

    private $apiKey;
    private $apiUrl;
    private $url;

    /** @var FilesystemCache */
    private $cache;

    /** @var ContainerInterface */
    private $container;

    public function __construct($config, FilesystemCache $cache, ContainerInterface $container)
    {
        $this->apiUrl = $config['apiUrl'];
        $this->apiKey = $config['apiKey'];
        $this->url = $config['mediaUrl'];
        $this->container = $container;
        $this->cache = $cache;
    }


    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     * @throws \Zend_Cache_Exception
     */
    public function write($path, $contents, Config $config)
    {

        $stream = tmpfile();
        fwrite($stream, $contents);
        rewind($stream);
        $result = $this->writeStream($path, $stream, $config);
        fclose($stream);

        if ($result === false) {
            return false;
        }

        $result['contents'] = $contents;
        $result['mimetype'] = Util::guessMimeType($path, $contents);

        return $result;

    }

    /**
     * Write a new file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        //$dataLength = filesize($resource);
        $curl = curl_init();
        curl_setopt_array($curl,
            array(
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_URL => $this->apiUrl . $path,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_TIMEOUT => 60000,
                CURLOPT_FOLLOWLOCATION => 0,
                CURLOPT_FAILONERROR => 0,
                CURLOPT_SSL_VERIFYPEER => 1,
                CURLOPT_VERBOSE => 0,
                CURLOPT_INFILE => $resource,
                CURLOPT_INFILESIZE => fstat($resource)['size'],
                CURLOPT_UPLOAD => 1,
                CURLOPT_HTTPHEADER => array(
                    'accesskey: ' . $this->apiKey
                )
            ));
        // Send the request
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);// Cleanup
        curl_close($curl);
        fclose($resource);

        if ($http_code != 201) {
            return false;
        }

        $result = $this->getCached($path);

        if (!$result[$path]) {
            $result[$path] = true;
            $this->cache->save($this->getCacheKey($path), $result);
        }

        $type = 'file';

        return compact('type', 'path', 'visibility');

    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     * @throws \Zend_Cache_Exception
     */
    public function update($path, $contents, Config $config)
    {
        $this->delete($path);
        return $this->write($path, $contents, $config);
    }

    /**
     * Update a file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     * @throws \Zend_Cache_Exception
     */
    public function updateStream($path, $resource, Config $config)
    {
        $this->delete($path);
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     * @throws \Zend_Cache_Exception
     */
    public function rename($path, $newpath)
    {
        $this->write($newpath, $this->read($path), new Config()); //TODO: check config
        $this->delete($path);

        return true;
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     * @throws \Zend_Cache_Exception
     */
    public function copy($path, $newpath)
    {
        $this->write($newpath, $this->read($path), new Config()); //TODO: check config

        return true;
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        $curl = curl_init();

        curl_setopt_array($curl,
            array(
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_URL => $this->apiUrl . $path,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/json',
                    'AccessKey:' . $this->apiKey
                )
            ));

        $result = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        //For error checking
        if ($result === false || $http_code != 200) {
            return false;
        }

        $this->cache->delete($this->getCacheKey($path));

        return true;
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return $this->delete($dirname);
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        return [];
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        return [];
    }

    private function getCacheKey($path)
    {
        return md5($path)[0];
    }

    private function getCached($path)
    {
        $cacheId = $this->getCacheKey($path);

        $result = $this->cache->fetch($cacheId);

        if ($result) {
            return $result;
        }

        return [];

    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        if ($this->container->has('Shop')) {
            return true;
        }

        $result = $this->getCached($path);

        if (!$result[$path]) {
            if ((bool)$this->getSize($path)) {
                $result[$path] = true;
                $this->cache->save($this->getCacheKey($path), $result);
            }
        }

        return $result[$path];
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        if (!$object = $this->readStream($path)) {
            return false;
        }
        $object['contents'] = stream_get_contents($object['stream']);
        fclose($object['stream']);
        unset($object['stream']);

        return $object;
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        return [
            'type' => 'file',
            'path' => $path,
            'stream' => fopen($this->apiUrl . $path . '?AccessKey=' . $this->apiKey, 'r')
        ];

    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        // TODO: Implement listContents() method.
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {

        $headers = get_headers($this->apiUrl . $path . '?AccessKey=' . $this->apiKey, true);

        if (strpos($headers[0], '200') === false) {
            return false;
        }

        return [
            'type' => 'file',
            'path' => $path,
            'timestamp' => (int)strtotime($headers['Last-Modified']),
            'size' => (int)$headers['Content-Length'],
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
            'mimetype' => $headers['Content-Type'],
        ];

    }

    /**
     * Get the size of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        return [
            'path' => $path,
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
        ];
    }
}
