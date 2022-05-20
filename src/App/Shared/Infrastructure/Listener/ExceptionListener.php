<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class ExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $env       = $_ENV['APP_ENV'];
        $exception = $event->getThrowable();
        $code      = $exception->getCode() ?: 500;
        if (method_exists($exception, 'getStatusCode')) {
            $code = (int)$exception->getStatusCode() ?: 500;
        }
        $message = ($code === 500 && $env === 'prod') ? 'Ошибка сервера!' : $exception->getMessage();
        $customResponse = new JsonResponse([
            'message' => $message,
            'detail'  => $message
        ],
            $code);
        $event->setResponse($customResponse);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * The code must not depend on runtime state as it will only be called at compile time.
     * All logic depending on runtime state must be put into the individual methods handling the events.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => ['onKernelException', -128]
        );
    }
}