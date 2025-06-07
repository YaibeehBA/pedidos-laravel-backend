<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Envío #{{ $data['envio']['id'] }}</title>
    <style>
        /* Estilos compatibles con DOMPDF */
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 10px;
            color: #333;
        }
        
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        
        /* Encabezado */
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
        }
        
        .logo-container {
            float: left;
            width: 20%;
        }
        
        .logo {
            max-width: 100px;
            max-height: 60px;
        }
        
        .company-info {
            float: left;
            width: 55%;
            padding-left: 10px;
        }
        
        .company-name {
            font-weight: bold;
            color: #e74c3c;
            font-size: 12pt;
            margin: 0 0 5px 0;
        }
        
        .document-info {
            float: right;
            width: 25%;
            text-align: right;
        }
        
        .document-title {
            font-weight: bold;
            color: #e74c3c;
            font-size: 11pt;
            margin: 0;
        }
        
        /* Secciones */
        .section {
            margin-bottom: 10px;
            padding: 8px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        
        .section-title {
            font-weight: bold;
            color: #e74c3c;
            font-size: 10pt;
            margin: 0 0 8px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        
        /* Columnas */
        .column-container {
            width: 100%;
        }
        
        .column {
            float: left;
            width: 48%;
            margin-right: 2%;
        }
        
        .column:last-child {
            margin-right: 0;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            font-size: 9pt;
            color: #555;
        }
        
        /* Tabla */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 9pt;
        }
        
        .product-table th {
            background: #e74c3c;
            color: white;
            padding: 5px;
            text-align: left;
        }
        
        .product-table td {
            padding: 4px;
            border-bottom: 1px solid #ddd;
        }
        
        /* Totales */
        .totals {
            float: right;
            width: 30%;
            margin-top: 10px;
        }
        
        .total-row {
            margin-bottom: 3px;
        }
        
        .grand-total {
            font-weight: bold;
            border-top: 1px solid #e74c3c;
            padding-top: 3px;
            margin-top: 3px;
        }
        
        /* Firmas */
        .signatures {
            margin-top: 20px;
        }
        
        .signature {
            width: 45%;
            float: left;
            margin-right: 5%;
            text-align: center;
            border-top: 1px solid #999;
            padding-top: 3px;
            font-size: 9pt;
        }
        
        .signature:last-child {
            margin-right: 0;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 8pt;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        /* Estados */
        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .status.pendiente { background: #fff3cd; color: #856404; }
        .status.en_proceso { background: #cce5ff; color: #004085; }
        .status.entregado { background: #d4edda; color: #155724; }
        .status.cancelado { background: #f8d7da; color: #721c24; }
        .status.completado { background: #d4edda; color: #155724; }
        .status.pagado { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header clearfix">
        <div class="logo-container">
            @if($data['empresa'] && $data['empresa']['logo'])
                <img src="{{ storage_path('app/public/' . $data['empresa']['logo']) }}" class="logo">
            @else
                <div style="font-weight:bold;color:#e74c3c;">LOGO</div>
            @endif
        </div>
        
        <div class="company-info">
            <div class="company-name">{{ $data['empresa']['nombre'] ?? 'NOMBRE EMPRESA' }}</div>
            <div>{{ $data['empresa']['direccion'] ?? 'Dirección no especificada' }}</div>
            <div>Tel: {{ $data['empresa']['telefono'] ?? 'N/A' }} | Cel: {{ $data['empresa']['celular'] ?? 'N/A' }}</div>
            <div>Email: {{ $data['empresa']['email'] ?? 'N/A' }}</div>
        </div>
        
        <div class="document-info">
            <div class="document-title">COMPROBANTE DE ENVÍO</div>
            <div>N° {{ $data['envio']['id'] }}</div>
            <div>Fecha: {{ date('d/m/Y H:i') }}</div>
        </div>
    </div>

    <!-- Sección Cliente y Envío -->
    <div class="column-container clearfix">
        <div class="column">
            <div class="section">
                <div class="section-title">DATOS DEL CLIENTE</div>
                <div class="info-item">
                    <span class="info-label">Nombre:</span>
                    {{ $data['usuario']['nombre'] }} {{ $data['usuario']['apellido'] }}
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    {{ $data['usuario']['email'] }}
                </div>
                <div class="info-item">
                    <span class="info-label">Teléfono:</span>
                    {{ $data['usuario']['celular'] }}
                </div>
            </div>
        </div>
        
        <div class="column">
            <div class="section">
                <div class="section-title">DATOS DEL ENVÍO</div>
                <div class="info-item">
                    <span class="info-label">Tipo:</span>
                    {{ $data['envio']['tipo_envio'] }}
                </div>
                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <span class="status {{ strtolower(str_replace(' ', '_', $data['envio']['estado_envio'])) }}">
                        {{ $data['envio']['estado_envio'] }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Ciudad:</span>
                    {{ $data['ciudad_destino']['nombre'] }}
                </div>
                <div class="info-item">
                    <span class="info-label">Peso:</span>
                    {{ $data['envio']['peso_total'] }} kg
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Dirección y Orden -->
    <div class="column-container clearfix">
        <div class="column">
            <div class="section">
                <div class="section-title">DIRECCIÓN DE ENVÍO</div>
                <div class="info-item">
                    <span class="info-label">Dirección:</span>
                    {{ $data['envio']['direccion'] }}
                </div>
                <div class="info-item">
                    <span class="info-label">Referencia:</span>
                    {{ $data['envio']['referencia'] }}
                </div>
            </div>
        </div>
        
        <div class="column">
            <div class="section">
                <div class="section-title">DATOS DE LA ORDEN</div>
                <div class="info-item">
                    <span class="info-label">N° Orden:</span>
                    {{ $data['orden']['id'] }}
                </div>
                <div class="info-item">
                    <span class="info-label">Estado del Pedido:</span>
                    <span class="status {{ strtolower($data['orden']['estado']) }}">
                        {{ $data['orden']['estado'] }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado del Pago:</span>
                    <span class="status {{ strtolower($data['orden']['estado_pago']) }}">
                        {{ $data['orden']['estado_pago'] }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Entrega:</span>
                    {{ date('d/m/Y', strtotime($data['orden']['fecha_entrega'])) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <div class="section">
        <div class="section-title">PRODUCTOS</div>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Cantidad</th>
                    <th>P. Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['productos'] as $producto)
                <tr>
                    <td>{{ $producto['nombre'] }}</td>
                    <td>{{ $producto['talla'] }}</td>
                    <td>{{ $producto['color'] }}</td>
                    <td>{{ $producto['cantidad'] }}</td>
                    <td>${{ number_format($producto['precio_unitario'], 2) }}</td>
                    <td>${{ number_format($producto['subtotal'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($data['orden']['monto_total'] - $data['envio']['costo_envio'], 2) }}</span>
            </div>
            <div class="total-row">
                <span>Envío:</span>
                <span>${{ number_format($data['envio']['costo_envio'], 2) }}</span>
            </div>
            @if($data['orden']['descuento_total'] > 0)
            <div class="total-row">
                <span>Descuento:</span>
                <span>-${{ number_format($data['orden']['descuento_total'], 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>${{ number_format($data['orden']['monto_total'], 2) }}</span>
            </div>
        </div>
    </div>
  <br><br><br><br><br><br><br><br><br><br> <br><br><br><br><br><br>
    <!-- Firmas -->
    <div class="signatures clearfix">
        <div class="signature">Firma del Cliente</div>
        <div class="signature">Firma del Repartidor</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Este documento es válido como comprobante de envío y venta</div>
        <div style="margin-top:100px;">
            
            <strong>Empresa:</strong> {{ $data['empresa']['nombre'] ?? 'Jaime Suquilandi Minta' }} | 
            <strong>Gerente:</strong> Jaime Suquilandi Minta |
            <strong>Teléfono:</strong> {{ $data['empresa']['telefono'] ?? '096 967 4222' }} | 
            <strong>Dirección:</strong> {{ $data['empresa']['direccion'] ?? 'Av. 9 de octubre y Bulgaria' }}
        </div>
    </div>
</body>
</html>