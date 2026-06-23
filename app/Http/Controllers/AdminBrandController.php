<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminBrandController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $brands = Brand::withCount('catalogs')->orderBy('name')->get();
        return view('admin.brands', compact('user', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($data);
        return back()->with('success', 'Brand added.');
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);
        return back()->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->catalogs()->exists()) {
            return back()->with('error', 'Cannot delete brand with existing catalogs. Remove the catalogs first.');
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();
        return back()->with('success', 'Brand deleted.');
    }
}
