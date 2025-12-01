<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToppingController extends Controller
{
    
    public function index()
    {
        $toppings = DB::table('toppings')->orderBy('tp_id', 'desc')->get();

        // Ambil semua nama file dari folder public/images
        $imageFiles = collect(glob(public_path('images/*')))->map(function ($path) {
            return basename($path);
        });

        return view('toppings.index', compact('toppings', 'imageFiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tp_name' => 'required|string|max:255|unique:toppings,tp_name',
            'tp_price' => 'required|numeric',
            'tp_stock' => 'required|integer|min:0',
            'tp_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ], [
            'tp_name.unique' => 'Topping sudah ada di daftar!',
        ]);

        $imageName = null;

        // Simpan file langsung ke public/images
        if ($request->hasFile('tp_image')) {
            $file = $request->file('tp_image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $imageName);
        }

        DB::table('toppings')->insert([
            'tp_name' => $request->tp_name,
            'tp_price' => $request->tp_price,
            'tp_stock' => $request->tp_stock,
            'tp_image' => $imageName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Topping berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'tp_name' => 'required|string|max:255|unique:toppings,tp_name,' . $id . ',tp_id',
        'tp_price' => 'required|numeric|min:0',
    ], [
        'tp_name.unique' => 'Topping sudah ada di daftar!',
    ]);
    
    

    $topping = DB::table('toppings')->where('tp_id', $id)->first();
    if (!$topping) {
        return redirect()->route('toppings.index')->with('error', 'Topping tidak ditemukan!');
    }

    $data = [
        'tp_name' => $request->tp_name,
        'tp_price' => $request->tp_price,
        'tp_stock' => $request->tp_stock,
        'updated_at' => now(),
    ];
    

    if ($request->hasFile('tp_image')) {
        $file = $request->file('tp_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images'), $filename);
        $data['tp_image'] = $filename;
    }

    DB::table('toppings')->where('tp_id', $id)->update($data);

    return redirect()->route('toppings.index')->with('success', 'Topping berhasil diperbarui!');
}


    public function destroy($id)
    {
        $topping = DB::table('toppings')->where('tp_id', $id)->first();

        if ($topping && $topping->tp_image && file_exists(public_path('images/' . $topping->tp_image))) {
            unlink(public_path('images/' . $topping->tp_image));
        }

        DB::table('toppings')->where('tp_id', $id)->delete();

        return redirect()->back()->with('success', 'Topping berhasil dihapus!');
    }
}

