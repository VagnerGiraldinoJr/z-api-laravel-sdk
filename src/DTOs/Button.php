<?php

namespace SuaEmpresa\ZApi\DTOs;

use InvalidArgumentException;

class Button
{
    protected string $id;
    protected string $type;
    protected string $label;
    protected ?string $url = null;
    protected ?string $phone = null;

    public function __construct(
        string $id,
        string $type,
        string $label,
        ?string $url = null,
        ?string $phone = null
    ) {
        $this->validateType($type);
        $this->validateRequiredFields($type, $url, $phone);
        
        $this->id = $id;
        $this->type = $type;
        $this->label = $label;
        $this->url = $url;
        $this->phone = $phone;
    }

    /**
     * Cria um botão do tipo URL
     */
    public static function url(string $id, string $label, string $url): self
    {
        return new self($id, 'URL', $label, $url, null);
    }

    /**
     * Cria um botão do tipo CALL
     */
    public static function call(string $id, string $label, string $phone): self
    {
        return new self($id, 'CALL', $label, null, $phone);
    }

    /**
     * Converte o botão para array (para envio na API)
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'label' => $this->label,
        ];

        if ($this->type === 'URL' && $this->url !== null) {
            $data['url'] = $this->url;
        }

        if ($this->type === 'CALL' && $this->phone !== null) {
            $data['phone'] = $this->phone;
        }

        return $data;
    }

    /**
     * Valida se o tipo é válido
     */
    protected function validateType(string $type): void
    {
        $validTypes = ['URL', 'CALL'];
        
        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException(
                "Invalid button type '{$type}'. Must be one of: " . implode(', ', $validTypes)
            );
        }
    }

    /**
     * Valida campos obrigatórios conforme o tipo
     */
    protected function validateRequiredFields(string $type, ?string $url, ?string $phone): void
    {
        if ($type === 'URL' && empty($url)) {
            throw new InvalidArgumentException(
                "Button of type 'URL' requires a 'url' parameter."
            );
        }

        if ($type === 'CALL' && empty($phone)) {
            throw new InvalidArgumentException(
                "Button of type 'CALL' requires a 'phone' parameter."
            );
        }
    }

    // Getters
    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
}
