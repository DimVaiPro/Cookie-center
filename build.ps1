# Build Production ZIP για το WordPress Plugin του project
# Δημιουργεί ένα zip αρχείο έτοιμο για production deployment

# Ρυθμίσεις
$pluginName = Split-Path -Leaf $PSScriptRoot # Το όνομα του φακέλου του plugin
$outputDir = ".\dist"
$zipName = "$pluginName.zip"
$tempDir = "$outputDir\$pluginName"

Write-Host "=== Building Production ZIP for $pluginName ===" -ForegroundColor Cyan

# Δημιουργία output directory αν δεν υπάρχει
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir | Out-Null
    Write-Host "Created output directory: $outputDir" -ForegroundColor Green
}

# Καθαρισμός παλιών builds
if (Test-Path $tempDir) {
    Remove-Item -Recurse -Force $tempDir
    Write-Host "Cleaned old build directory" -ForegroundColor Yellow
}

# Δημιουργία temp directory
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Λίστα φακέλων/αρχείων προς συμπερίληψη
$itemsToInclude = @(
    "admin",
    "includes",
    "public",
    "cookie-center.php",
    "README.md",
    "uninstall.php"
)

# Λίστα αρχείων/φακέλων προς εξαίρεση (patterns)
$excludePatterns = @(
    "*.log",
    "*.tmp",
    ".DS_Store",
    "Thumbs.db",
    "node_modules",
    ".git",
    ".gitignore",
    ".vscode",
    ".idea",
    "*.code-workspace"
)

Write-Host "`nCopying files..." -ForegroundColor Yellow

# Αντιγραφή κάθε item
foreach ($item in $itemsToInclude) {
    $sourcePath = ".\$item"
    
    if (Test-Path $sourcePath) {
        $destPath = "$tempDir\$item"
        
        if (Test-Path $sourcePath -PathType Container) {
            # Αντιγραφή φακέλου
            Copy-Item -Path $sourcePath -Destination $destPath -Recurse -Force
            Write-Host "  ✓ Copied folder: $item" -ForegroundColor Green
        } else {
            # Αντιγραφή αρχείου
            Copy-Item -Path $sourcePath -Destination $destPath -Force
            Write-Host "  ✓ Copied file: $item" -ForegroundColor Green
        }
    } else {
        Write-Host "  ⚠ Skipped (not found): $item" -ForegroundColor DarkYellow
    }
}

# Αφαίρεση excluded patterns
Write-Host "`nCleaning excluded files..." -ForegroundColor Yellow
foreach ($pattern in $excludePatterns) {
    $filesToRemove = Get-ChildItem -Path $tempDir -Recurse -Force -Include $pattern -ErrorAction SilentlyContinue
    foreach ($file in $filesToRemove) {
        Remove-Item -Path $file.FullName -Recurse -Force
        Write-Host "  ✗ Removed: $($file.Name)" -ForegroundColor Red
    }
}

# Δημιουργία ZIP
Write-Host "`nCreating ZIP archive..." -ForegroundColor Yellow
$zipPath = "$outputDir\$zipName"

# Αφαίρεση παλιού zip αν υπάρχει και δημιουργία νέου
if (Test-Path $zipPath) {
    Remove-Item -Force $zipPath
    Write-Host "Replaced existing ZIP" -ForegroundColor Cyan
}

# Συμπίεση
Compress-Archive -Path $tempDir -DestinationPath $zipPath -Force

# Καθαρισμός temp directory
Remove-Item -Recurse -Force $tempDir

# Πληροφορίες για το αποτέλεσμα
$zipSize = (Get-Item $zipPath).Length / 1MB
Write-Host "`n=== Build Complete ===" -ForegroundColor Green
Write-Host "Output: $zipPath" -ForegroundColor Cyan
Write-Host "Size: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Cyan
Write-Host "`nReady for production deployment! 🚀" -ForegroundColor Green
