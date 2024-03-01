<?php


namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Company; 
use Illuminate\Http\Request; 


class ProductController extends Controller 
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $companyList = Company::pluck('company_name', 'id');
    
        $products = Product::query();
        if (!empty($search)) {
            $products->where('product_name', 'like', '%' . $search . '%');
        }
        if ($request->filled('company')) {
            $products->where('company_id', $request->input('company'));
        }
    
        $products = $products->paginate(10);
    
        return view('products.index', compact('products', 'companyList'));
    }

    public function create()
    {

        $companies = Company::all();

        return view('products.create', compact('companies'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'product_name' => 'required', 
            'company_id' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable',
            'img_path' => 'nullable|image',
        ]);

        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);


        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        $product->save();

        return redirect('products');
    }

    public function show(Product $product)

    {

        return view('products.show', ['product' => $product]);

    }

    public function edit(Product $product)
    {
        $companies = Company::all();

        return view('products.edit', compact('product', 'companies'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required',
            'company_id'=> 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable',
            'img_path' => 'nullable|image',
        ]);


        $product->product_name = $request->product_name;
        $product->company_id = $request->company_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->comment = $request->comment;

        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        $product->save();

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {

        $product->delete();


        return redirect('/products');

    }
}