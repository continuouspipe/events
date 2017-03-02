<?php

namespace ContinuousPipe\Events\EventStore;

final class EventWithMetadata
{
    /**
     * @var EventMetadata
     */
    private $metadata;

    /**
     * @var mixed
     */
    private $event;

    /**
     * @param EventMetadata $metadata
     * @param mixed $event
     */
    public function __construct(EventMetadata $metadata, $event)
    {
        $this->metadata = $metadata;
        $this->event = $event;
    }

    /**
     * @return EventMetadata
     */
    public function getMetadata(): EventMetadata
    {
        return $this->metadata;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }
}
