<?php

namespace spec\Infotech\DocumentGenerator\Service;

use Infotech\DocumentGenerator\DataFetcher\FetcherInterface;
use Infotech\DocumentGenerator\DocumentType\AbstractDocumentType;
use Infotech\DocumentGenerator\Renderer\RendererInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GeneratorServiceSpec extends ObjectBehavior
{
    public function getMatchers()
    {
        return [
            'haveKeys' => function($subject) {
                $keys = func_get_args();
                array_shift($keys);
                foreach ($keys as $key) {
                    if (!array_key_exists($key, $subject)) {
                        return false;
                    }
                }
                return true;
            },
        ];
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Infotech\DocumentGenerator\Service\GeneratorService');
    }

    function it_is_application_component()
    {
        $this->shouldHaveType('CApplicationComponent');
    }

    function it_has_no_document_types_initially()
    {
        $this->getDocumentTypes()->shouldReturn([]);
    }

    function it_has_no_renderers_initially()
    {
        $this->getRenderers()->shouldReturn([]);
    }

    function it_can_register_new_document_type(AbstractDocumentType $docType)
    {
        $this->registerDocumentType('someDoc', $docType);
        $this->getDocumentTypes()->shouldReturn(['someDoc' => $docType]);
    }

    function it_can_register_new_renderer(RendererInterface $renderer)
    {
        $this->registerRenderer('format', $renderer);
        $this->getRenderers()->shouldReturn(['format' => $renderer]);
    }

    function it_can_not_register_document_type_with_same_name_twice(AbstractDocumentType $docType)
    {
        $this->registerDocumentType('someDoc', $docType);
        $this->shouldThrow('Infotech\DocumentGenerator\Service\GeneratorServiceException')
            ->duringRegisterDocumentType('someDoc', $docType);
    }

    function it_can_not_register_renderer_with_same_name_twice(RendererInterface $renderer)
    {
        $this->registerRenderer('format', $renderer);
        $this->shouldThrow('Infotech\DocumentGenerator\Service\GeneratorServiceException')
            ->duringRegisterRenderer('format', $renderer);
    }

    function it_can_configure_renderers_from_configuration_array()
    {
        $this->setRenderersConfig([
            'fmt-1' => 'spec\Infotech\DocumentGenerator\Service\RendererStub',
            'fmt-2' => 'spec\Infotech\DocumentGenerator\Service\RendererStub'
        ]);
        $this->getRenderers()->shouldHaveCount(2);
        $this->getRenderers()->shouldHaveKeys('fmt-1', 'fmt-2');
    }

    function it_can_configure_document_types_from_configuration_array()
    {
        $this->setDocumentTypesConfig([
            'doctype-1' => 'spec\Infotech\DocumentGenerator\Service\DocumentTypeStub',
            'doctype-2' => 'spec\Infotech\DocumentGenerator\Service\DocumentTypeStub'
        ]);
        $this->getDocumentTypes()->shouldHaveCount(2);
        $this->getDocumentTypes()->shouldHaveKeys('doctype-1', 'doctype-2');
    }

    function it_can_not_configure_renderers_with_invalid_class()
    {
        $this->shouldThrow('Infotech\DocumentGenerator\Service\GeneratorServiceException')
            ->duringSetRenderersConfig([
                'render' => 'stdClass',
            ]);
    }

    function it_can_not_configure_document_types_with_invalid_class()
    {
        $this->shouldThrow('Infotech\DocumentGenerator\Service\GeneratorServiceException')
            ->duringSetDocumentTypesConfig([
                'doctype' => 'stdClass',
            ]);
    }

    function it_can_generate_document_with_proper_renderer_and_type(
        RendererInterface $renderer,
        AbstractDocumentType $docType
    )
    {
        $file = 'some/template/file.format';
        $dataKey = 'data-key';
        $data = ['PLACEHOLDER' => 'substitution'];
        $renderedDocument = 'rendered document';

        $renderer->render($file, $data)->shouldBeCalled()->willReturn($renderedDocument);
        $docType->getData($dataKey, AbstractDocumentType::DEFAULT_FETCHER_NAME)->shouldBeCalled()->willReturn($data);

        $this->registerRenderer('format', $renderer);
        $this->registerDocumentType('doc-type', $docType);
        $this->generate($file, 'format', 'doc-type', $dataKey)->shouldReturn($renderedDocument);
    }

    function it_can_generate_document_with_proper_renderer_and_type_and_fetcher(
        RendererInterface $renderer,
        AbstractDocumentType $docType
    )
    {
        $file = 'some/template/file.format';
        $dataKey = 'data-key';
        $fetcher = 'fetcher';
        $data = ['PLACEHOLDER' => 'substitution'];
        $renderedDocument = 'rendered document';

        $renderer->render($file, $data)->shouldBeCalled()->willReturn($renderedDocument);
        $docType->getData($dataKey, $fetcher)->shouldBeCalled()->willReturn($data);

        $this->registerRenderer('format', $renderer);
        $this->registerDocumentType('doc-type', $docType);
        $this->generate($file, 'format', 'doc-type', $dataKey, $fetcher)->shouldReturn($renderedDocument);
    }
}

class RendererStub implements RendererInterface
{
    public function render($templatePath, array $data)
    {
        return '';
    }
}


class DocumentTypeStub extends AbstractDocumentType
{
    /**
     * @return FetcherInterface
     */
    public function createDataFetcher($name)
    {
        return new FetcherStub();
    }
}

class FetcherStub implements FetcherInterface
{
    public function getPlaceholdersInfo()
    {
        return ['PLACEHOLDER' => 'some placeholder'];
    }

    public function getData($key)
    {
        return ['PLACEHOLDER' => $key];
    }

    public function getSampleData()
    {
        return ['PLACEHOLDER' => 'sample'];
    }
}
