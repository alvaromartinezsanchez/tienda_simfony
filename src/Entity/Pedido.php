<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pedido
 *
 * @ORM\Table(name="pedidos", indexes={@ORM\Index(name="fk_pedido_usuario", columns={"usuario_id"})})
 * @ORM\Entity
 */
class Pedido
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=100, nullable=false)
     */
    private $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=100, nullable=false)
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=false)
     */
    private $direccion;

    /**
     * @var float
     *
     * @ORM\Column(name="coste", type="float", precision=200, scale=2, nullable=false)
     */
    private $coste;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=20, nullable=false)
     */
    private $estado;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="hora", type="time", nullable=true)
     */
    private $hora;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="pedidos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LineasPedido", mappedBy="pedido", cascade={"persist"})
     */
    private $lineasPedido;

    public function __construct()
    {
        $this->lineasPedido = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getLocalidad(): ?string
    {
        return $this->localidad;
    }

    public function setLocalidad(string $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getCoste(): ?float
    {
        return $this->coste;
    }

    public function setCoste(float $coste): self
    {
        $this->coste = $coste;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(?\DateTimeInterface $hora): self
    {
        $this->hora = $hora;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection|LineasPedido[]
     */
    public function getLineasPedido(): Collection
    {
        return $this->lineasPedido;
    }

    public function addLineasPedido(LineasPedido $lineasPedido): self
    {
        if (!$this->lineasPedido->contains($lineasPedido)) {
            $this->lineasPedido[] = $lineasPedido;
            //$lineasPedido->setPedido($this);
        }

        return $this;
    }

    public function removeLineasPedido(LineasPedido $lineasPedido): self
    {
        if ($this->lineasPedido->removeElement($lineasPedido)) {
            // set the owning side to null (unless already changed)
            if ($lineasPedido->getPedido() === $this) {
                $lineasPedido->setPedido(null);
            }
        }

        return $this;
    }


}
