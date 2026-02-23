@php
    if (!function_exists('parseSize')) {
        function parseSize($size)
        {
            $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
            $size = preg_replace('/[^0-9\.]/', '', $size);
            if ($unit)
                return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
            return round($size);
        }
    }
    if (!function_exists('formatBytes')) {
        function formatBytes($bytes, $precision = 1)
        {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
        }
    }
    if (!function_exists('fileIcon')) {
        function fileIcon($mime, $name = '')
        {
            if (!$mime)
                return '📄';
            if (str_starts_with($mime, 'image/'))
                return '🖼️';
            if (str_starts_with($mime, 'video/'))
                return '🎬';
            if (str_starts_with($mime, 'audio/'))
                return '🎵';
            if ($mime === 'application/pdf')
                return '📕';
            if (str_contains($mime, 'word') || str_contains($mime, 'document'))
                return '📝';
            if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet'))
                return '📊';
            if (str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint'))
                return '📊';
            if (str_contains($mime, 'zip') || str_contains($mime, 'rar') || str_contains($mime, 'archive'))
                return '🗜️';
            return '📄';
        }
    }
    if (!function_exists('fileColor')) {
        function fileColor($mime)
        {
            if (!$mime)
                return 'bg-gray-50 text-gray-500';
            if (str_starts_with($mime, 'image/'))
                return 'bg-purple-50 text-purple-600';
            if (str_starts_with($mime, 'video/'))
                return 'bg-pink-50 text-pink-600';
            if (str_starts_with($mime, 'audio/'))
                return 'bg-indigo-50 text-indigo-600';
            if ($mime === 'application/pdf')
                return 'bg-red-50 text-red-600';
            if (str_contains($mime, 'word') || str_contains($mime, 'document'))
                return 'bg-blue-50 text-blue-600';
            if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet'))
                return 'bg-green-50 text-green-600';
            if (str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint'))
                return 'bg-orange-50 text-orange-600';
            if (str_contains($mime, 'zip') || str_contains($mime, 'archive'))
                return 'bg-yellow-50 text-yellow-600';
            return 'bg-gray-50 text-gray-500';
        }
    }
@endphp