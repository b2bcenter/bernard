<?php

namespace Bernard\Router;

use Bernard\Envelope;
use Bernard\Exception\ReceiverNotFoundException;
use Bernard\Router;

/**
 * Routes an Envelope to a Receiver based on an internal receiver map.
 */
class ReceiverMapRouter implements Router
{
    protected $receivers = [];

    private $receiverResolver;

    /**
     * @param array                 $receivers
     * @param ReceiverResolver|null $receiverResolver
     */
    public function __construct(array $receivers = [], ReceiverResolver $receiverResolver = null)
    {
        if ($receiverResolver === null) {
            $receiverResolver = new SimpleReceiverResolver();
        }

        $this->receiverResolver = $receiverResolver;

        foreach ($receivers as $name => $receiver) {
            $this->add($name, $receiver);
        }
    }

    /**
     * @param string $name
     * @param mixed  $receiver
     */
    private function add($name, $receiver)
    {
        if (!$this->receiverResolver->accepts($receiver)) {
            throw new \InvalidArgumentException(sprintf('Receiver "%s" is not supported.', $receiver));
        }

        $this->receivers[$name] = $receiver;
    }

    /**
     * {@inheritdoc}
     */
    public function route(Envelope $envelope)
    {
        $receiver = $this->get($this->getName($envelope));
        $receiver = $this->receiverResolver->resolveDebug($receiver, $envelope, $point);

        if (null === $receiver) {
            throw new ReceiverNotFoundException('No receiver found with key_name "'.$this->getName($envelope).'" with point "'.$point.'". Available: "'.var_export($this->receivers, true).'".');
        }

        return $receiver;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    protected function get($name)
    {
        return isset($this->receivers[$name]) ? $this->receivers[$name] : null;
    }

    /**
     * Returns the (message) name to look for in the receiver map.
     *
     * @param Envelope $envelope
     *
     * @return string
     */
    protected function getName(Envelope $envelope)
    {
        return $envelope->getName();
    }
}
