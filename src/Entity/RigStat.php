<?php

namespace App\Entity;

use App\Enum\ImportStatus;
use App\Enum\MiningType;
use App\Util\DateUtil;
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

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @ORM\Column(type="float", options={"default" = 0})
     */
    private $averageSpeed;

    /**
     * @ORM\Column(type="smallint", options={"default" = 0})
     */
    private $warningCount = 0;

    public function import($data, $type)
    {
        $this->setLocalIp($data['ipAddress']);
        $this->setUptime($data['uptime']);
        $this->setType($type);

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
        $this->setTimestamp(new \DateTime());

        // check average
        if ($this->getAverageSpeed() == 0 && $this->getMiningSpeedSum() != 0)
        {
            $this->setAverageSpeed($this->getMiningSpeedSum());
        }
        else
        {
            if ($this->getMiningSpeedSum() == 0) {
                return ImportStatus::SKIP;
            }

            if ($this->getMiningSpeedSum() < $this->getAverageSpeed() * 0.9) {
                if ($this->isNeedToWarn()) {
                    $this->clearWarningCount();
                    return ImportStatus::AVG_ERROR;
                }

                $this->incWarningCount();
                return ImportStatus::SKIP;
            }

            $this->setAverageSpeed(round(($this->getAverageSpeed() + $this->getMiningSpeedSum()) / 2, 2));
        }

        return ImportStatus::OK;
    }

    public function getSpeedPostfix()
    {
        $postfixes = [
            MiningType::ETH => 'Mh/s',
            MiningType::ZEC => 'Sol/s'
        ];

        return $postfixes[$this->getType()];
    }

    public function isOutdated()
    {
        if ($this->getTimestamp() === null) return false;
        return DateUtil::getDiffInMinutes($this->getTimestamp(), new \DateTime()) > 5;
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

    public function __construct()
    {
        $this->setAverageSpeed(0);
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

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getAverageSpeed()
    {
        return $this->averageSpeed;
    }

    /**
     * @param mixed $averageSpeed
     */
    public function setAverageSpeed($averageSpeed)
    {
        $this->averageSpeed = $averageSpeed;
    }

    /**
     * @return mixed
     */
    public function getWarningCount()
    {
        return $this->warningCount;
    }

    /**
     * @param mixed $warningCount
     */
    public function setWarningCount($warningCount)
    {
        $this->warningCount = $warningCount;
    }

    public function incWarningCount()
    {
        $this->setWarningCount($this->getWarningCount() + 1);
    }

    public function isNeedToWarn()
    {
        return $this->getWarningCount() >= 5;
    }

    public function clearWarningCount()
    {
        $this->setWarningCount(0);
    }
}
