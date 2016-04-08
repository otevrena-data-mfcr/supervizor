<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class BudgetGroup
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="budget_group")
 */
class BudgetGroup extends Nette\Object
{
    use Identifier;
    
    /**
     * @var ArrayCollection|BudgetItem[]
     * @ORM\OneToMany(targetEntity="BudgetItem", mappedBy="budgetGroup",cascade={"persist"})
     */
    private $budgetItems;
    
    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $created;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $updated;

    
    public function __construct()
    {
        $this->budgetItems = new ArrayCollection();
    }
    
    
    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = $this->updated = new \DateTime();
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
    }
    
    /**
     * @return BudgetItems[]|ArrayCollection
     */
    public function getPlanets()
    {
        return $this->budgetItems;
    }
}
