<?php

declare(strict_types=1);

namespace App\EventSubscribers;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ControllerSubscriber.
 */
class ControllerSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    /** @var LoggerInterface */
    protected $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event)
    {
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
    }

    /**
     * @param ViewEvent $event
     */
    public function onKernelView(ViewEvent $event)
    {
        $value = $event->getControllerResult();

        $response = new Response();

        $response->setContent(json_encode([
            'status' => 'success',
            'data'   => $value ?: null,
        ]));

        $response->headers->set('Content-Type', 'application/json');

        $event->setResponse($response);
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
    }

    /**
     * @param ExceptionEvent $event
     *
     * @throws \JsonException
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $error     = $exception->getMessage();
        $request   = $event->getRequest();

        $logLevel = LogLevel::CRITICAL;

        $this->logger->log($logLevel, $error, [
            'request_query_string' => $request->getQueryString(),
            'request_raw_post'     => $request->getContent(),
            'request_post'         => $request->request->all(),
            'request_get'          => $request->query->all(),
            'trace'                => $exception->getTraceAsString(),
        ]);

        $response = new Response();

        $response->setContent(json_encode([
            'status'  => 'error',
            'message' => $error,
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR));

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->headers->set('Content-Type', 'application/json');

        // sends the modified response object to the event
        $event->setResponse($response);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST    => 'onKernelRequest',
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::VIEW       => 'onKernelView',
            KernelEvents::RESPONSE   => 'onKernelResponse',
            KernelEvents::EXCEPTION  => 'onKernelException',
        ];
    }
}
