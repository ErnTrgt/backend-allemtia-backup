<?php
// namespace App\Http\Controllers;

// use App\Models\Product;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class ProductController extends Controller
// {
//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'price' => 'required|numeric|min:0',
//             'stock' => 'required|integer|min:0',
//             'description' => 'required|string|max:255',
//         ]);

//         Product::create([
//             'user_id' => Auth::id(),
//             'name' => $request->name,
//             'price' => $request->price,
//             'stock' => $request->stock,
//             'description' => $request->description,
//         ]);

//         return redirect()->route('seller.products.index')->with('success', 'Product added successfully!');
//     }

//     public function edit($id)
//     {
//         $product = Product::where('user_id', Auth::id())->findOrFail($id);
//         return view('seller.products.edit', compact('product'));
//     }

//     public function update(Request $request, $id)
//     {
//         $product = Product::where('user_id', Auth::id())->findOrFail($id);

//         $request->validate([
//             'name' => 'required|string|max:255',
//             'price' => 'required|numeric|min:0',
//             'stock' => 'required|integer|min:0',
//         ]);

//         $product->update($request->only('name', 'price', 'stock'));

//         return redirect()->route('seller.products.index')->with('success', 'Product updated successfully!');
//     }

//     public function destroy($id)
//     {
//         $product = Product::where('user_id', Auth::id())->findOrFail($id);
//         $product->delete();

//         return redirect()->route('seller.products.index')->with('success', 'Product deleted successfully!');
//     }

//     public function toggleStatus($id)
//     {
//         $product = Product::where('user_id', Auth::id())->findOrFail($id);
//         $product->status = $product->status === 'Aktif' ? 'Pasif' : 'Aktif';
//         $product->save();

//         return redirect()->route('seller.products.index')->with('success', 'Product status updated successfully!');
//     }
// }
