<?php

namespace ContinuousPipe\Events\EventStore;

final class EventMetadata
{
    /**
     * @var \DateTimeInterface
     */
    private $dateTime;

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function __construct(\DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }
}
