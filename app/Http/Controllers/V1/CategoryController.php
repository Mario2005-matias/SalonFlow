<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponses;

    public function __construct(private CategoryService $categoryService) {}

    public function index()
    {
        return $this->success(
            'Categorias encontradas',
            CategoryResource::collection(Category::all())
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());

        return $this->success('Categoria criada com sucesso', new CategoryResource($category), 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated());

        return $this->success('Categoria atualizada com sucesso', new CategoryResource($category));
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $this->categoryService->delete($category);

        return $this->success('Categoria eliminada com sucesso');
    }
}
