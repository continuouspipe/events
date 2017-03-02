<?php

namespace ContinuousPipe\Events\EventStore;

interface EventStore
{
    /**
     * Write an event to the given stream.
     *
     * @param string $stream
     * @param mixed $event
     *
     * @throws EventStoreException
     */
    public function store(string $stream, $event);

    /**
     * Read all the events from the given stream.
     *
     * @param string $stream
     *
     * @throws EventStoreException
     *
     * @return mixed[]
     */
    public function read(string $stream) : array;

    /**
     * Read all the events from the given stream, with their metadata.
     *
     * @param string $stream
     *
     * @throws EventStoreException
     *
     * @return EventWithMetadata[]
     */
    public function readWithMetadata(string $stream) : array;
}
