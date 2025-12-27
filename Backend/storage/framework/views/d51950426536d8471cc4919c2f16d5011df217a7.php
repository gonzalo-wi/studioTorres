<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Turno</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 40px 30px;
            text-align: center;
            border-bottom: 4px solid #D4AF37;
        }
        
        .logo {
            font-size: 32px;
            font-weight: 700;
            color: #D4AF37;
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .header-subtitle {
            color: #ffffff;
            font-size: 14px;
            letter-spacing: 1px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 16px;
            color: #333333;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .status-badge {
            display: inline-block;
            background-color: #D4AF37;
            color: #1a1a1a;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
            margin-bottom: 30px;
        }
        
        .details-box {
            background-color: #fafafa;
            border-left: 4px solid #D4AF37;
            padding: 25px;
            margin: 30px 0;
            border-radius: 4px;
        }
        
        .details-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666666;
            min-width: 120px;
            font-size: 14px;
        }
        
        .detail-value {
            color: #1a1a1a;
            font-weight: 500;
            font-size: 14px;
        }
        
        .appointment-code {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #D4AF37;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            margin: 30px 0;
        }
        
        .appointment-code-label {
            color: #ffffff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .appointment-code-value {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 3px;
        }
        
        .info-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin: 30px 0;
        }
        
        .info-title {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .info-list {
            list-style: none;
            padding: 0;
        }
        
        .info-list li {
            color: #333333;
            padding: 8px 0 8px 20px;
            position: relative;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .info-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #D4AF37;
            font-weight: 700;
            font-size: 20px;
        }
        
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #D4AF37 0%, #F4D03F 100%);
            color: #1a1a1a;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 0.2s;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            background-color: #1a1a1a;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        
        .footer-text {
            font-size: 14px;
            color: #cccccc;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .footer-brand {
            color: #D4AF37;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 2px;
        }
        
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
            margin: 30px 0;
        }
        
        @media  only screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 28px;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">STUDIO TORRES</div>
            <div class="header-subtitle">Barbería Profesional</div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">Hola <?php echo e($clientName); ?>,</div>
            
            <span class="status-badge">Turno Confirmado</span>
            
            <p class="message">
                Tu turno ha sido confirmado exitosamente. A continuación encontrarás todos los detalles de tu reserva.
            </p>
            
            <!-- Appointment Details -->
            <div class="details-box">
                <div class="details-title">Detalles del Turno</div>
                
                <div class="detail-row">
                    <span class="detail-label">Fecha:</span>
                    <span class="detail-value"><?php echo e($date); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Hora:</span>
                    <span class="detail-value"><?php echo e($time); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Servicio:</span>
                    <span class="detail-value"><?php echo e($serviceName); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Barbero:</span>
                    <span class="detail-value"><?php echo e($barberName); ?></span>
                </div>
                
                <?php if(isset($address) && $address): ?>
                <div class="detail-row">
                    <span class="detail-label">Dirección:</span>
                    <span class="detail-value"><?php echo e($address); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Appointment Code -->
            <div class="appointment-code">
                <div class="appointment-code-label">Código de Turno</div>
                <div class="appointment-code-value"><?php echo e($appointmentCode); ?></div>
            </div>
            
            <div class="divider"></div>
            
            <!-- Important Information -->
            <div class="info-section">
                <div class="info-title">Información Importante</div>
                <ul class="info-list">
                    <li>Por favor llega 5 minutos antes de tu hora reservada.</li>
                    <li>Si necesitas cancelar o reprogramar tu turno, avísanos con 24 horas de anticipación.</li>
                    <li>Guarda tu código de turno para cualquier consulta.</li>
                </ul>
            </div>
            
            <!-- CTA Button -->
            <?php if(isset($detailsUrl) && $detailsUrl): ?>
            <div class="button-container">
                <a href="<?php echo e($detailsUrl); ?>" class="cta-button">Ver Detalles del Turno</a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                Gracias por confiar en nosotros.<br>
                ¡Te esperamos!
            </p>
            <div class="footer-brand">STUDIO TORRES</div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\gwinazki.ELJUMILLANO\Documents\Proyectos\HernanBarber\Backend\resources\views/emails/appointment-confirmed.blade.php ENDPATH**/ ?>