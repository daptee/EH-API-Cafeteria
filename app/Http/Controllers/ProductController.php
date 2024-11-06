<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public $model = ProductImage::class;
    public $s = "imagen";
    public $sp = "imagenes";
    public $ss = "imagen/es";
    public $v = "a"; 
    public $pr = "la"; 
    public $prp = "las";

    public function store(Request $request)
    {
        $request->validate([
            'product_code' => 'required',
            'images' => 'nullable|array',
            'images.*.image' => [
                'required',
                'file',
                'max:2000',
                function ($attribute, $value, $fail) {
                    $imageInfo = getimagesize($value);
                    if ($imageInfo) {
                        $width = $imageInfo[0];
                        $height = $imageInfo[1];

                        if ($width < $height) {
                            $fail("La imagen {$attribute} debe ser de formato horizontal.");
                        }

                        if ($width > 1600) {
                            $fail("La imagen {$attribute} no debe superar los 1600 píxeles de ancho.");
                        }
                    } else {
                        $fail("El archivo {$attribute} debe ser una imagen válida.");
                    }
                }
            ],
            'images.*.principal' => 'required|boolean',
            'images.*.banner' => 'required|boolean',
            'variations' => 'nullable|array',
            'variations.*.id' => 'required_with:variations',
            'variations.*.images' => 'array',
        ], [
            'images.*.image.max' => "Cada imagen debe ser menor a 2 MB.",
            'variations.*.image.max' => "Cada imagen debe ser menor a 2 MB.",
        ]);

        try {
            if($request->images){
                $this->saveImages($request->product_code, $request->images);
            }
            // foreach ($request->images as $image) {
            //     $response_save_image = $this->save_image_public_folder($image['image'], "products/$request->product_code/images/");
            //     if($response_save_image['status'] == 200){
            //         $product_images = new $this->model();
            //         $product_images->product_code = $request->product_code;
            //         $product_images->url = $response_save_image['path'];
            //         $product_images->principal_image = $image['principal'];
            //         $product_images->banner_image = $image['banner'];
            //         $product_images->save();
            //     }else{
            //         Log::debug(["error" => "Error al guardar imagen", "message" => $response_save_image['message'], "product_code" => $request->product_code]);
            //     }
            // }
            // Guardar imágenes de las variaciones, si existen
            if ($request->has('variations')) {
                foreach ($request->variations as $variation) {
                    $this->saveImages("{$request->product_code}.{$variation['id']}", $variation['images']);
                }
            }
        } catch (Exception $error) {
            Log::debug("Error al guardar imagenes: " . $error->getMessage() . ' line: ' . $error->getLine());
            return response(["message" => "Error al guardar imagenes", "error" => $error->getMessage()], 500);
        }
       
        return response()->json(['message' => 'Imagenes de producto guardadas exitosamentes.'], 200);
    }

    private function saveImages($productCode, $images)
    {
        foreach ($images as $image) {
            $response = $this->save_image_public_folder($image['image'], "products/$productCode/images/");
            if ($response['status'] == 200) {
                $productImage = new $this->model();
                $productImage->product_code = $productCode;
                $productImage->url = $response['path'];
                $productImage->principal_image = $image['principal'] ?? null;
                $productImage->banner_image = $image['banner'] ?? null;
                $productImage->save();
            } else {
                Log::debug(["error" => "Error al guardar imagen", "message" => $response['message'], "product_code" => $productCode]);
            }
        }
    }

    public function save_image_public_folder($file, $path_to_save)
    {
        try {
            $fileName = Str::random(5) . time() . '.' . $file->extension();
            $file->move(public_path($path_to_save), $fileName);
            $path = "/" . $path_to_save . $fileName;
            return ["status" => 200, "path" => $path];
        } catch (Exception $error) {
            return ["status" => 500, "message" => $error->getMessage()];
        }
    }

    // public function product_images($product_code)
    // {
    //     $product_images = $this->model::where('product_code', $product_code)->get();

    //     return response()->json(['product_images' => $product_images], 200);
    // }

    public function product_images($product_code)
    {
        // Obtener las imágenes principales del producto
        $product_images = ProductImage::where('product_code', $product_code)->get();

        // Obtener las imágenes de variaciones que tengan un código en el formato "444.X.Y"
        $variationImages = ProductImage::where('product_code', 'like', "$product_code.%")->get()
            ->groupBy(function ($item) {
                // Extraer la parte después de '444.', por ejemplo, "0.0" o "1.0"
                return implode('.', array_slice(explode('.', $item->product_code), 1));
            })
            ->map(function ($images, $variation) {
                // Formatear cada grupo en el formato solicitado
                return [
                    'variation' => $variation,
                    'images' => $images->values()  // Asegurarse de que las imágenes están en un array numerado
                ];
            })
            ->values(); // Convertir a un array numerado

        return response()->json(['product_images' => $product_images, 'variations_images' => $variationImages], 200);
    }

    public function product_images_principal()
    {
        $products_images = $this->model::where('principal_image', 1)->get();

        return response()->json(['products_images' => $products_images], 200);
    }

    public function product_images_delete($image_id)
    {
        $product_image = $this->model::find($image_id);
        
        if(!$product_image)
            return response()->json(['message' => 'ID image invalido.'], 400);
        
        $product_image->delete();
    
        return response()->json(['message' => 'Imagen eliminada con exito.'], 200);
    }
}
