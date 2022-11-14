<?php

namespace App\Services;

// Models
use App\Models\{MasterCategory};

class MasterCategoryService {

	/**
     * get parent categories from child
     */
	public function getParentCategoriesFromChild(int $category_id)
    {
        $category = MasterCategory::with('parent')->find($category_id);
        $collection = array();
        $collection['id'][] = $category->id;
        $collection['name'][] = $category->name;

        if (isset($category->parent->id) && !empty($category->parent->id)) {
            do {
                $collection['id'][] = $category->parent->id;
                $collection['name'][] = $category->parent->name;
                $category = MasterCategory::with('parent')->find($category->parent->id);
            } while ($category->parent()->exists());
        }

        krsort($collection['id']);
        krsort($collection['name']);

        return $collection;
    }
}