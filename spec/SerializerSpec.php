<?php

namespace spec\Bernard;

use Bernard\Envelope;
use Normalt\Normalizer\AggregateNormalizer;

class SerializerSpec extends \PhpSpec\ObjectBehavior
{
    function let(AggregateNormalizer $aggregate)
    {
        $this->beConstructedWith($aggregate);
    }

    function it_serializes_normalized_envelope_into_json(Envelope $envelope, AggregateNormalizer $aggregate)
    {
        $aggregate->normalize($envelope, null)->willReturn(array(
            'class' => 'Bernard\\Message\\PlainMessage',
            'timestamp' => 1337,
            'message' => array('name' => 'Import', 'arguments' => array('arg1' => 'value')),
        ));

        $this->serialize($envelope)
            ->shouldReturn('{"class":"Bernard\\\\Message\\\\PlainMessage","timestamp":1337,"message":{"name":"Import","arguments":{"arg1":"value"}}}');
    }

    function it_unserializes_into_envelope(Envelope $envelope, AggregateNormalizer $aggregate)
    {
        $normalized = array(
            'class' => 'Bernard\\Message\\PlainMessage',
            'timestamp' => 1337,
            'message' => array('name' => 'Import', 'arguments' => array('arg1' => 'value')),
        );

        $aggregate->denormalize($normalized, 'Bernard\Envelope', null)->willReturn($envelope);

        $this->unserialize('{"class":"Bernard\\\\Message\\\\PlainMessage","timestamp":1337,"message":{"name":"Import","arguments":{"arg1":"value"}}}')
            ->shouldReturn($envelope);
    }
}