<?php
/*
 * This file is part of the infotech/yii-document-generator package.
 *
 * (c) Infotech, Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infotech\DocumentGenerator\Service;

use CApplicationComponent;
use Infotech\DocumentGenerator\DocumentType\AbstractDocumentType;
use Infotech\DocumentGenerator\Renderer\RendererInterface;

/**
 * Document generator service application component
 *
 * Configuration example:
 * <code>
 *     ...
 *     "components" => [
 *         ...
 *         "documentGenerator" => [
 *             "class" => "Infotech\\DocumentGenerator\\Service\\GeneratorService",
 *             "renderersConfig" => [
 *                 "pdf" => "MyProject\\DocumentGenerator\\TemplateRenderer\\PDFRenderer", // implements Infotech\DocumentGenerator\Renderer\RendererInterface
 *                 ...
 *             ],
 *             "documentTypesConfig" => [
 *                 "someDocument" => "MyProject\\DocumentGenerator\\DocumentType\\SomeDocumentType", // descendant of Infotech\DocumentGenerator\DocumentType\AbstractDocumentType
 *                 ...
 *             ]
 *         ],
 *         ...
 *     ],
 *     ...
 * </code>
 */
class GeneratorService extends CApplicationComponent
{

    private $documentTypes = array();

    private $renderers = array();

    /**
     * Instantiate and registering document renderers
     *
     * @param array $renderers Renderers configuration map [name => fully qualified class name]
     * @throws GeneratorServiceException if map contains name of class that is not implementing RendererInterface
     * @throws GeneratorServiceException if map contains renderer name that already has been registered
     */
    public function setRenderersConfig(array $renderers)
    {
        foreach ($renderers as $name => $class) {
            $renderer = new $class;
            if (!$renderer instanceof RendererInterface) {
                throw new GeneratorServiceException(
                    'Configured class of renderer must implement'
                    . ' Infotech\DocumentGenerator\Renderer\RendererInterface interface'
                );
            }
            $this->registerRenderer($name, $renderer);
        }
    }

    /**
     * Instantiate and registering document types
     *
     * @param array $documentTypes Document types configuration map [name => fully qualified class name]
     * @throws GeneratorServiceException if map contains name of class that is not descendant of AbstractDocumentType
     * @throws GeneratorServiceException if map contains type name that already has been registered
     */
    public function setDocumentTypesConfig(array $documentTypes)
    {
        foreach ($documentTypes as $name => $class) {
            $documentType = new $class;
            if (!$documentType instanceof AbstractDocumentType) {
                throw new GeneratorServiceException(
                    'Configured class of document type must be descendant of'
                        . ' Infotech\DocumentGenerator\DocumentType\AbstractDocumentType class'
                );
            }
            $this->registerDocumentType($name, $documentType);
        }
    }

    /**
     * @return AbstractDocumentType[]
     */
    public function getDocumentTypes()
    {
        return $this->documentTypes;
    }

    /**
     * @return RendererInterface[]
     */
    public function getRenderers()
    {
        return $this->renderers;
    }

    /**
     * @param string                $name             Document type name
     * @param AbstractDocumentType  $documentType
     * @throws GeneratorServiceException while registering document type with same name twice
     */
    public function registerDocumentType($name, AbstractDocumentType $documentType)
    {
        if (isset($this->documentTypes[$name])) {
            throw new GeneratorServiceException('Can\'t register document type with same name twice.');
        }
        $this->documentTypes[$name] = $documentType;
    }

    /**
     * @param string $name Renderer name
     * @param RendererInterface $renderer
     * @throws GeneratorServiceException while registering renderer with same name twice
     */
    public function registerRenderer($name, RendererInterface $renderer)
    {
        if (isset($this->renderers[$name])) {
            throw new GeneratorServiceException('Can\'t register renderer with same name twice.');
        }
        $this->renderers[$name] = $renderer;
    }

}
