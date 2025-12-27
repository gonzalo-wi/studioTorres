# Script para generar iconos PWA desde LogoBarber.png

$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$sourcePath = Join-Path $scriptPath "src\assets\LogoBarber.png"
$publicPath = Join-Path $scriptPath "public"

# Cargar assemblies de .NET para procesamiento de imágenes
Add-Type -AssemblyName System.Drawing

# Función para redimensionar imagen
function Resize-Image {
    param(
        [string]$inputPath,
        [string]$outputPath,
        [int]$size
    )
    
    $img = [System.Drawing.Image]::FromFile($inputPath)
    $newImg = New-Object System.Drawing.Bitmap($size, $size)
    $graphics = [System.Drawing.Graphics]::FromImage($newImg)
    
    # Configurar calidad alta
    $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $graphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $graphics.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
    
    $graphics.DrawImage($img, 0, 0, $size, $size)
    
    $newImg.Save($outputPath, [System.Drawing.Imaging.ImageFormat]::Png)
    
    $graphics.Dispose()
    $newImg.Dispose()
    $img.Dispose()
}

# Crear directorio public si no existe
if (-not (Test-Path $publicPath)) {
    New-Item -ItemType Directory -Path $publicPath
}

Write-Host "Generando iconos PWA..." -ForegroundColor Green

# Generar iconos
Resize-Image -inputPath $sourcePath -outputPath "$publicPath/pwa-192x192.png" -size 192
Write-Host "✓ pwa-192x192.png generado" -ForegroundColor Cyan

Resize-Image -inputPath $sourcePath -outputPath "$publicPath/pwa-512x512.png" -size 512
Write-Host "✓ pwa-512x512.png generado" -ForegroundColor Cyan

# Copiar también el favicon
Copy-Item $sourcePath "$publicPath/favicon.png" -Force
Write-Host "OK favicon.png copiado" -ForegroundColor Cyan

Write-Host "Iconos PWA generados exitosamente!" -ForegroundColor Green
