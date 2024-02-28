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
            'images' => 'required',
        ]);

        try {
            foreach ($request->images as $image) {
                $response_save_image = $this->save_image_public_folder($image['image'], "products/$request->product_code/images/");
                if($response_save_image['status'] == 200){
                    $product_images = new $this->model();
                    $product_images->product_code = $request->product_code;
                    $product_images->url = $response_save_image['path'];
                    $product_images->principal_image = $image['principal'];
                    $product_images->save();
                }else{
                    Log::debug(["error" => "Error al guardar imagen", "message" => $response_save_image['message'], "product_code" => $request->product_code]);
                }
            }
        } catch (Exception $error) {
            Log::debug("Error al guardar imagenes: " . $error->getMessage() . ' line: ' . $error->getLine());
            return response(["message" => "Error al guardar imagenes", "error" => $error->getMessage()], 500);
        }
       
        return response()->json(['message' => 'Imagenes de producto guardadas exitosamentes.'], 200);
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

    public function product_images($product_code)
    {
        $product_images = $this->model::where('product_code', $product_code)->get();

        return response()->json(['product_images' => $product_images], 200);
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
