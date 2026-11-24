<?php

namespace Scue\Geo\Controller\Rest;

use Magento\Framework\App\State;
use Throwable;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Validator\Exception;
use Magento\Framework\Webapi\Rest\Response\FieldsFilter;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Magento\Webapi\Controller\Rest\InputParamsResolver;
use Magento\Webapi\Controller\Rest\SynchronousRequestProcessor as Controller;
use Magento\Framework\Webapi\Rest\Response as RestResponse;
use Magento\Framework\Locale\Resolver as LocalResolver;
use Magento\Framework\TranslateInterface;
use Scue\Geo\Model\LanguageDetectionSupport;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Message\MessageInterface;

class SynchronousRequestProcessor extends Controller
{
    use LanguageDetectionSupport;

    const GEO_PROCESSOR_PATH = '/\/geo\/city\//';

    public function __construct(
        private State $appState,
        private RestResponse $response,
        Request $request,
        LocalResolver $localResolver,
        TranslateInterface $translate,
        InputParamsResolver $inputParamsResolver,
        ServiceOutputProcessor $serviceOutputProcessor,
        FieldsFilter $fieldsFilter,
        DeploymentConfig $deploymentConfig,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($response, $inputParamsResolver, $serviceOutputProcessor, $fieldsFilter, $deploymentConfig, $objectManager);

        if ($this->canProcess($request)) {
            $local = $this->getLanguageByRequest($request)->getLocale();
            $translate->setLocale($local);
            $localResolver->setLocale($local);
        }
    }

    public function canProcess(Request $request)
    {
        return parent::canProcess($request) && preg_match(self::GEO_PROCESSOR_PATH, $request->getPathInfo()) === 1;
    }

    public function process(Request $request)
    {
        $error = [];

        try {
            parent::process($request);
        } catch (Exception $exception) {
            $this->response->setStatusCode($exception->getCode());
            $error = [
                'message' => $this->renderExceptionMessages($exception, MessageInterface::TYPE_ERROR),
                'details' => $this->renderExceptionMessages($exception, MessageInterface::TYPE_NOTICE),
                'trace' => $exception->getTraceAsString()
            ];
        } catch (Throwable $exception) {
            $this->response->setStatusCode(500);
            $error = [
                'message' => __('Something went wrong'),
                'details' => $exception->getMessage() . ', file: ' . $exception->getFile() . ', line: ' . $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        if (! empty($error)) {
            if ($this->appState->getMode() !== State::MODE_DEVELOPER) {
                unset($error['details']);
                unset($error['trace']);
            }

            $body = json_encode([
                'error' => $error
            ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            $this->response->setBody($body);
        }

        $this->response->setHeader('Content-Type', 'application/json');
    }

    private function renderExceptionMessages(Exception $exception, string $type): ?string
    {
        return implode(', ', array_map(fn ($message) => $message->getText(), $exception->getMessages($type)));
    }
}