<?php
/*
 * This file is part of the infotech/yii-document-generator package.
 *
 * (c) Infotech, Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infotech\DocumentGenerator\DocumentType;

use Infotech\DocumentGenerator\DataFetcher\FetcherInterface;

/**
 * Abstract type of the generated document
 */
abstract class AbstractDocumentType
{
    const DEFAULT_FETCHER_NAME = 'default';

    /**
     * @var FetcherInterface[]
     */
    private $dataFetchers = array();

    public function getPlaceholdersInfo()
    {
        return $this->getDataFetcher(self::DEFAULT_FETCHER_NAME)->getPlaceholdersInfo();
    }

    /**
     * @return FetcherInterface
     */
    public function getDataFetcher($fetcher)
    {
        $fetcher = (string)$fetcher;
        if (!isset($this->dataFetchers[$fetcher])) {
            $this->dataFetchers[$fetcher] = $this->createDataFetcher($fetcher);
        }
        return $this->dataFetchers[$fetcher];
    }

    /**
     * Get real substitution data
     *
     * @param mixed $key Data identifier
     * @return array
     */
    public function getData($key, $fetcher = self::DEFAULT_FETCHER_NAME)
    {
        return $this->getDataFetcher($fetcher)->getData($key);
    }

    /**
     * Get sample substitutions data
     *
     * @return array
     */
    public function getSampleData($fetcher = self::DEFAULT_FETCHER_NAME)
    {
        return $this->getDataFetcher($fetcher)->getSampleData();
    }

    /**
     * Create data fetcher by given name
     *
     * @param string   $name        Name of fetcher to create
     * @return FetcherInterface
     */
    abstract public function createDataFetcher($name);

}
