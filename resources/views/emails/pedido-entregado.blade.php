<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pedido entregado</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 760px; margin: 0 auto; padding: 20px; }
        .header { background: #E4002B; color: white; padding: 24px 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 24px 20px; }
        .section { background: white; padding: 18px; margin-bottom: 18px; border-radius: 4px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 16px; color: #155724; margin-bottom: 18px; }
        .field { margin: 8px 0; }
        .field strong { color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 9px 8px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
        th { color: #555; background: #fafafa; }
        .right { text-align: right; }
        .footer { color: #777; font-size: 13px; text-align: center; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tu pedido fue entregado</h1>
        </div>

        <div class="content">
            <div class="success">
                Hola <strong>{{ $pedido->cliente->nombre }}</strong>, marcamos como entregado tu pedido <strong>#{{ $pedido->numero_pedido }}</strong>.
            </div>

            <div class="section">
                <div class="field"><strong>Fecha de compra:</strong> {{ $pedido->fecha_compra?->format('d/m/Y') }}</div>
                <div class="field"><strong>Fecha de entrega:</strong> {{ ($pedido->fecha_entregado ?? $pedido->fecha_entrega)?->format('d/m/Y') }}</div>
                <div class="field"><strong>Forma de pago:</strong> {{ ucfirst(str_replace('_', ' ', $pedido->forma_pago)) }}</div>
                <div class="field"><strong>Total:</strong> ${{ number_format($pedido->total, 2, ',', '.') }}</div>
            </div>

            <div class="section">
                <strong>Productos</strong>
                <table>
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Producto</th>
                            <th class="right">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->items as $item)
                        <tr>
                            <td>{{ $item->codigo_producto }}</td>
                            <td>{{ strip_tags($item->nombre_producto) }}</td>
                            <td class="right">{{ $item->cantidad }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="footer">
                Gracias por elegir Ralux. Este es un correo automatico, por favor no respondas a este mensaje.
            </div>
        </div>
    </div>
</body>
</html>
