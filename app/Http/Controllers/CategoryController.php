<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{


    public function addcategories()
    {
        $categories =   [
            [
                'value' => "Entrepreneurship",
                'icon' => "card-heading",
                'id' => 1,
                'color' => "##117530",
                'image' => "/img/nine.png"
            ],
            [
                'value' => "Career Development",
                'icon' => "ladder",
                'id' => 2,
                'color' => "#0000A5",
                'image' => "/img/eight.png",
            ],
            [
                'value' => "Technology",
                'icon' => "cpu",
                'id' => 3,
                'color' => "#347C2C",
                'image' => "/img/six.png",
            ],
            [
                'value' => "Finance",
                'icon' => "gear-fill",
                'id' => 4,
                'color' => "#6666B0",
                'image' => "/img/one.png",
            ],
            [
                'value' => "Wellness",
                'icon' => "shuffle",
                'id' => 5,
                'color' => "#8B008B",
                'image' => "/img/five.png",
            ],
            [
                'value' => "Entertainment",
                'icon' => "speaker",
                'id' => 6,
                'color' => "#C32148",
                'image' => "/img/seven.png",
            ],
            [
                'value' => "Social Impact",
                'icon' => "speaker",
                'id' => 7,
                'color' => "#3C565B",
                'image' => "/img/ten.png",
            ]
        ];

        Category::insert($categories);
        return Category::all();
    }

    public function getcategories()
    {
        return Category::all();
    }
    public function getcategory($id)
    {
        return Category::find($id);
    }

    public function dropcategories(){
      $ids =  Category::pluck('id');
      Category::destroy($ids);
    return 'Ok';
    }
}
