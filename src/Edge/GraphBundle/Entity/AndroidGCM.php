<?php

namespace Edge\GraphBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AndroidGCM
 *
 * @ORM\Table(name="android_gcm")
 * @ORM\Entity
 */
class AndroidGCM
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="device_id", type="integer")
     */
    private $deviceId;

    /**
     * @var string
     *
     * @ORM\Column(name="device_reg_id", type="text")
     */
    private $deviceRegId;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set deviceId
     *
     * @param integer $deviceId
     * @return AndroidGCM
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    
        return $this;
    }

    /**
     * Get deviceId
     *
     * @return integer 
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * Set deviceRegId
     *
     * @param string $deviceRegId
     * @return AndroidGCM
     */
    public function setDeviceRegId($deviceRegId)
    {
        $this->deviceRegId = $deviceRegId;
    
        return $this;
    }

    /**
     * Get deviceRegId
     *
     * @return string 
     */
    public function getDeviceRegId()
    {
        return $this->deviceRegId;
    }
}
