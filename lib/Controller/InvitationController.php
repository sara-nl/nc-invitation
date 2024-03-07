<?php

/**
 * Invitation controller.
 *
 */

namespace OCA\Invitation\Controller;

use Exception;
use OCA\Invitation\AppInfo\InvitationApp;
use OCA\Invitation\AppInfo\AppError;
use OCA\Invitation\Service\InvitationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\ILogger;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class InvitationController extends Controller
{
    private InvitationService $service;
    private LoggerInterface $logger;

    public function __construct(
        IRequest $request,
        InvitationService $service,
        LoggerInterface $logger
    ) {
        parent::__construct(InvitationApp::APP_NAME, $request);
        $this->service = $service;
        $this->logger = $logger;
    }

    /**
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function index(): TemplateResponse
    {
        return new TemplateResponse($this->appName, 'invitation.index');
    }

    /**
     * example url: https://rd-1.nl/apps/invitation/find-all-invitations?fields=[{"status":"open"},{"status":"accepted"}]
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function findAll(string $fields = null): DataResponse
    {
        if (!isset($fields)) {
            $this->logger->error("findAll() - missing parameter 'fields'.", ['app' => InvitationApp::APP_NAME]);
            return new DataResponse(
                [
                    'success' => false,
                    'error_message' => AppError::REQUEST_MISSING_PARAMETER,
                ],
                Http::STATUS_NOT_FOUND,
            );
        }
        try {
            $fieldsAndValues = json_decode($fields, true);
            $invitations = $this->service->findAll($fieldsAndValues);
            return new DataResponse(
                [
                    'success' => true,
                    'data' => $invitations,
                ],
                Http::STATUS_OK
            );
        } catch (Exception $e) {
            $this->logger->error('invitations not found for fields: ' . print_r($fields, true) . 'Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString(), ['app' => InvitationApp::APP_NAME]);
            return new DataResponse(
                [
                    'success' => false,
                    'error_message' => AppError::ERROR,
                ],
                Http::STATUS_NOT_FOUND,
            );
        }
    }
}
