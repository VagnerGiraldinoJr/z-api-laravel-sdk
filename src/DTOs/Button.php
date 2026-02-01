<?php

namespace SuaEmpresa\ZApi\DTOs;

use InvalidArgumentException;

/**
 * Data Transfer Object para botões interativos da Z-API
 * 
 * Esta classe representa um botão de ação que pode ser enviado
 * via WhatsApp usando a Z-API. Suporta botões de URL e CALL,
 * com validação automática dos campos obrigatórios.
 * 
 * @package SuaEmpresa\ZApi\DTOs
 */
class Button
{
    /**
     * Identificador único do botão
     * 
     * @var string
     */
    protected string $id;

    /**
     * Tipo do botão (URL ou CALL)
     * 
     * @var string
     */
    protected string $type;

    /**
     * Texto exibido no botão
     * 
     * @var string
     */
    protected string $label;

    /**
     * URL de destino (obrigatório para botões URL)
     * 
     * @var string|null
     */
    protected ?string $url = null;

    /**
     * Número de telefone (obrigatório para botões CALL)
     * 
     * @var string|null
     */
    protected ?string $phone = null;

    /**
     * Construtor do Button
     * 
     * Cria um novo botão com validação dos campos obrigatórios.
     * Para facilitar a criação, use os métodos estáticos url() ou call().
     * 
     * @param string $id Identificador único do botão
     * @param string $type Tipo do botão ('URL' ou 'CALL')
     * @param string $label Texto exibido no botão
     * @param string|null $url URL de destino (obrigatório se type = 'URL')
     * @param string|null $phone Número de telefone (obrigatório se type = 'CALL')
     * 
     * @throws InvalidArgumentException Se o tipo for inválido ou campos obrigatórios estiverem faltando
     */
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
     * 
     * Factory method para criar botões que redirecionam para uma URL
     * quando clicados pelo usuário no WhatsApp.
     * 
     * @param string $id Identificador único do botão
     * @param string $label Texto exibido no botão
     * @param string $url URL de destino
     * @return self Nova instância de Button configurada como URL
     * 
     * @example
     * ```php
     * $button = Button::url('btn-offer', 'Ver Oferta', 'https://loja.com/ofertas');
     * ```
     */
    public static function url(string $id, string $label, string $url): self
    {
        return new self($id, 'URL', $label, $url, null);
    }

    /**
     * Cria um botão do tipo CALL
     * 
     * Factory method para criar botões que iniciam uma chamada telefônica
     * quando clicados pelo usuário no WhatsApp.
     * 
     * @param string $id Identificador único do botão
     * @param string $label Texto exibido no botão
     * @param string $phone Número de telefone no formato internacional (ex: 551133334444)
     * @return self Nova instância de Button configurada como CALL
     * 
     * @example
     * ```php
     * $button = Button::call('btn-support', 'Ligar para Suporte', '551133334444');
     * ```
     */
    public static function call(string $id, string $label, string $phone): self
    {
        return new self($id, 'CALL', $label, null, $phone);
    }

    /**
     * Converte o botão para array
     * 
     * Serializa o botão no formato esperado pela API Z-API,
     * incluindo apenas os campos relevantes para o tipo de botão.
     * 
     * @return array<string, string> Array associativo com os dados do botão
     * 
     * @example
     * ```php
     * $button = Button::url('btn-1', 'Click', 'https://example.com');
     * $array = $button->toArray();
     * // ['id' => 'btn-1', 'type' => 'URL', 'label' => 'Click', 'url' => 'https://example.com']
     * ```
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
     * Valida se o tipo de botão é válido
     * 
     * @param string $type Tipo a ser validado
     * @return void
     * 
     * @throws InvalidArgumentException Se o tipo não for 'URL' ou 'CALL'
     */
    protected function validateType(string $type): void
    {
        $validTypes = ['URL', 'CALL'];
        
        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException(
                "Invalid button type '{$type}'"
            );
        }
    }

    /**
     * Valida se os campos obrigatórios estão presentes conforme o tipo
     * 
     * @param string $type Tipo do botão
     * @param string|null $url URL (obrigatória para tipo URL)
     * @param string|null $phone Telefone (obrigatório para tipo CALL)
     * @return void
     * 
     * @throws InvalidArgumentException Se campos obrigatórios estiverem faltando
     */
    protected function validateRequiredFields(string $type, ?string $url, ?string $phone): void
    {
        if ($type === 'URL' && empty($url)) {
            throw new InvalidArgumentException(
                "Button of type 'URL' requires a 'url' parameter"
            );
        }

        if ($type === 'CALL' && empty($phone)) {
            throw new InvalidArgumentException(
                "Button of type 'CALL' requires a 'phone' parameter"
            );
        }
    }

    /**
     * Retorna o ID do botão
     * 
     * @return string Identificador único do botão
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Retorna o tipo do botão
     * 
     * @return string Tipo do botão ('URL' ou 'CALL')
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Retorna o label (texto) do botão
     * 
     * @return string Texto exibido no botão
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Retorna a URL do botão
     * 
     * @return string|null URL de destino ou null se não for botão URL
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Retorna o telefone do botão
     * 
     * @return string|null Número de telefone ou null se não for botão CALL
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }
}
