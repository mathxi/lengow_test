<?php

namespace App\Entity;

use App\Entity\OrderLine;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="lgw_test_order")
 */
class Order
{
    public const STATUS_NEW = 'new';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_CANCELED = 'canceled';

    public const ALL_STATUS = [
        self::STATUS_NEW,
        self::STATUS_SHIPPED,
        self::STATUS_CANCELED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="orders")
     * @ORM\JoinColumn(name="customer_id", nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLine", mappedBy="order", orphanRemoval=true, cascade={"persist"})
     */
    private $orderLines;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection|OrderLine[]
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines[] = $orderLine;
            $orderLine->setOrder($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->contains($orderLine)) {
            $this->orderLines->removeElement($orderLine);
            // set the owning side to null (unless already changed)
            if ($orderLine->getOrder() === $this) {
                $orderLine->setOrder(null);
            }
        }

        return $this;
    }

}
