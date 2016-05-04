<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Supervizor\Budget;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class BudgetGroup
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="budget_group")
 */
class BudgetGroup extends Nette\Object
{

    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="string",length=6000,nullable=false)
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(type="integer",nullable=false)
     */
    private $x;

    /**
     * @var int
     * @ORM\Column(type="integer",nullable=false)
     */
    private $y;

    /**
     * @var string
     * @ORM\Column(type="string",length=6,nullable=false)
     */
    private $color;

    /**
     * @var ArrayCollection|BudgetItem[]
     * @ORM\OneToMany(targetEntity="\Supervizor\Budget\BudgetItem", mappedBy="budgetGroup",cascade={"persist"})
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

    /**
     * BudgetGroup constructor.
     * @param string $name
     * @param string $description
     * @param int $x
     * @param int $y
     * @param string $color
     */
    public function __construct($name, $slug, $description, $x, $y, $color)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->setX($x);
        $this->setY($y);
        $this->setColor($color);
        $this->budgetItems = new ArrayCollection();
    }

    /**
     * @param int $x
     */
    protected function setX($x)
    {
        if (!is_numeric($x) || !$x)
        {
            throw new Nette\InvalidArgumentException('Invalid $x value');
        }
        $this->x = $x;
    }

    /**
     * @param int $y
     */
    protected function setY($y)
    {
        if (!is_numeric($y) || !$y)
        {
            throw new Nette\InvalidArgumentException('Invalid $y value');
        }
        $this->y = $y;
    }

    /**
     * @param string $color
     */
    protected function setColor($color)
    {
        if (Nette\Utils\Strings::length($color) !== 6)
        {
            throw new Nette\InvalidArgumentException('Invalid $color value');
        }
        $this->color = $color;
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
     * @return BudgetItem[]|ArrayCollection
     */
    public function getBudgetItems()
    {
        return $this->budgetItems;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * 
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        $amount = 0;

        foreach ($this->getBudgetItems() as $budgetItem) {
            $amount += $budgetItem->getTotalAmount();
        }
        
        return $amount;
    }

}
