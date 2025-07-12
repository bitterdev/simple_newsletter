<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Subscriber;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberInfoRepository
{

    protected $entityManager;
    protected $application;
    protected $repository;

    public function __construct(
        Application $application,
        EntityManagerInterface $entityManager
    )
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    protected function getRepository()
    {
        if (!$this->repository) {
            $this->repository = $this->entityManager->getRepository(Subscriber::class);
        }

        return $this->repository;
    }

    public function getById($id)
    {
        return $this->get('id', $id);
    }

    public function getByEmail($email)
    {
        return $this->get('email', $email);
    }

    /**
     * @param $where
     * @param $var
     * @return SubscriberInfo|null
     */
    private function get($where, $var)
    {
        /** @var Subscriber $entity */
        $entity = $this->getRepository()->findOneBy([$where => $var]);

        if (!is_object($entity)) {
            return null;
        }

        return $this->getBySubscriberEntity($entity);
    }

    /**
     * @param Subscriber $entity
     * @return SubscriberInfo
     */
    public function getBySubscriberEntity($entity)
    {
        /** @var SubscriberInfo $subscriberInfo */
        $subscriberInfo = $this->application->make(SubscriberInfo::class);
        $subscriberInfo->setEntity($entity);
        return $subscriberInfo;
    }

}