<?php

namespace App\Entity;

use App\Enum\RigStatus;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RigRepository")
 */
class Rig
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSeen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Connector", cascade={"persist"})
     * @ORM\JoinColumn(name="connector_id", referencedColumnName="id")
     * @var Connector
     */
    private $connector;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = RigStatus::NOT_WORKING;

    /**
     * This function collects data from remote machine
     */
    public function collectData()
    {
        try {
            $connection = fsockopen(
                $this->getConnector()->getHost(),
                $this->getConnector()->getPort(),
                $errorId,
                $errorMessage,
                30
            );
        } catch (\Exception $e) {
            $this->setStatus(RigStatus::NOT_WORKING);
            return false;
        }

        if (!$connection)
        {
            $this->setStatus(RigStatus::NOT_WORKING);
            return false;
        }

        fclose($connection);

        $this->setStatus(RigStatus::WORKING);
        $this->setLastSeen(new DateTime());
        return true;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * @param mixed $lastSeen
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;
    }

    /**
     * @return Connector
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @param Connector $connector
     */
    public function setConnector(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

}
