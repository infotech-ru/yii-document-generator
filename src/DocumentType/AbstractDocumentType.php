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
    /**
     * @var FetcherInterface
     */
    private $dataFetcher;

    public function getPlaceholdersInfo()
    {
        return $this->getDataFetcher()->getPlaceholdersInfo();
    }

    /**
     * @return FetcherInterface
     */
    public function getDataFetcher()
    {
        if (null === $this->dataFetcher) {
            $this->dataFetcher = $this->createDataFetcher();
        }
        return $this->dataFetcher;
    }

    public function getData($key)
    {
        return $this->getDataFetcher()->getData($key);
    }

    /**
     * @return FetcherInterface
     */
    abstract public function createDataFetcher();

} 