<?php

namespace isoft\fmtsf4\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
//use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use isoft\fmtsf4\Entity\Journal;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class JournalSubscriber implements EventSubscriber
{
    protected $nonTrackedEntities = [
        'isoft\fmtsf4\Entity\Journal',
        'App\Entity\AwsCloudWatch',
        'App\Entity\Session',
        'App\Entity\Credential'
    ];
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
    }

    public function getSubscribedEvents()
    {
        return [Events::postPersist, Events::postUpdate, Events::preRemove];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $request = isset($GLOBALS["request"]) ? $GLOBALS["request"] : null;

        if ($this->isJournalable($entity)) {
            $this->createJournalEntry($entity, $args->getEntityManager(), 'insert', $request);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $request = isset($GLOBALS["request"]) ? $GLOBALS["request"] : null;

        if ($this->isJournalable($entity)) {
            $this->createJournalEntry($entity, $args->getEntityManager(), 'update', $request);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $request = isset($GLOBALS["request"]) ? $GLOBALS["request"] : null;

        if ($this->isJournalable($entity)) {
            $this->createJournalEntry($entity, $args->getEntityManager(), 'delete', $request);
        }
    }

    /**
     * Check if must be tracked
     * @param $entity
     * @return bool
     */
    private function isJournalable($entity)
    {
        $isJournalable = true;

        //don't track certain tables
        foreach ($this->nonTrackedEntities as $nonTrackedEntity) {
            if (get_class($entity) == $nonTrackedEntity) {
                $isJournalable = false;
            }
        }

        return $isJournalable;
    }

    /**
     * Check if it is just a timestamp change
     * @param $changeSet
     * @return bool
     */
    private function isJustTimestampChange($changeSet)
    {
        if (array_key_exists('updatedAt', $changeSet)) {
            unset($changeSet['updatedAt']);
        }

        if (array_key_exists('createdAt', $changeSet)) {
            unset($changeSet['createdAt']);
        }

        return count($changeSet) == 0;
    }

    /**
     * Creates a journal entry
     * @param $entity
     * @param $db
     * @param $action
     */
    private function createJournalEntry($entity, $db, $action, $request)
    {
        try {
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($entity) {
                return get_class($entity);
            });
            $this->serializer = new Serializer(array($normalizer), array($encoder));
            //setJson field must be an array, symfony will save it as json
            $arrayEntity = $this->serializer->serialize($entity, 'json');
            $arrayEntity = json_decode($arrayEntity);
            //Elimina Sub-Entidades
            foreach ($arrayEntity as $i => $e) {
                if (is_array($e)) {
                    unset($arrayEntity->$i);
                }
            }
        } catch (\Exception $exc) {
        }

        $entry = new Journal();
        $entry->setEntity(get_class($entity));
        $entry->setTableName($db->getClassMetadata(get_class($entity))->getTableName());
        $entry->setRecordId($entity->getId());
        $entry->setAction($action);
        $entry->setJson($arrayEntity);
        if ($request != null) {
            $entry->setUsername($request->headers->get("x-consumer-username"));
            if ($request->headers->has("fastoken")) {
                $entry->setToken($request->headers->get("fastoken"));
            } elseif ($request->headers->has("admintoken")) {
                $entry->setToken($request->headers->get("admintoken"));
            }
        }
        $db->persist($entry);
        $db->flush();
    }
}
