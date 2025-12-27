<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turno Disponible</title>
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
            background-color: #10B981;
            color: #ffffff;
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
        
        .alert-box {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            border-left: 4px solid #F59E0B;
            padding: 20px;
            border-radius: 4px;
            margin: 30px 0;
        }
        
        .alert-title {
            font-weight: 700;
            color: #92400E;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .alert-text {
            color: #78350F;
            font-size: 14px;
            line-height: 1.6;
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
        
        .highlight {
            color: #D4AF37;
            font-weight: 700;
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
            
            <span class="status-badge">✨ Turno Disponible</span>
            
            <p class="message">
                ¡Tenemos <span class="highlight">excelentes noticias</span>! Se liberó un turno que coincide con tu búsqueda en la lista de espera.
            </p>
            
            <!-- Appointment Details -->
            <div class="details-box">
                <div class="details-title">Detalles del Turno Disponible</div>
                
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
            </div>
            
            <!-- Alert Box -->
            <div class="alert-box">
                <div class="alert-title">⏰ Importante - Tiempo Limitado</div>
                <div class="alert-text">
                    Este turno está <strong>reservado para ti por 2 horas</strong>. Si no lo confirmas en ese tiempo, lo ofreceremos al siguiente en la lista de espera.
                </div>
            </div>
            
            <!-- CTA Button -->
            <div class="button-container">
                <a href="<?php echo e($confirmUrl); ?>" class="cta-button">Confirmar Mi Turno</a>
            </div>
            
            <div class="divider"></div>
            
            <p class="message" style="text-align: center; margin-bottom: 0;">
                Confirmá ahora para asegurar tu lugar
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                ¡No dejes pasar esta oportunidad!<br>
                Te esperamos.
            </p>
            <div class="footer-brand">STUDIO TORRES</div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\gwinazki.ELJUMILLANO\Documents\Proyectos\HernanBarber\Backend\resources\views/emails/slot-available.blade.php ENDPATH**/ ?>