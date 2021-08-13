<?php

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class BadResponseDTO
{
    /**
     * @Serializer\Type("int")
     */
    private $code;

    /**
     * @Serializer\Type("string")
     */
    private $message;

    /**
     * @Serializer\Type("array")
     */
    private $details;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }
}
