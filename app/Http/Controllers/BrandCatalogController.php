<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BrandCatalogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $brands = Brand::orderBy('name')->get();
        $catalogs = BrandCatalog::with('brand')->latest()->get();
        return view('brand-catalogs', compact('user', 'brands', 'catalogs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'title'    => 'required|string|max:255',
            'notes'    => 'nullable|string',
            'status'   => 'required|in:available,upcoming,seasonal',
            'link'     => 'nullable|url|max:2048',
            'file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if (!$request->filled('link') && !$request->hasFile('file')) {
            return back()->with('error', 'Please provide an external link or upload a file.');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('catalogs', 'public');
        }

        unset($data['file']);
        BrandCatalog::create($data);
        return back()->with('success', 'Catalog added.');
    }

    public function update(Request $request, BrandCatalog $catalog)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'title'    => 'required|string|max:255',
            'notes'    => 'nullable|string',
            'status'   => 'required|in:available,upcoming,seasonal',
            'link'     => 'nullable|url|max:2048',
            'file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($catalog->file_path) {
                Storage::disk('public')->delete($catalog->file_path);
            }
            $data['file_path'] = $request->file('file')->store('catalogs', 'public');
        } else {
            $data['file_path'] = $catalog->file_path;
        }

        if (!$request->filled('link') && !$data['file_path']) {
            return back()->with('error', 'Please provide an external link or upload a file.');
        }

        unset($data['file']);
        $catalog->update($data);
        return back()->with('success', 'Catalog updated.');
    }

    public function destroy(BrandCatalog $catalog)
    {
        if ($catalog->file_path) {
            Storage::disk('public')->delete($catalog->file_path);
        }
        $catalog->delete();
        return back()->with('success', 'Catalog deleted.');
    }
}
