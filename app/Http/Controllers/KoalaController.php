<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KoalaController extends Controller
{
    private $basePath;

    public function __construct()
    {
        $this->basePath = base_path('koala-bertanya');
    }

    public function index()
    {
        $files = $this->scanDirectory($this->basePath);
        return view('koala.index', compact('files'));
    }

    public function show($path)
    {
        // Prevent directory traversal
        if (Str::contains($path, '..')) {
            abort(403);
        }

        $fullPath = $this->basePath . '/' . $path;

        if (!File::exists($fullPath)) {
            abort(404);
        }

        if (File::isDirectory($fullPath)) {
             $files = $this->scanDirectory($fullPath, $path);
             return view('koala.index', compact('files'));
        }

        $content = File::get($fullPath);
        
        // Simple Markdown Parsing using Laravel Str helper (if enabled) or a basic replacement for now
        // Assuming Laravel 9+ has Str::markdown(), otherwise we might need a parser.
        // Let's check if Str::markdown exists via a simple test or assume it does.
        // If not, simply wrap in <pre> for now or use a simple regex for headers.
        
        try {
            $html = Str::markdown($content);
        } catch (\Exception $e) {
            // Fallback if Str::markdown is not available or fails
            $html = '<pre>' . e($content) . '</pre>';
        }

        // Add some basic styling wrapper
        $title = Str::title(str_replace(['_', '-'], ' ', basename($path, '.md')));

        return view('koala.show', compact('html', 'title'));
    }

    private function scanDirectory($directory, $relativePath = '')
    {
        $items = [];
        $files = File::files($directory);
        $directories = File::directories($directory);

        foreach ($directories as $dir) {
            $dirname = basename($dir);
            $items[] = [
                'type' => 'folder',
                'name' => $dirname,
                'path' => $relativePath ? $relativePath . '/' . $dirname : $dirname,
                'label' => Str::title(str_replace(['_', '-'], ' ', $dirname))
            ];
        }

        foreach ($files as $file) {
            if ($file->getExtension() !== 'md') continue;
            
            $filename = $file->getFilename();
            $items[] = [
                'type' => 'file',
                'name' => $filename,
                'path' => $relativePath ? $relativePath . '/' . $filename : $filename,
                'label' => Str::title(str_replace(['_', '-'], ' ', $file->getFilenameWithoutExtension()))
            ];
        }

        return $items;
    }
}
