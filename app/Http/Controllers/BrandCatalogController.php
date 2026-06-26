<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandCatalog;
use App\Models\User;
use App\Notifications\NewBrandCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BrandCatalogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $brands = Brand::orderBy('name')->get();
        $classification = $request->input('classification');
        $brandId = $request->input('brand_id');

        $search = $request->input('search');

        $query = BrandCatalog::with('brand')->latest();
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        if ($classification) {
            $query->whereHas('brand', fn($q) => $q->where('classification', $classification));
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%')
                  ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', '%' . $search . '%'));
            });
        }
        $catalogs = $query->paginate(6)->appends($request->only(['classification', 'brand_id', 'search']));
        $selectedBrand = $brandId ? Brand::find($brandId) : null;

        return view('brand-catalogs', compact('user', 'brands', 'catalogs', 'classification', 'brandId', 'selectedBrand', 'search'));
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
        $catalog = BrandCatalog::create($data);
        $catalog->load('brand');

        $currentId = Auth::id();
        User::where('id', '!=', $currentId)->get()
            ->each(fn($u) => $u->notify(new NewBrandCatalog($catalog)));

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

        $oldFile = $catalog->file_path;
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('catalogs', 'public');
        } else {
            $data['file_path'] = $catalog->file_path;
        }
        unset($data['file']);

        if (!$request->filled('link') && !$data['file_path']) {
            return back()->with('error', 'Please provide an external link or upload a file.');
        }

        $catalog->update($data);

        if ($request->hasFile('file') && $oldFile) {
            Storage::disk('public')->delete($oldFile);
        }
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
