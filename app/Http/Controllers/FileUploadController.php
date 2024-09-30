<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads', 'public');

            return response()->json(['fileId' => $path, 'message' => 'File uploaded successfully']);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function eliminarImagen(Request $request)
    {
        Storage::disk('public')->delete($request->file);
        return response()->json(['message' => 'File deleted successfully']);
    }
}
