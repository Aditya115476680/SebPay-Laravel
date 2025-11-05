<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ToppingController extends Controller
{
    public function index()
    {
        $toppings = DB::table('toppings')->orderBy('tp_id', 'desc')->get();
        return view('toppings.index', compact('toppings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tp_name' => 'required|string|max:255',
            'tp_price' => 'required|numeric',
            'tp_stock' => 'required|integer|min:0',
            'tp_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('tp_image')) {
            $imagePath = $request->file('tp_image')->store('toppings', 'public');
        }

        DB::table('toppings')->insert([
            'tp_name' => $request->tp_name,
            'tp_price' => $request->tp_price,
            'tp_stock' => $request->tp_stock,
            'tp_image' => $imagePath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Topping berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $topping = DB::table('toppings')->where('tp_id', $id)->first();
        return response()->json($topping);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tp_name' => 'required|string|max:255',
            'tp_price' => 'required|numeric',
            'tp_stock' => 'required|integer|min:0',
            'tp_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $topping = DB::table('toppings')->where('tp_id', $id)->first();
        $imagePath = $topping->tp_image;

        if ($request->hasFile('tp_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('tp_image')->store('toppings', 'public');
        }

        DB::table('toppings')->where('tp_id', $id)->update([
            'tp_name' => $request->tp_name,
            'tp_price' => $request->tp_price,
            'tp_stock' => $request->tp_stock,
            'tp_image' => $imagePath,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Topping berhasil diubah!');
    }

    public function destroy($id)
    {
        $topping = DB::table('toppings')->where('tp_id', $id)->first();

        if ($topping && $topping->tp_image && Storage::disk('public')->exists($topping->tp_image)) {
            Storage::disk('public')->delete($topping->tp_image);
        }

        DB::table('toppings')->where('tp_id', $id)->delete();

        return redirect()->back()->with('success', 'Topping berhasil dihapus!');
    }
}
