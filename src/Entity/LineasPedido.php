<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LineasPedido
 *
 * @ORM\Table(name="lineas_pedidos", indexes={@ORM\Index(name="fk_linea_pedido", columns={"pedido_id"}), @ORM\Index(name="fk_linea_producto", columns={"producto_id"})})
 * @ORM\Entity
 */
class LineasPedido
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
     * @var int
     *
     * @ORM\Column(name="unidades", type="integer", nullable=false)
     */
    private $unidades;

    /**
     * @var \Pedido
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Pedido", inversedBy="lineasPedido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pedido_id", referencedColumnName="id")
     * })
     */
    private $pedido;

    /**
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producto", inversedBy="lineasPedido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     * })
     */
    private $producto;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnidades(): ?int
    {
        return $this->unidades;
    }

    public function setUnidades(int $unidades): self
    {
        $this->unidades = $unidades;

        return $this;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function setPedido(?Pedido $pedido): self
    {
        $this->pedido = $pedido;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

        return $this;
    }


}
