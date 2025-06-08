<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Envío #{{ $data['envio']['id'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 8mm;
            line-height: 1.2;
            color: #333;
        }
        
        .header {
            background: white;
            color: #000000;
            padding: 8pt;
            margin-bottom: 8pt;
        }
        
        .header-flex {
            display: table;
            width: 100%;
        }
        
        .logo-cell {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
        }
        
        .company-cell {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
            padding-left: 8pt;
        }
        
        .invoice-cell {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
            text-align: right;
        }
        
        .logo {
            max-width: 50pt;
            max-height: 35pt;
        }
        
        .company-name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2pt;
        }
        
        .company-info {
            font-size: 8pt;
        }
        
        .invoice-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 4pt;
        }
        
        .invoice-details {
            font-size: 8pt;
        }
        
        .info-section {
            margin-bottom: 6pt;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-spacing: 4pt;
        }
        
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .info-box {
            border: 1px solid #ddd;
            border-left: 3px solid #e74c3c;
            padding: 6pt;
            background: #f9f9f9;
            margin-bottom: 4pt;
        }
        
        .box-title {
            font-weight: bold;
            color: #e74c3c;
            font-size: 8pt;
            margin-bottom: 4pt;
            text-transform: uppercase;
        }
        
        .info-row {
            margin-bottom: 2pt;
            font-size: 8pt;
        }
        
        .label {
            font-weight: bold;
            color: #555;
            width: 30%;
            display: inline-block;
        }
        
        .value {
            color: #333;
        }
        
        .status {
            display: inline-block;
            padding: 1pt 4pt;
            border-radius: 2pt;
            font-size: 7pt;
            font-weight: bold;
        }
        
        .status.pendiente { background: #fff3cd; color: #856404; }
        .status.en_proceso { background: #d1ecf1; color: #0c5460; }
        .status.entregado { background: #d4edda; color: #155724; }
        .status.completado { background: #d4edda; color: #155724; }
        .status.cancelado { background: #f8d7da; color: #721c24; }
        .status.pagado { background: #d1ecf1; color: #0c5460; }
        
        .products {
            margin: 6pt 0;
        }
        
        .section-title {
            background: #e74c3c;
            color: white;
            padding: 4pt;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 0;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        
        .products-table th {
            background: #34495e;
            color: white;
            padding: 4pt 2pt;
            font-size: 7pt;
            text-align: center;
            border: 1px solid #333;
        }
        
        .products-table td {
            padding: 3pt 2pt;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        .products-table .product-name {
            text-align: left;
            font-weight: bold;
        }
        
        .products-table .price {
            text-align: right;
            font-weight: bold;
        }
        
        .discount-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 4pt;
            margin: 4pt 0;
            font-size: 8pt;
            text-align: center;
            color: #856404;
            font-weight: bold;
        }
        
        .totals {
            float: right;
            width: 40%;
            margin-top: 6pt;
        }
        
        .totals-table {
            width: 100%;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        
        .totals-header {
            background: #e74c3c;
            color: white;
            padding: 4pt;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
        }
        
        .totals-body {
            padding: 6pt;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 2pt;
            font-size: 8pt;
        }
        
        .total-label {
            display: table-cell;
            font-weight: bold;
            color: #555;
        }
        
        .total-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }
        
        .discount-row {
            color: #e74c3c;
        }
        
        .grand-total {
            border-top: 1px solid #e74c3c;
            padding-top: 3pt;
            margin-top: 3pt;
            font-size: 9pt;
            color: #e74c3c;
        }
        
        .signatures {
            clear: both;
            margin-top: 15pt;
            display: table;
            width: 100%;
        }
        
        .signature {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 50pt;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin: 20pt 10pt 4pt 10pt;
        }
        .signature-linearoja {
            border-top: 2px solid #FF0000; /* Rojo puro */
            margin: 20pt 10pt 4pt 10pt;
            margin-top:-5px;
        }

        
        .signature-text {
            font-size: 8pt;
            font-weight: bold;
            color: #555;
        }
        
        .footer {
            margin-top: 50pt;
            padding-top: 6pt;
            border-top: 1px solid #ddd;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }
        
        .footer-title {
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 3pt;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-flex">
            <div class="logo-cell">
                @if($data['empresa'] && $data['empresa']['logo'])
                    <img src="{{ storage_path('app/public/' . $data['empresa']['logo']) }}" class="logo">
                @else
                    <div style="border:1px solid white;padding:4pt;text-align:center;font-size:8pt;">LOGO</div>
                @endif
            </div>
            <div class="company-cell">
                <div class="company-name">{{ $data['empresa']['nombre'] ?? 'EMPRESA' }}</div>
                <div class="company-info">
                    {{ $data['empresa']['direccion'] ?? 'Dirección' }}<br>
                    Tel: {{ $data['empresa']['telefono'] ?? 'N/A' }} | Cel: {{ $data['empresa']['celular'] ?? 'N/A' }}<br>
                    Email: {{ $data['empresa']['email'] ?? 'N/A' }}
                </div>
            </div>
            <div class="invoice-cell">
                <div class="invoice-title">COMPROBANTE DE ENVÍO</div>
                <div class="invoice-details">
                    N° {{ str_pad($data['envio']['id'], 6, '0', STR_PAD_LEFT) }}<br>
                    {{ \Carbon\Carbon::parse($data['envio']['created_at'])->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
<div class="signature-linearoja" ></div>
    <!-- Información Principal -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-col">
                <div class="info-box">
                    <div class="box-title">Cliente</div>
                    <div class="info-row">
                        <span class="label">Nombre:</span>
                        <span class="value">{{ $data['usuario']['nombre'] }} {{ $data['usuario']['apellido'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Email:</span>
                        <span class="value">{{ $data['usuario']['email'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Teléfono:</span>
                        <span class="value">{{ $data['usuario']['celular'] }}</span>
                    </div>
                </div>
                
                <div class="info-box">
                    <div class="box-title">Dirección de Envío</div>
                    <div class="info-row">
                        <span class="label">Ciudad:</span>
                        <span class="value">{{ $data['ciudad_destino']['nombre'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Dirección:</span>
                        <span class="value">{{ $data['envio']['direccion'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Referencia:</span>
                        <span class="value">{{ $data['envio']['referencia'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="info-col">
                <div class="info-box">
                    <div class="box-title">Datos de Envío</div>
                    <div class="info-row">
                        <span class="label">Tipo:</span>
                        <span class="value">{{ ucfirst($data['envio']['tipo_envio']) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Estado:</span>
                        <span class="status {{ strtolower(str_replace(' ', '_', $data['envio']['estado_envio'])) }}">
                            {{ $data['envio']['estado_envio'] }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Peso:</span>
                        <span class="value">{{ number_format($data['envio']['peso_total'], 2) }} kg</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Costo:</span>
                        <span class="value">${{ number_format($data['envio']['costo_envio'], 2) }}</span>
                    </div>
                </div>
                
                <div class="info-box">
                    <div class="box-title">Datos de Orden</div>
                    <div class="info-row">
                        <span class="label">N° Orden:</span>
                        <span class="value">{{ str_pad($data['orden']['id'], 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Estado del Pedido:</span>
                        <span class="status {{ strtolower($data['orden']['estado']) }}">
                            {{ ucfirst($data['orden']['estado']) }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Pago:</span>
                        <span class="status {{ strtolower($data['orden']['estado_pago']) }}">
                            {{ ucfirst($data['orden']['estado_pago']) }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Entrega:</span>
                        <span class="value">{{ \Carbon\Carbon::parse($data['orden']['fecha_entrega'])->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Descuento aplicado -->
    @if($data['orden']['descuento_total'] > 0)
    <div class="discount-info">
         DESCUENTO APLICADO: ${{ number_format($data['orden']['descuento_total'], 2) }}
    </div>
    @endif

    <!-- Productos -->
    <div class="products">
        <div class="section-title">PRODUCTOS</div>
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width:35%;">Producto</th>
                    <th style="width:8%;">Talla</th>
                    <th style="width:12%;">Color</th>
                    <th style="width:8%;">Cant.</th>
                    <th style="width:12%;">P. Base</th>
                    @if($data['orden']['descuento_total'] > 0)
                    <th style="width:12%;">Desc.</th>
                    <th style="width:13%;">P. Final</th>
                    @else
                    <th style="width:25%;">Subtotal</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data['productos'] as $producto)
                <tr>
                    <td class="product-name">{{ $producto['nombre'] }}</td>
                    <td>{{ $producto['talla'] }}</td>
                    <td>{{ $producto['color'] }}</td>
                    <td>{{ $producto['cantidad'] }}</td>
                    <td class="price">${{ number_format($producto['precio_unitario'], 2) }}</td>
                    @if($data['orden']['descuento_total'] > 0)
                    @php
                        $descuentoUnitario = $producto['precio_unitario'] - ($producto['subtotal'] / $producto['cantidad']);
                    @endphp
                    <td class="price" style="color:#e74c3c;">
                        @if($descuentoUnitario > 0)
                            -${{ number_format($descuentoUnitario, 2) }}
                        @else
                            $0.00
                        @endif
                    </td>
                    <td class="price">${{ number_format($producto['subtotal'] / $producto['cantidad'], 2) }}</td>
                    @else
                    <td class="price">${{ number_format($producto['subtotal'], 2) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div class="totals">
        <div class="totals-table">
            <div class="totals-header">TOTALES</div>
            <div class="totals-body">
                @php
                    $subtotalSinDescuento = collect($data['productos'])->sum('subtotal') + $data['orden']['descuento_total'];
                @endphp
                
                <div class="total-row">
                    <div class="total-label">Subtotal:</div>
                    <div class="total-value">${{ number_format($subtotalSinDescuento, 2) }}</div>
                </div>
                
                @if($data['orden']['descuento_total'] > 0)
                <div class="total-row discount-row">
                    <div class="total-label">Descuento:</div>
                    <div class="total-value">-${{ number_format($data['orden']['descuento_total'], 2) }}</div>
                </div>
                <div class="total-row">
                    <div class="total-label">Subtotal c/desc:</div>
                    <div class="total-value">${{ number_format(collect($data['productos'])->sum('subtotal'), 2) }}</div>
                </div>
                @endif
                
                <div class="total-row">
                    <div class="total-label">Envío:</div>
                    <div class="total-value">
                        @if($data['envio']['costo_envio'] > 0)
                            ${{ number_format($data['envio']['costo_envio'], 2) }}
                        @else
                            GRATIS
                        @endif
                    </div>
                </div>
                
                <div class="total-row grand-total">
                    <div class="total-label">TOTAL:</div>
                    <div class="total-value">${{ number_format($data['orden']['monto_total'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Firmas -->
    <div class="signatures">
        <div class="signature">
            <div class="signature-line"></div>
            <div class="signature-text">Firma del Cliente</div>
        </div>
        <div class="signature">
            <div class="signature-line"></div>
            <div class="signature-text">Firma del Repartidor</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-title">Este documento es válido como comprobante de envío y venta</div>
        <div>
            <strong>{{ $data['empresa']['nombre'] ?? 'Empresa' }}</strong> | 
            Tel: {{ $data['empresa']['telefono'] ?? 'N/A' }} | 
            Dir: {{ $data['empresa']['direccion'] ?? 'N/A' }}
        </div>
    </div>
</body>
</html>