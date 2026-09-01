<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RobotsController extends Controller
{
    protected function filePath(): string
    {
        return public_path('robots.txt');
    }

    // Serve robots.txt (when no static file exists, Apache serves static otherwise)
    public function index()
    {
        $content = file_exists($this->filePath())
            ? file_get_contents($this->filePath())
            : config('seo.robots_default');

        return Response::make($content, 200, ['Content-Type' => 'text/plain']);
    }

    // Admin management form
    public function edit()
    {
        $content = file_exists($this->filePath())
            ? file_get_contents($this->filePath())
            : config('seo.robots_default');

        return view('seo.robots', compact('content'));
    }

    // Save robots.txt
    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        file_put_contents($this->filePath(), $request->content);

        return redirect()->route('robots.edit')->with('success', 'robots.txt saved');
    }
}
