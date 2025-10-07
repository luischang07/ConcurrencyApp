<?php

namespace App\Domain\Orders\Services;
class Inventario
{
    private array $stockPorMedicamento = [];
    private array $cambios = [];
    private int $sucursalId;

    public function __construct(int $sucursalId, array $stockInicial)
    {
        $this->sucursalId = $sucursalId;
        foreach($stockInicial as $item){
            $this->stockPorMedicamento[$item->medicamentos_id] = $item->stock;
        }
    }
    public function consultarStock(int $medicamentoId): int
    {
        return $this->stockPorMedicamento[$medicamentoId] ?? 0;
    }
    public function descontarStock(int $medicamentoId, int $cantidad): bool
    {
        if($this->consultarStock($medicamentoId) < $cantidad){
            return false;
        }
        $this->stockPorMedicamento[$medicamentoId]-=$cantidad;
        $this->cambios[] = ['medicamentoId' => $medicamentoId, 'cantidad' => $cantidad];
        return true;
    }
    public function getCambios(): array
    {
        return $this->cambios;
    }
    public function getSucursalId(): int
    {
        return $this->sucursalId;
    }
}