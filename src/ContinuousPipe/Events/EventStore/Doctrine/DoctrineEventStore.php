<?php

namespace ContinuousPipe\Events\EventStore\Doctrine;

use ContinuousPipe\Events\EventStore\EventMetadata;
use ContinuousPipe\Events\EventStore\EventStore;
use ContinuousPipe\Events\EventStore\EventStoreException;
use ContinuousPipe\Events\EventStore\EventWithMetadata;
use ContinuousPipe\Events\TimeResolver\TimeResolver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Ramsey\Uuid\Uuid;

class DoctrineEventStore implements EventStore
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var TimeResolver
     */
    private $timeResolver;

    /**
     * @param EntityManager $entityManager
     * @param SerializerInterface $serializer
     * @param TimeResolver $timeResolver
     */
    public function __construct(
        EntityManager $entityManager,
        SerializerInterface $serializer,
        TimeResolver $timeResolver
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->timeResolver = $timeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function store(string $stream, $event)
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setAttribute('should-obfuscate', false);

        $dataTransferObject = new EventDto(
            Uuid::uuid4(),
            $stream,
            get_class($event),
            $this->serializer->serialize($event, 'json', $serializationContext),
            $this->timeResolver->resolve()
        );

        try {
            $this->entityManager->persist($dataTransferObject);
            $this->entityManager->flush($dataTransferObject);
        } catch (ORMException $e) {
            throw new EventStoreException('Unable to store the event', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $stream): array
    {
        return array_map(function (EventWithMetadata $eventWithMetadata) {
            return $eventWithMetadata->getEvent();
        }, $this->readWithMetadata($stream));
    }

    /**
     * {@inheritdoc}
     */
    public function readWithMetadata(string $stream): array
    {
        $dataTransferObjects = $this->entityManager->getRepository(EventDto::class)->findBy([
            'stream' => $stream,
        ], [
            'creationDate' => 'ASC'
        ]);

        return array_map(function (EventDto $dataTransferObject) {
            return new EventWithMetadata(
                new EventMetadata($dataTransferObject->getCreationDate()),
                $this->serializer->deserialize(
                    $dataTransferObject->getJsonSerialized(),
                    $dataTransferObject->getClass(),
                    'json'
                )
            );
        }, $dataTransferObjects);
    }
}
