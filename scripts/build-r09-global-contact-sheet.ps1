param(
    [string]$EvidenceRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'evidence/R09/global-closeout/r08-three-viewport'),
    [string]$OutputPath = (Join-Path (Split-Path -Parent $PSScriptRoot) 'evidence/R09/global-closeout/r08-three-viewport/R08-THREE-VIEWPORT-FINAL-CONTACT-SHEET.png')
)

$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Drawing

$EvidenceRoot = [IO.Path]::GetFullPath($EvidenceRoot)
$OutputPath = [IO.Path]::GetFullPath($OutputPath)
$rows = @(
    'growth-invite',
    'growth-team',
    'reward-signin',
    'reward-dividend',
    'app-download'
)
$viewports = @('360x800', '390x844', '430x932')
$cellWidth = 198
$imageWidth = 180
$labelHeight = 24
$rowGap = 14
$outerPadding = 10
$font = [Drawing.Font]::new('Arial', 10, [Drawing.FontStyle]::Bold)
$brush = [Drawing.Brushes]::Black
$loaded = [Collections.Generic.List[Drawing.Bitmap]]::new()

try {
    $rowHeights = [Collections.Generic.List[int]]::new()
    foreach ($row in $rows) {
        $maxHeight = 0
        foreach ($viewport in $viewports) {
            $path = Join-Path $EvidenceRoot "$row-$viewport.png"
            if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
                throw "missing contact-sheet source: $path"
            }
            $bitmap = [Drawing.Bitmap]::new($path)
            $loaded.Add($bitmap)
            $scaledHeight = [int][Math]::Round($bitmap.Height * ($imageWidth / [double]$bitmap.Width))
            $maxHeight = [Math]::Max($maxHeight, $scaledHeight)
        }
        $rowHeights.Add($labelHeight + $maxHeight + $rowGap)
    }

    $canvasWidth = ($outerPadding * 2) + ($cellWidth * $viewports.Count)
    $canvasHeight = ($outerPadding * 2) + (($rowHeights | Measure-Object -Sum).Sum)
    $canvas = [Drawing.Bitmap]::new($canvasWidth, $canvasHeight)
    try {
        $graphics = [Drawing.Graphics]::FromImage($canvas)
        try {
            $graphics.Clear([Drawing.Color]::White)
            $graphics.InterpolationMode = [Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
            $graphics.PixelOffsetMode = [Drawing.Drawing2D.PixelOffsetMode]::HighQuality
            $bitmapIndex = 0
            $y = $outerPadding
            for ($rowIndex = 0; $rowIndex -lt $rows.Count; $rowIndex++) {
                for ($column = 0; $column -lt $viewports.Count; $column++) {
                    $bitmap = $loaded[$bitmapIndex++]
                    $x = $outerPadding + ($column * $cellWidth)
                    $label = "$($rows[$rowIndex])-$($viewports[$column]).png"
                    $graphics.DrawString($label, $font, $brush, $x, $y)
                    $scaledHeight = [int][Math]::Round($bitmap.Height * ($imageWidth / [double]$bitmap.Width))
                    $graphics.DrawImage($bitmap, $x, $y + $labelHeight, $imageWidth, $scaledHeight)
                }
                $y += $rowHeights[$rowIndex]
            }
        }
        finally {
            $graphics.Dispose()
        }

        $parent = Split-Path -Parent $OutputPath
        if (-not (Test-Path -LiteralPath $parent -PathType Container)) {
            New-Item -ItemType Directory -Path $parent -Force | Out-Null
        }
        $canvas.Save($OutputPath, [Drawing.Imaging.ImageFormat]::Png)
    }
    finally {
        $canvas.Dispose()
    }
}
finally {
    foreach ($bitmap in $loaded) { $bitmap.Dispose() }
    $font.Dispose()
}

Write-Host "[R09-CONTACT-SHEET] PASS: $OutputPath"
