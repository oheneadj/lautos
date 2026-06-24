<?php

$dir = __DIR__ . '/app/Models';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);

    // Skip if already has LogsActivity
    if (strpos($content, 'LogsActivity;') !== false && basename($file) !== 'Car.php') {
        continue;
    }

    if (basename($file) === 'Car.php') {
        continue;
    }

    // Add imports
    $importString = "use Spatie\Activitylog\Models\Concerns\LogsActivity;\nuse Spatie\Activitylog\Support\LogOptions;\n\nclass ";
    $content = preg_replace('/class\s+[A-Za-z0-9_]+\s+extends\s+Model/', "use Spatie\Activitylog\Models\Concerns\LogsActivity;\nuse Spatie\Activitylog\Support\LogOptions;\n\n$0", $content);
    
    // For User model, which extends Authenticatable
    $content = preg_replace('/class\s+[A-Za-z0-9_]+\s+extends\s+Authenticatable/', "use Spatie\Activitylog\Models\Concerns\LogsActivity;\nuse Spatie\Activitylog\Support\LogOptions;\n\n$0", $content);

    // Add LogsActivity to trait use
    // Matches "use HasFactory;" or "use HasFactory, Notifiable;"
    $content = preg_replace('/use\s+([A-Za-z0-9_,\s]+);/', "use $1, LogsActivity;", $content, 1);

    // Add getActivitylogOptions method after the trait use
    $method = "\n\n    public function getActivitylogOptions(): LogOptions\n    {\n        return LogOptions::defaults()\n            ->logFillable()\n            ->logOnlyDirty()\n            ->dontLogEmptyChanges();\n    }";
    
    $content = preg_replace('/(use\s+[A-Za-z0-9_,\s]+, LogsActivity;)/', "$1$method", $content, 1);

    file_put_contents($file, $content);
    echo "Updated " . basename($file) . "\n";
}
echo "Done!\n";
