<?php

namespace App\Service;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function create(array $data)
    {
        return Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);
    }

    public function update(Category $category, array $data)
    {
        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete(); 
    }
}
