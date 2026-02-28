<?php

namespace App\Traits;

use App\Models\VoucherTemplate;
use Illuminate\Support\Facades\File;

trait VoucherTemplateHelpers
{
    /**
     * Get system (file-based) and user (database-based) templates.
     */
    protected function getVoucherTemplates($ownerId = null)
    {
        $ownerId = $ownerId ?: auth()->id();
        
        $userTemplates = VoucherTemplate::where('user_id', $ownerId)
            ->orWhere(function($q) {
                $q->whereNull('user_id')->where('is_system', false);
            })
            ->orderBy('name')
            ->get();
        
        $systemTemplates = [];
        $templateDir = resource_path('views/vouchers/templates');
        if (File::exists($templateDir)) {
            $files = File::files($templateDir);
            foreach ($files as $file) {
                if (str_ends_with($file->getFilename(), '.blade.php') || $file->getExtension() === 'php') {
                    $filename = $file->getFilename();
                    $name = str_replace(['.blade.php', '.php'], '', $filename);
                    $systemTemplates[] = (object)[
                        'id' => 'file:' . $filename,
                        'name' => ucwords(str_replace('_', ' ', $name)),
                        'is_system' => true,
                        'is_file' => true,
                        'filename' => $filename,
                        'html_content' => File::get($file->getPathname())
                    ];
                }
            }
        }
        
        return collect($systemTemplates)->concat($userTemplates);
    }
}
