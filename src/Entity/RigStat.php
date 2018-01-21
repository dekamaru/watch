<?php

namespace App\Entity;

use App\Enum\MiningType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RigStatRepository")
 */
class RigStat
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
    private $publicIp;

    /**
     * @ORM\Column(type="string")
     */
    private $localIp;

    /**
     * @ORM\Column(type="json_array")
     */
    private $temps;

    /**
     * @ORM\Column(type="json_array")
     */
    private $fanspeeds;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $miningSpeeds;

    /**
     * @ORM\Column(type="string")
     */
    private $uptime;


    public function load($data, $type)
    {
        $this->setLocalIp($data['ipAddress']);
        $this->setUptime($data['uptime']);

        // set fanspeeds and temps
        $fanspeeds = $this->_filterData(explode(' ', $data['gpu_fanspeed']));
        $temps = $this->_filterData(explode(' ', $data['gpu_temp']));

        $this->setFanspeeds($fanspeeds);
        $this->setTemps($temps);

        if ($type == MiningType::ZEC)
        {
            preg_match_all('/========== Sol\/s\: (.*?) Sol\/W/', $data['console'], $matches);
            if (count($matches[1]) > 0) {
                $miningSpeeds = [$matches[1][count($matches[1]) - 1]];
            } else {
                $miningSpeeds = [];
            }
        }
        elseif ($type == MiningType::ETH)
        {
            $miningSpeeds = explode(';', $data['claymore']['result'][3]);
            foreach($miningSpeeds as &$speed)
            {
                $speed = number_format(floatval($speed) / 1000, 2);
            }
        }
        else
        {
            $miningSpeeds = [];
        }

        $this->setMiningSpeeds($miningSpeeds);
    }

    private function _filterData($array)
    {
        $result = [];
        foreach($array as $item)
        {
            if (empty($item)) continue;
            $result[] = intval(trim($item));
        }
        return $result;
    }

    public function getMiningSpeedSum()
    {
        return array_sum($this->getMiningSpeeds());
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
    public function getPublicIp()
    {
        return $this->publicIp;
    }

    /**
     * @param mixed $publicIp
     */
    public function setPublicIp($publicIp)
    {
        $this->publicIp = $publicIp;
    }

    /**
     * @return mixed
     */
    public function getLocalIp()
    {
        return $this->localIp;
    }

    /**
     * @param mixed $localIp
     */
    public function setLocalIp($localIp)
    {
        $this->localIp = $localIp;
    }

    /**
     * @return mixed
     */
    public function getTemps()
    {
        return $this->temps;
    }

    /**
     * @param mixed $temps
     */
    public function setTemps($temps)
    {
        $this->temps = $temps;
    }

    /**
     * @return mixed
     */
    public function getFanspeeds()
    {
        return $this->fanspeeds;
    }

    /**
     * @param mixed $fanspeeds
     */
    public function setFanspeeds($fanspeeds)
    {
        $this->fanspeeds = $fanspeeds;
    }

    /**
     * @return mixed
     */
    public function getMiningSpeeds()
    {
        return $this->miningSpeeds;
    }

    /**
     * @param mixed $miningSpeeds
     */
    public function setMiningSpeeds($miningSpeeds)
    {
        $this->miningSpeeds = $miningSpeeds;
    }

    /**
     * @return mixed
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * @param mixed $uptime
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;
    }
}
