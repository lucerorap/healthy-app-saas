<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the request.
     */
    public function index()
    {
        return view('admin.products.index')->with([
            'products' => Product::with(['positives', 'negatives'])->latest()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddProductRequest $request)
    {
        if ($request->validated()) {
            $data = $request->validated();
            $data['image_path'] = $this->saveImage($request->file('image_path'));
            $product = Product::create($data);
            $product->qr_code_path = $this->saveProductQRCode($product->id);
            $product->save();
            $this->mergeProductImageWithQRCode($product);

            return redirect()->route('admin.products.index')->with([
                'success' => 'Product added successfully'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit')->with([
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        if ($request->validated()) {
            $data = $request->validated();
            if ($request->has('image_path')) {
                // Remove the old product image
                $this->removeProductImageFromStorage($product->image_path);

                // Save the new product image
                $data['image_path'] = $this->saveImage($request->file('image_path'));

                // Add the qr code to the new image
                $this->mergeProductImageWithQRCode($product);
            }
            $product->update($data);

            return redirect()->route('admin.products.index')->with([
                'success' => 'Product updated successfully'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Remove the product image
        $this->removeProductImageFromStorage($product->image_path);

        // Remove the qr code
        $this->removeProductImageFromStorage($product->qr_code_path);

        // Delete the product
        $product->delete();

        return redirect()->route('admin.products.index')->with([
            'success' => 'Product deleted successfully'
        ]);
    }

    /**
     * Upload and save product image
     */
    public function saveImage($file)
    {
        $image_name = time().'_'.$file->getClientOriginalName();
        $file->storeAs('images/products', $image_name, 'public');

        return 'storage/images/products'.$image_name;
    }

    /**
     * Save the product qr code
     */
    public function saveProductQRCode($productId)
    {
        // Generate the qr code
        $builder = new Builder(
            writer: new PngWriter(),
            data: $productId,
            size: 60,
        );

        $qrCode = $builder->build();

        // Define the path
        $qrCodePath = 'qr_codes/'.$productId.'.png';

        // Save the qr code
        Storage::disk('public')->put($qrCodePath, $qrCode->getString());

        // Return the file path
        return 'storage/'.$qrCodePath;
    }

    /**
     * Merge the product image with the qr code
     */
    public function mergeProductImageWithQRCode($product)
    {
        // Get the product image
        $productImagePath = $product->image_path;

        // Check the type of the product image
        if (mime_content_type($productImagePath) === 'image/png') {
            $createProductImage = imagecreatefrompng($productImagePath);
        } else if (mime_content_type($productImagePath === 'image/jpeg')) {
            $createProductImage = imagecreatefromjpeg($productImagePath);
        }

        // Get the qr code
        $qrCodePath = $product->qr_code_path;
        $qrImage = imagecreatefrompng($qrCodePath);

        // Get the dimensions of the images
        $productWidth = imagesx($createProductImage);
        $productHeight = imagesy($createProductImage);
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // Calculate the position to center the qr code in product image
        $distX = ($productWidth - $qrWidth) / 2;
        $distY = ($productHeight - $qrHeight) / 2;

        // add the qr code to the product image
        imagecopy($createProductImage, $qrImage, $distX, $distY, 0, 0, $qrWidth, $qrHeight);

        // we do not lose the background of the png images
        imagesavealpha($createProductImage, true);

        // save the created image
        imagepng($createProductImage, $product->image_path);

        // free the memory
        imagedestroy($createProductImage);
        imagedestroy($qrImage);
    }

    /**
     * Remove the product image from storage
     */
    public function removeProductImageFromStorage($file)
    {
        $path = public_path($file);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
