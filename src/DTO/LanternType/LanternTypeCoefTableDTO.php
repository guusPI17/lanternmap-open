<?php

namespace App\DTO\LanternType;

use JMS\Serializer\Annotation as Serializer;

class LanternTypeCoefTableDTO
{
    /**
     * @Serializer\Type("array<App\DTO\LanternType\TableXhDTO>")
     */
    private $tableXh;

    /**
     * @Serializer\Type("array<App\DTO\LanternType\TableYhDTO>")
     */
    private $tableYh;

    public function getTableXh(): array
    {
        return $this->tableXh;
    }

    public function setTableXh(array $tableXh): void
    {
        $this->tableXh = $tableXh;
    }

    public function getTableYh(): array
    {
        return $this->tableYh;
    }

    public function setTableYh(array $tableYh): void
    {
        $this->tableYh = $tableYh;
    }
}
