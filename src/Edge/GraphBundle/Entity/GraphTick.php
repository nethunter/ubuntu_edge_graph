<?php

namespace Edge\GraphBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Graph
 *
 * @ORM\Table(name="graph_ticks")
 * @ORM\Entity
 */
class GraphTick
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
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="graph_datetime", type="datetime")
     */
    private $graphDatetime;

    /**
     * @var integer
     *
     * @ORM\Column(name="graph_funding", type="bigint")
     */
    private $graphFunding;


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
     * Set graphId
     *
     * @param integer $graphId
     * @return Graph
     */
    public function setGraphId($graphId)
    {
        $this->graphId = $graphId;
    
        return $this;
    }

    /**
     * Get graphId
     *
     * @return integer 
     */
    public function getGraphId()
    {
        return $this->graphId;
    }

    /**
     * Set graphDatetime
     *
     * @param \DateTime $graphDatetime
     * @return Graph
     */
    public function setGraphDatetime($graphDatetime)
    {
        $this->graphDatetime = $graphDatetime;
    
        return $this;
    }

    /**
     * Get graphDatetime
     *
     * @return \DateTime 
     */
    public function getGraphDatetime()
    {
        return $this->graphDatetime;
    }

    /**
     * Set graphFunding
     *
     * @param integer $graphFunding
     * @return Graph
     */
    public function setGraphFunding($graphFunding)
    {
        $this->graphFunding = $graphFunding;
    
        return $this;
    }

    /**
     * Get graphFunding
     *
     * @return integer 
     */
    public function getGraphFunding()
    {
        return $this->graphFunding;
    }
}
