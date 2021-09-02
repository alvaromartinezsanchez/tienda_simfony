<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Producto
 *
 * @ORM\Table(name="productos", indexes={@ORM\Index(name="fk_producto_categoria", columns={"categoria_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductoRepository")
 */
class Producto
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
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="text", length=65535, nullable=true)
     */
    private $descripcion;

    /**
     * @var float
     *
     * @ORM\Column(name="precio", type="float", precision=100, scale=2, nullable=false)
     */
    private $precio;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer", nullable=false)
     */
    private $stock;

    /**
     * @var string|null
     *
     * @ORM\Column(name="oferta", type="string", length=2, nullable=true)
     */
    private $oferta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @var \Categoria
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Categoria", inversedBy="producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     * })
     */
    private $categoria;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LineasPedido", mappedBy="producto")
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getOferta(): ?string
    {
        return $this->oferta;
    }

    public function setOferta(?string $oferta): self
    {
        $this->oferta = $oferta;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria($categoria): self
    {
        $this->categoria = $categoria;

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
            $lineasPedido->setProducto($this);
        }

        return $this;
    }

    public function removeLineasPedido(LineasPedido $lineasPedido): self
    {
        if ($this->lineasPedido->removeElement($lineasPedido)) {
            // Pone el campo producto en lineasPedido(tabla) a null
            if ($lineasPedido->getProducto() === $this) {
                $lineasPedido->setProducto(null);
            }
        }

        return $this;
    }

}
