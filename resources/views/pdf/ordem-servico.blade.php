<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem de Serviço #{{ $ordemServico->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #93b5d8ff;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #0066cc;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .info-section {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #ffffffff;
            border-radius: 4px;
            border: 1px solid #ffffffff;
            width: 48%;
            float: left;
            margin-right: 2%;
        }

        .veiculo-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 12px;
            background-color: #ffffffff;
            border-left: 4px solid #ffffffff;
            width: 48%;
            float: right;
            margin-left: 2%;
        }

        .clearfix {
            clear: both;
        }

        .info-item {
            font-size: 11px;
            margin-bottom: 8px;
            flex: 1;
        }

        .info-item strong {
            color: #464c52ff;
            display: block;
            margin-bottom: 3px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background-color: #464c52ff;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .veiculo-item {
            text-align: center;
            flex: 1;
        }

        .veiculo-item .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .veiculo-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #464c52ff;
        }

        .table-container {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-em-andamento {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-concluido {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }

        .observacoes {
            background-color: #f8f9fa;
            padding: 12px;
            border-left: 4px solid #17a2b8;
            margin-top: 15px;
            font-size: 11px;
        }

        .observacoes h4 {
            color: #17a2b8;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        .totais {
            background-color: #e8f4fd;
            padding: 12px;
            border-radius: 4px;
            margin-top: 15px;
        }

        .totais-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .totais-item.total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #93b5d8ff;
            padding-top: 5px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ORDEM DE SERVIÇO #{{ $ordemServico->id }}</h1>
        <p>Sistema de Gestão de Frota - Relatório gerado em {{ $dataGeracao }}</p>
    </div>

    <div class="info-section">
        <div class="info-item">
            <strong>Status:</strong>
            <span class="status-badge status-{{ str_replace(' ', '-', strtolower($ordemServico->status->value)) }}">
                {{ $ordemServico->status }}
            </span>
        </div>
        <div class="info-item">
            <strong>Tipo Manutenção:</strong>
            {{ $ordemServico->tipo_manutencao }}
        </div>
        <div class="info-item">
            <strong>Data Abertura:</strong>
            {{ date('d/m/Y H:i', strtotime($ordemServico->data_inicio)) }}
            @if($ordemServico->data_fim)
                    <strong>Data Encerramento:</strong>
                    {{ date('d/m/Y H:i', strtotime($ordemServico->data_fim)) }}
            @endif
        </div>

    </div>

    <div class="veiculo-info">
        <div class="veiculo-item">
            <div class="label">Veículo</div>
            <div class="value">{{ e($ordemServico->veiculo->placa ?? 'N/A') }}</div>
        </div>
        <div class="veiculo-item">
            <div class="label">Modelo</div>
            <div class="value">{{ e($ordemServico->veiculo->modelo ?? 'N/A') }}</div>
        </div>
        <div class="veiculo-item">
            <div class="label">Quilometragem OS</div>
            <div class="value"> {{ number_format($ordemServico->quilometragem ?? 0, 0, ',', '.') }} km</div>
        </div>
        @if($ordemServico->parceiro)
        <div class="veiculo-item">
            <div class="label">Fornecedor</div>
            <div class="value">{{ e($ordemServico->parceiro->nome) }}</div>
        </div>
        @endif
    </div>

    <div class="clearfix"></div>

    <div class="section">
        <div class="section-title">Serviços Executados</div>

        @if($ordemServico->itens && $ordemServico->itens->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Código</th>
                        <th style="width: 35%;">Descrição do Serviço</th>
                        <th style="width: 10%;">Posição</th>
                        <th style="width: 25%;">Observação</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordemServico->itens as $item)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">
                            {{ e($item->servico->id ?? 'N/A') }}
                        </td>
                        <td>{{ e($item->servico->descricao ?? 'N/A') }}</td>
                        <td style="text-align: center;">{{ e($item->posicao ?? 'N/A') }}</td>
                        <td>{{ e($item->observacao ?? 'Sem observações') }}</td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ str_replace(' ', '-', strtolower($item->status->value   )) }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else
        <div class="no-data">
            <p>Nenhum serviço cadastrado para esta ordem de serviço.</p>
        </div>
        @endif
    </div>

    {{-- @if($ordemServico->agendamentos && $ordemServico->agendamentos->count() > 0)
    <div class="section">
        <div class="section-title">Agendamentos</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Data/Hora</th>
                        <th style="width: 60%;">Descrição</th>
                        <th style="width: 20%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordemServico->agendamentos as $agendamento)
                    <tr>
                        <td style="text-align: center;">
                            {{ date('d/m/Y H:i', strtotime($agendamento->data_agendamento)) }}
                        </td>
                        <td>{{ e($agendamento->descricao ?? 'Sem descrição') }}</td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ str_replace(' ', '-', strtolower($agendamento->status)) }}">
                                {{ $agendamento->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif --}}

    @if($ordemServico->planoPreventivoVinculado && $ordemServico->planoPreventivoVinculado->count() > 0)
    <div class="section">
        <div class="section-title">Planos Preventivos Vinculados</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">ID Plano</th>
                        <th style="width: 80%;">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordemServico->planoPreventivoVinculado as $planoVinculado)
                    @if($planoVinculado->planoPreventivo)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $planoVinculado->planoPreventivo->id ?? 'N/A' }}
                        </td>
                        <td>{{ e($planoVinculado->planoPreventivo->descricao ?? 'N/A') }}</td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" style="text-align: center; font-style: italic; color: #666;">
                            Plano preventivo não encontrado
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="observacoes">
        <h4>Informações Adicionais</h4>
        <p><strong>Data de Criação:</strong> {{ date('d/m/Y H:i:s', strtotime($ordemServico->created_at)) }}</p>
        @if($ordemServico->updated_at != $ordemServico->created_at)
        <p><strong>Última Atualização:</strong> {{ date('d/m/Y H:i:s', strtotime($ordemServico->updated_at)) }}</p>
        @endif
    </div>

    <div class="footer">
        <p>Sistema de Gestão de Frota - Ordem de Serviço #{{ $ordemServico->id }} - Gerado automaticamente em {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
