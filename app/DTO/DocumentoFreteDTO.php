<?php

namespace App\DTO;

readonly class DocumentoFreteDTO
{
    public function __construct(
        public ?int    $veiculo_id,
        public ?string $documento_transporte,
        public ?string $numero_documento,
        public ?string $tipo_documento,
        public ?string $data_emissao,
        public ?float  $valor_total,
        public ?float  $valor_icms,
        public ?string $municipio,
        public ?string $estado,
    )
    {}

    public static function makeFromArray(array $data): self
    {
        if($data['destino']) {
            $data['municipio'] = self::processarDestino($data['destino'])['municipio'];
            $data['estado'] = self::processarDestino($data['destino'])['estado'];
        } else {
            $data['municipio'] = 'CHAPECO';
            $data['estado'] = 'SC';
        }

        return new self(
            veiculo_id: $data['veiculo_id'] ?? null,
            documento_transporte: $data['documento_transporte'] ?? null,
            numero_documento: $data['numero_documento'] ?? null,
            tipo_documento: $data['tipo_documento'] ?? null,
            data_emissao: $data['data_emissao'] ?? null,
            valor_total: $data['valor_total'] ?? 0.0,
            valor_icms: $data['valor_icms'] ?? 0.0,
            municipio: $data['municipio'] ?? null,
            estado: $data['estado'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'veiculo_id' => $this->veiculo_id,
            'documento_transporte' => $this->documento_transporte,
            'numero_documento' => $this->numero_documento,
            'tipo_documento' => $this->tipo_documento,
            'data_emissao' => $this->data_emissao,
            'valor_total' => $this->valor_total,
            'valor_icms' => $this->valor_icms,
            'municipio' => $this->municipio,
            'estado' => $this->estado,
        ];
    }

    private static function processarDestino(string $destino): array
    {
        $parts = explode(' - ', $destino);
        $municipio = trim($parts[0] ?? '');
        $estado = trim($parts[1] ?? '');

        return [
            'municipio' => strtoupper($municipio),
            'estado' => strtoupper($estado),
        ];
    }
}
