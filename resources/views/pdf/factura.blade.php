<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $pedido->numero_pedido }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }

        /* ── Header ── */
        .header { display: table; width: 100%; margin-bottom: 24px;  }
        .header-left  { display: table-cell; vertical-align: middle; width: 60%; padding-left: 10px; padding-top: 10px; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; padding-right: 10px;  }
        .brand { font-size: 22px; font-weight: bold; color: #AD0369; letter-spacing: 1px; }
        .brand-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
        .badge { display: inline-block; background: #AD0369; color: #fff; font-size: 16px; font-weight: bold;
                 padding: 6px 16px; border-radius: 4px; }
        .pedido-num { font-size: 11px; color: #64748b; margin-top: 4px; }

        /* ── Divider ── */
        hr { border: none; border-top: 2px solid #AD0369; margin: 0 0 20px; }

        /* ── Info grid (cliente + pedido) ── */
        .info-grid { display: table; width: 100%; margin-bottom: 20px; border-spacing: 12px 0; }
        .info-box { display: table-cell; width: 50%; vertical-align: top; border: 1px solid #e2e8f0;
                    border-radius: 4px; padding: 0; overflow: hidden; }
        .info-box-header { background: #1e293b; color: #fff; font-size: 10px; font-weight: bold;
                           text-transform: uppercase; letter-spacing: 0.5px; padding: 6px 10px; }
        .info-box-body { padding: 10px; }
        .info-row { margin-bottom: 6px; }
        .info-label { color: #94a3b8; font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; }
        .info-value { font-weight: bold; color: #1e293b; font-size: 11px; }

        /* ── Status badge ── */
        .status-entregado { background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .status-pendiente  { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }

        /* ── Tabla productos ── */
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
                         color: #64748b; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #e2e8f0; }
        table.productos { width: 100%; border-collapse: collapse; margin-bottom: 20px; margin-right: 24px; }
        table.productos thead tr { background: #1e293b; color: #fff; }
        table.productos th { padding: 7px 8px; font-size: 10px; text-align: left; font-weight: bold; }
        table.productos th.right { text-align: right; }
        table.productos th.center { text-align: center; }
        table.productos td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; }
        table.productos td.right { text-align: right; }
        table.productos td.center { text-align: center; }
        table.productos tbody tr:last-child td { border-bottom: none; }
        .precio-original { text-decoration: line-through; color: #94a3b8; font-size: 10px; }
        .precio-desc { color: #16a34a; font-weight: bold; }

        /* ── Totales ── */
        .totals-wrap { width: 45%; margin-left: auto; margin-bottom: 20px; padding-right: 10px; }
        .totals-wrap table { width: 100%; border-collapse: collapse; }
        .totals-wrap td { padding: 4px 0; }
        .totals-wrap .t-label { color: #64748b; }
        .totals-wrap .t-value { text-align: right; font-weight: bold; color: #1e293b; }
        .totals-wrap .t-desc { color: #16a34a; }
        .totals-wrap .t-total { font-size: 13px; font-weight: bold; border-top: 2px solid #1e293b;
                                padding-top: 6px; margin-top: 4px; }
        .totals-wrap .separator td { border-top: 1px solid #e2e8f0; padding-top: 6px; }

        /* ── Mensaje / archivo ── */
        .msg-box { background: #f8fafc; border-left: 3px solid #AD0369; padding: 8px 12px;
                   margin-bottom: 12px; border-radius: 0 4px 4px 0; }
        .msg-box .msg-title { font-size: 9px; text-transform: uppercase; color: #94a3b8; font-weight: bold; margin-bottom: 3px; }

        /* ── Footer ── */
        .footer { text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0;
                  padding-top: 10px; margin-top: 20px; }
    </style>
</head>
<body>

    {{-- ══ ENCABEZADO ══ --}}
    <div class="header">
        <div class="header-left">
            <div class="brand">RALUX</div>
            <div class="brand-sub">Distribuidora</div>
        </div>
        <div class="header-right">
            <div class="badge">FACTURA</div>
            <div class="pedido-num">Pedido #{{ $pedido->numero_pedido }}</div>
        </div>
    </div>
    <hr>

    {{-- ══ INFO CLIENTE + PEDIDO ══ --}}
    <div class="info-grid">
        <div class="info-box" style="margin-right: 8px;">
            <div class="info-box-header">Información del cliente</div>
            <div class="info-box-body">
                <div class="info-row">
                    <div class="info-label">Nombre</div>
                    <div class="info-value">{{ $pedido->cliente->nombre }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $pedido->cliente->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value">{{ $pedido->cliente->telefono }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Domicilio</div>
                    <div class="info-value">{{ $pedido->cliente->domicilio }}</div>
                </div>
                <div class="info-row" style="margin-bottom:0">
                    <div class="info-label">Localidad / Provincia</div>
                    <div class="info-value">{{ $pedido->cliente->localidad }}, {{ $pedido->cliente->provincia }}</div>
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-header">Información del pedido</div>
            <div class="info-box-body">
                <div class="info-row">
                    <div class="info-label">Nº de pedido</div>
                    <div class="info-value">{{ $pedido->numero_pedido }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de compra</div>
                    <div class="info-value">{{ $pedido->fecha_compra->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Forma de pago</div>
                    <div class="info-value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $pedido->forma_pago) }}</div>
                </div>
                @if($pedido->fecha_entrega)
                <div class="info-row">
                    <div class="info-label">Fecha estimada de entrega</div>
                    <div class="info-value">{{ $pedido->fecha_entrega->format('d/m/Y') }}</div>
                </div>
                @endif
                <div class="info-row" style="margin-bottom:0">
                    <div class="info-label">Estado</div>
                    <div class="info-value">
                        @if($pedido->entregado)
                            <span class="status-entregado">Entregado{{ $pedido->fecha_entregado ? ' · ' . $pedido->fecha_entregado->format('d/m/Y') : '' }}</span>
                        @else
                            <span class="status-pendiente">Pendiente</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ PRODUCTOS ══ --}}
    <div class="section-title">Productos</div>
    <table class="productos">
        <thead>
            <tr>
                <th style="width:12%">Código</th>
                <th>Producto</th>
                <th class="right" style="width:16%">Precio unit.</th>
                <th class="center" style="width:8%">Cant.</th>
                <th class="right" style="width:16%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->items as $item)
            <tr>
                <td>{{ $item->codigo_producto }}</td>
                <td>{!! $item->nombre_producto !!}</td>
                <td class="right">
                    @if($item->descuento_unitario > 0)
                        <span class="precio-original">${{ number_format($item->precio_unitario, 2, ',', '.') }}</span><br>
                        <span class="precio-desc">${{ number_format($item->precio_unitario - $item->descuento_unitario, 2, ',', '.') }}</span>
                    @else
                        ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                    @endif
                </td>
                <td class="center">{{ $item->cantidad }}</td>
                <td class="right">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ══ TOTALES ══ --}}
    <div class="totals-wrap">
        <table>
            <thead><tr><th style="display:none">Concepto</th><th style="display:none">Importe</th></tr></thead>
            <tbody>
            <tr>
                <td class="t-label">Subtotal sin descuento:</td>
                <td class="t-value">${{ number_format($pedido->subtotal_sin_descuento, 2, ',', '.') }}</td>
            </tr>

            @if($pedido->descuento_cliente > 0)
            @php
                $partes = array_values(array_filter([
                    (float)($pedido->porcentaje_descuento_c1 ?? 0),
                    (float)($pedido->porcentaje_descuento_c2 ?? 0),
                    (float)($pedido->porcentaje_descuento_c3 ?? 0),
                ]));
            @endphp
            <tr>
                <td class="t-label t-desc">
                    Desc. cliente{{ !empty($partes) ? ' (' . implode('%+', array_map(fn($p) => number_format($p, 0, ',', '.'), $partes)) . '%)' : '' }}:
                </td>
                <td class="t-value t-desc">-${{ number_format($pedido->descuento_cliente, 2, ',', '.') }}</td>
            </tr>
            @endif

            @if($pedido->descuento_tipo > 0)
            <tr>
                <td class="t-label t-desc">Desc. tipo de producto:</td>
                <td class="t-value t-desc">-${{ number_format($pedido->descuento_tipo, 2, ',', '.') }}</td>
            </tr>
            @endif

            @if($pedido->descuento_producto > 0)
            <tr>
                <td class="t-label t-desc">Descuento producto:</td>
                <td class="t-value t-desc">-${{ number_format($pedido->descuento_producto, 2, ',', '.') }}</td>
            </tr>
            @endif

            @if($pedido->descuento_pago > 0)
            <tr>
                <td class="t-label t-desc">
                    Desc. {{ ucfirst(str_replace('_', ' ', $pedido->forma_pago)) }} ({{ rtrim(rtrim(number_format($pedido->porcentaje_descuento_pago ?? 0, 2, '.', ''), '0'), '.') }}%):
                </td>
                <td class="t-value t-desc">-${{ number_format($pedido->descuento_pago, 2, ',', '.') }}</td>
            </tr>
            @endif

            <tr class="separator">
                <td class="t-label">Subtotal:</td>
                <td class="t-value">${{ number_format($pedido->subtotal, 2, ',', '.') }}</td>
            </tr>
            @php
                $ivaDetalle = collect($pedido->iva_detalle ?? []);
                $ivaLabel = $ivaDetalle->isNotEmpty()
                    ? \App\Support\CarritoIva::etiquetaDetalle($ivaDetalle)
                    : 'IVA ('.number_format($pedido->porcentaje_iva, 2).'%)';
            @endphp
            <tr>
                <td class="t-label">{{ $ivaLabel }}:</td>
                <td class="t-value">${{ number_format($pedido->iva, 2, ',', '.') }}</td>
            </tr>
            @foreach($ivaDetalle as $lineaIva)
            <tr>
                <td class="t-label" style="font-size: 10px; color: #666;">IVA {{ \App\Support\CarritoIva::formatearPorcentaje((float) $lineaIva['porcentaje']) }}%</td>
                <td class="t-value" style="font-size: 10px; color: #666;">${{ number_format($lineaIva['iva'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="t-total t-label">TOTAL:</td>
                <td class="t-total t-value">${{ number_format($pedido->total, 2, ',', '.') }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    {{-- ══ MENSAJE ══ --}}
    @if($pedido->mensaje)
    <div class="msg-box">
        <div class="msg-title">Comentario del cliente</div>
        <div>{{ $pedido->mensaje }}</div>
    </div>
    @endif

    {{-- ══ ARCHIVO ══ --}}
    @if($pedido->archivo_nombre)
    <div class="msg-box">
        <div class="msg-title">Archivo adjunto</div>
        <div>{{ $pedido->archivo_nombre }}</div>
    </div>
    @endif

    {{-- ══ FOOTER ══ --}}
    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }} · Pedido #{{ $pedido->numero_pedido }}
    </div>

</body>
</html>
