<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    //这是一个递归方法
    //$parentId 参数代表要获取子类目的父类目 ID,null 代表所获取的所有跟类目
    //$allCategories 参数代表数据库中的所有的类目,如果是 null 则代表需要从数据库中查询

    public function getCategoryTree($parentId = null, $allCategories = null)
    {
        if (is_null($allCategories)) {
            //从数据库中一次性取出所有类目
            $allCategories = Category::all();
        }

        return $allCategories
            //从所有类目中挑选出父类 ID 为 parentId 的类目
            ->where('parent_id', $parentId)
            ->map(function (Category $category) use ($allCategories) {
                $data = ['id' => $category->id, 'name' => $category->name];
                //如果当前类目不是父类目则直接返回
                if (!$category->is_directory) {
                    return $data;
                }

                //佛则递归调用本方法,将返回值放入 children 字段中
                $data['children'] = $this->getCategoryTree($category->id, $allCategories);

                return $data;
            });
    }
}
