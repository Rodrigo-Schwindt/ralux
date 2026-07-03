<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 10pt;
        color: #1a1a1a;
        background: #ffffff;
    }

    /* ── Page structure ── */
    .page {
        padding: 28px 32px 60px 32px;
    }

    /* ── Header ── */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .header-table td {
        vertical-align: middle;
        padding: 0;
    }
    .header-logo img {
        max-height: 50px;
        max-width: 160px;
    }
    .header-logo-placeholder {
        font-size: 20pt;
        font-weight: 700;
        color: #AD0369;
        letter-spacing: 2px;
    }
    .header-right {
        text-align: right;
    }
    .header-title {
        font-size: 16pt;
        font-weight: 700;
        color: #AD0369;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .header-date {
        font-size: 8pt;
        color: #888;
        margin-top: 3px;
    }

    .divider {
        border: none;
        border-top: 2.5px solid #AD0369;
        margin: 10px 0 14px 0;
    }
    .divider-light {
        border: none;
        border-top: 1px solid #e0e0e0;
        margin: 12px 0;
    }

    /* ── Product identity ── */
    .product-identity {
        background: #f8f8f8;
        border-left: 4px solid #AD0369;
        padding: 10px 14px;
        margin-bottom: 16px;
    }
    .product-code-row {
        margin-bottom: 4px;
    }
    .product-code {
        font-size: 10pt;
        font-weight: 700;
        color: #AD0369;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .product-tipo {
        font-size: 9pt;
        color: #666;
        margin-left: 10px;
    }
    .product-description {
        font-size: 14pt;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1.3;
    }
    .product-marca {
        font-size: 9pt;
        color: #555;
        margin-top: 3px;
    }

    /* ── Images section ── */
    .images-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }
    .images-table td {
        width: 50%;
        vertical-align: middle;
        padding: 0 6px;
        text-align: center;
    }
    .image-cell {
        border: 1px solid #e5e5e5;
        border-radius: 4px;
        padding: 10px;
        background: #fafafa;
        height: 200px;
        text-align: center;
        vertical-align: middle;
    }
    .image-cell img {
        max-width: 100%;
        height: 200px;
        object-fit: contain;
    }
    .image-cell-wide {
        height: 230px;
    }
    .image-cell-wide img {
        max-width: 100%;
        max-height: 230px;
        height: auto;
        object-fit: contain;
    }
    .orientative-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        margin-bottom: 16px;
    }
    .orientative-table td {
        width: 100%;
        padding: 0;
        text-align: center;
    }
    .image-label {
        font-size: 7.5pt;
        color: #888;
        text-align: center;
        margin-top: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .image-placeholder {
        color: #ccc;
        font-size: 9pt;
        font-style: italic;
    }

    /* ── Specs table ── */
    .section-title {
        font-size: 10pt;
        font-weight: 700;
        color: #AD0369;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
        padding-bottom: 4px;
        border-bottom: 1.5px solid #AD0369;
    }
    .specs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9.5pt;
    }
    .specs-table th {
        background: #AD0369;
        color: #ffffff;
        padding: 7px 10px;
        text-align: left;
        font-weight: 700;
        font-size: 8.5pt;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .specs-table th.col-value {
        text-align: right;
    }
    .specs-table td {
        padding: 6px 10px;
        border-bottom: 1px solid #eeeeee;
        vertical-align: top;
    }
    .specs-table td.label {
        font-weight: 600;
        color: #333;
        width: 42%;
    }
    .specs-table td.value {
        color: #1a1a1a;
        text-align: right;
    }
    .specs-table tr.even td {
        background: #f9f4f7;
    }
    .specs-table tr:last-child td {
        border-bottom: none;
    }
    
    .group-block {
        margin-top: 16px;
    }
    .group-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e5e5e5;
        border-radius: 4px;
        overflow: hidden;
        font-size: 9pt;
    }
    .group-table th {
        background: #AD0369;
        color: #fff;
        padding: 7px 10px;
        text-align: left;
        font-size: 8.5pt;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .group-table td {
        padding: 7px 10px;
        border-bottom: 1px solid #eeeeee;
        vertical-align: top;
    }
    .group-table tr:last-child td {
        border-bottom: none;
    }
    .group-label {
        width: 34%;
        font-weight: 700;
        color: #333;
        background: #f9f4f7;
    }
    .group-value {
        color: #1a1a1a;
    }
    /* ── Footer ── */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 36px;
        padding: 0 32px;
        border-top: 1px solid #e0e0e0;
        background: #f8f8f8;
    }
    .footer-table {
        width: 100%;
        height: 100%;
        border-collapse: collapse;
    }
    .footer-table td {
        vertical-align: middle;
        font-size: 7.5pt;
        color: #999;
    }
    .footer-right {
        text-align: right;
    }
    .page-number:before { content: counter(page); }
    .page-total:before  { content: counter(pages); }
</style>
</head>
<body>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>Ficha Técnica — {{ $producto->codigo_ralux }}</td>
                <td class="footer-right">
                    Generado: {{ $fecha }} &nbsp;|&nbsp;
                    Pág. <span class="page-number"></span> / <span class="page-total"></span>
                </td>
            </tr>
        </table>
    </div>

    <div class="page">

        <table class="header-table">
            <tr>
                <td class="header-logo" style="width:40%">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @else
                        <span class="header-logo-placeholder">RALUX</span>
                    @endif
                </td>
                <td class="header-right" style="width:60%">
                    <div class="header-title">Ficha Técnica</div>
                </td>
            </tr>
        </table>

        <hr class="divider">

        <div class="product-identity">
            <div class="product-code-row">
                <span class="product-code">{{ $producto->codigo_ralux }}</span>
                @if($producto->tipo)
                    <span class="product-tipo">/ {{ $producto->tipo->descripcion_es }}</span>
                @endif
                @if($producto->codigo_om)
                    <span class="product-tipo">| OM: {{ $producto->codigo_om }}</span>
                @endif
            </div>
            <div class="product-description">{!! $producto->descripcion_es !!}</div>
            @if($producto->marcas->count())
                <div class="product-marca">Marcas: {{ $producto->marcas->pluck('descripcion_es')->join(' · ') }}</div>
            @endif
        </div>

        @if($imagenBase64 || $diagramaBase64 || $diagramaOrientativoBase64)
        <table class="images-table">
            <tr>
                <td style="padding-left:0; padding-right:6px;">
                    <div class="image-cell">
                        @if($imagenBase64)
                            <img src="{{ $imagenBase64 }}" alt="Imagen del producto">
                        @else
                            <span class="image-placeholder">Sin imagen</span>
                        @endif
                    </div>
                    <div class="image-label">Imagen del producto</div>
                </td>
                <td style="padding-left:6px; padding-right:0;">
                    <div class="image-cell">
                        @if($diagramaBase64)
                            <img src="{{ $diagramaBase64 }}" alt="Diagrama dimensional">
                        @else
                            <span class="image-placeholder">Sin diagrama</span>
                        @endif
                    </div>
                    <div class="image-label">Diagrama dimensional</div>
                </td>
            </tr>
        </table>
        @if($diagramaOrientativoBase64)
        <table class="orientative-table">
            <tr>
                <td>
                    <div class="image-cell image-cell-wide">
                        <img src="{{ $diagramaOrientativoBase64 }}" alt="Diagrama orientativo">
                    </div>
                    <div class="image-label">Diagrama orientativo</div>
                </td>
            </tr>
        </table>
        @endif
        @endif

        <div class="section-title">Especificaciones Técnicas</div>
        <div class="specs-wrapper">
            <table class="specs-table">
                <thead>
                    <tr>
                        <th style="width:42%">Característica</th>
                        <th class="col-value">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @php $row = 0; @endphp

                    @if($producto->marcas->count())
                    <tr class="{{ $row % 2 === 1 ? 'even' : '' }}">
                        <td class="label">Marcas compatibles</td>
                        <td class="value">{{ $producto->marcas->pluck('descripcion_es')->join(', ') }}</td>
                    </tr>
                    @php $row++; @endphp
                    @endif

                    <tr class="{{ $row % 2 === 1 ? 'even' : '' }}">
                        <td class="label">Soporte</td>
                        <td class="value">{{ $producto->soporte ? 'Sí' : 'No' }}</td>
                    </tr>
                    @php $row++; @endphp

                    @if($producto->terminales)
                    <tr class="{{ $row % 2 === 1 ? 'even' : '' }}">
                        <td class="label">Terminales</td>
                        <td class="value">{{ $producto->terminales }}T</td>
                    </tr>
                    @php $row++; @endphp
                    @endif

                    @if($producto->voltaje)
                    <tr class="{{ $row % 2 === 1 ? 'even' : '' }}">
                        <td class="label">Voltaje</td>
                        <td class="value">{{ $producto->voltaje }}V</td>
                    </tr>
                    @php $row++; @endphp
                    @endif

                    @if($producto->amperaje)
                    <tr class="{{ $row % 2 === 1 ? 'even' : '' }}">
                        <td class="label">Amperaje</td>
                        <td class="value">{{ $producto->amperaje }}A</td>
                    </tr>
                    @php $row++; @endphp
                    @endif

                    @for($i = 1; $i <= 10; $i++)
                        @if($producto->{"caract_$i"})
                        <tr class="{{ $row % 2 === 1 ? 'even' : '' }}">
                            <td class="label">{{ $producto->{"caract_$i"} }}</td>
                            <td class="value">{{ $producto->{"valor_$i"} ?? '—' }}</td>
                        </tr>
                        @php $row++; @endphp
                        @endif
                    @endfor

                    @if($producto->periodo_desde || $producto->periodo_hasta)
                    <tr class="{{ $row % 2 === 1 ? 'even' : '' }}">
                        <td class="label">Período de aplicación</td>
                        <td class="value">
                            {{ $producto->periodo_desde?->format('Y') ?? '—' }}
                            @if($producto->periodo_hasta) – {{ $producto->periodo_hasta->format('Y') }} @endif
                        </td>
                    </tr>
                    @php $row++; @endphp
                    @endif

                    @if($row === 0)
                    <tr>
                        <td colspan="2" style="text-align:center; color:#999; font-style:italic; padding:14px;">
                            Sin especificaciones técnicas cargadas.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

    
        @php
            $modelosPorMarca = $producto->modelos
                ->filter(fn($m) => $m->marca)
                ->groupBy(fn($m) => $m->marca->descripcion_es)
                ->sortKeys();

            $codigosPorMarca = $producto->codigosOM
                ->filter(fn($c) => $c->marca)
                ->groupBy(fn($c) => $c->marca->descripcion_es)
                ->sortKeys();
        @endphp

        @if($modelosPorMarca->isNotEmpty())
        <div class="group-block">
            <div class="section-title">Marcas y Modelos</div>
            <table class="group-table">
                <thead>
                    <tr>
                        <th style="width:34%">Marca</th>
                        <th>Modelos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modelosPorMarca as $marcaNombre => $modelos)
                    <tr>
                        <td class="group-label">{{ $marcaNombre }}</td>
                        <td class="group-value">{{ $modelos->pluck('descripcion_es')->unique()->join(', ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($codigosPorMarca->isNotEmpty())
        <div class="group-block">
            <div class="section-title">Codigos de Referencia Equipos Originales</div>
            <table class="group-table">
                <thead>
                    <tr>
                        <th style="width:34%">Marca</th>
                        <th>Codigos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($codigosPorMarca as $marcaNombre => $codigos)
                    <tr>
                        <td class="group-label">{{ $marcaNombre }}</td>
                        <td class="group-value">{{ $codigos->pluck('codigo')->unique()->join(', ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</body>
</html>

