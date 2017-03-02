<?php

namespace ContinuousPipe\Events\TimeResolver;

class PredictableTimeResolver implements TimeResolver
{
    /**
     * @var TimeResolver
     */
    private $decoratedResolver;

    /**
     * @var \DateTime
     */
    private $current;

    /**
     * @param TimeResolver $decoratedResolver
     */
    public function __construct(TimeResolver $decoratedResolver)
    {
        $this->decoratedResolver = $decoratedResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve() : \DateTimeInterface
    {
        return $this->current ?: $this->decoratedResolver->resolve();
    }

    /**
     * @param \DateTimeInterface $datetime
     */
    public function setCurrent(\DateTimeInterface $datetime)
    {
        $this->current = $datetime;
    }
}
