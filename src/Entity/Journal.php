<?php

namespace isoft\fmtsf4\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JournalRepository")
 */
class Journal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tableName;

    /**
     * @ORM\Column(type="guid")
     */
    private $recordId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $action;

    /**
     * @ORM\Column(type="json")
     */
    private $json;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return $this
     */
    public function setEntity(string $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username = null)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken(string $token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * @param mixed $recordId
     */
    public function setRecordId($recordId)
    {
        $this->recordId = $recordId;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param $json
     * @return $this
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
