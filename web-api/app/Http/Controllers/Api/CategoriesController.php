<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CategoriesController extends Controller
{
    /**
     * @OA\Get(
     *     tags={"Category"},
     *     path="/api/categories",
     *     @OA\Response(response="200", description="List Categories.")
     * )
     */
    public function getList(): JsonResponse
    {
        $data = Categories::all();
        return response()->json($data)
            ->header("Content-Type", 'application/json; charset=utf-8');
    }

    /**
     * @OA\Post(
     *     tags={"Category"},
     *     path="/api/categories/create",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","image"},
     *                 @OA\Property(
     *                      property="image",
     *                      type="file",
     *                  ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Add Category.")
     * )
     */
    public function create(Request $request) : JsonResponse
    {
        $folderName =  public_path('upload');
        if (!file_exists($folderName)) {
            mkdir($folderName, 0777); // Створити папку з правами доступу 0777
        }

        $inputs = $request->all();
        $image = $request->file("image");
        $imageName = uniqid().".webp";
        $sizes = [50, 150, 300, 600, 1200];
        $manager = new ImageManager(new Driver());
        foreach($sizes as $size) {
            $fileSave = $size ."_".$imageName;
            $imageRead = $manager->read($image);
            $imageRead->scale(width: $size);
            $path = public_path('upload/'.$fileSave);
            $imageRead->toWebp()->save($path);
        }
        $inputs["image"] = $imageName;
        $category = Categories::create($inputs);

        return response()->json($category, 201)
            ->header("Content-Type", 'application/json; charset=utf-8');
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     summary="Get category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the category",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Category details"),
     *     @OA\Response(response="404", description="Category not found")
     * )
     */
    public function getById(int $id): JsonResponse
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    /**
     * @OA\Post(
     *     tags={"Category"},
     *     path="/api/categories/edit/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ідентифікатор категорії",
     *         required=true,
     *         description="ID of the category",
     *         @OA\Schema(
     *              type="number",
     *              format="int64"
     *          )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                   @OA\Property(
     *                       property="image",
     *                       type="file"
     *                   ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Add Category.")
     * )
     */
    public function edit($id, Request $request) : JsonResponse {
        $category = Categories::findOrFail($id);
        $imageName=$category->image;
        $inputs = $request->all();

        if($request->hasFile("image")) {
            $image = $request->file("image");
            $imageName = uniqid() . ".webp";
            $sizes = [50, 150, 300, 600, 1200];
            // create image manager with desired driver
            $manager = new ImageManager(new Driver());
            foreach ($sizes as $size) {
                $fileSave = $size . "_" . $imageName;
                $imageRead = $manager->read($image);
                $imageRead->scale(width: $size);
                $path = public_path('upload/' . $fileSave);
                $imageRead->toWebp()->save($path);
                $removeImage = public_path('upload/'.$size."_". $category->image);
                if(file_exists($removeImage))
                    unlink($removeImage);
            }
        }
        $inputs["image"]= $imageName;
        $category->update($inputs);
        return response()->json($category,200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }


    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     summary="Delete category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the category",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Category deleted"),
     *     @OA\Response(response="404", description="Category not found")
     * )
     */
    public function delete($id) : JsonResponse {
        $category = Categories::findOrFail($id);
        $sizes = [50,150,300,600,1200];
        foreach ($sizes as $size) {
            $fileSave = $size."_".$category->image;
            $path=public_path('upload/'.$fileSave);
            if(file_exists($path))
                unlink($path);
        }
        $category->delete();
        return response()->json("",200, ['Charset' => 'utf-8']);
    }
}
