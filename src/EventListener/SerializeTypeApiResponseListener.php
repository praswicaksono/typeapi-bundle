<?php

declare(strict_types=1);

namespace Pras\TypeApiBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SerializeTypeApiResponseListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['onKernelView', -255],
            ],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $value = $event->getControllerResult();

        if (!$value instanceof \JsonSerializable) {
            return;
        }

        $event->setResponse(new JsonResponse($value->jsonSerialize()));
    }
}
