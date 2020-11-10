<?php

namespace Bernard\Router;

use Bernard\Envelope;
use Bernard\Receiver;

/**
 * SimpleReceiverResolver supports various receiver inputs, like classes objects and callables.
 */
class SimpleReceiverResolver implements ReceiverResolver
{
    /**
     * {@inheritdoc}
     */
    public function accepts($receiver)
    {
        return is_callable($receiver) || is_object($receiver) || class_exists($receiver);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($receiver, Envelope $envelope)
    {
        if (null === $receiver) {
            return null;
        }

        if ($receiver instanceof Receiver) {
            return $receiver;
        }

        if (is_callable($receiver) == false) {
            $receiver = [$receiver, lcfirst($envelope->getName())];
        }

        // Receiver is still not a callable which means it's not a valid receiver.
        if (is_callable($receiver) == false) {
            return null;
        }

        return new Receiver\CallableReceiver($receiver);
    }

    public function resolveDebug($receiver, Envelope $envelope, &$point = 0)
    {
        if (null === $receiver) {
            $point = 1;
            return null;
        }

        if ($receiver instanceof Receiver) {
            $point = 2;
            return $receiver;
        }

        if (is_callable($receiver) == false) {
            $point = 3;
            $receiver = [$receiver, lcfirst($envelope->getName())];
        }

        // Receiver is still not a callable which means it's not a valid receiver.
        if (is_callable($receiver) == false) {
            $point = 4;
            return null;
        }

        $point = 5;
        return new Receiver\CallableReceiver($receiver);
    }
}
