<?php
/*
 * This file is part of the infotech/yii-document-generator package.
 *
 * (c) Infotech, Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infotech\DocumentGenerator\DataFetcher;

interface FetcherInterface
{

    /**
     * Get placeholders descriptions for template designers
     *
     * @return array Placeholders to its descriptions map
     */
    public function getPlaceholdersInfo();

    /**
     * Get placeholder substitutions by key
     *
     * @param string $key data identifier to fetch strings
     * @return array Placeholders to data strings map
     * @throw DataNotFoundException when can't find data associated with specified key
     */
    public function getData($key);
}
