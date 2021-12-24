<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function addinterests()
    {
        $interests =  [
            [

                'value' => "Marketing",
                'icon' => "basket-fill",
                'category_id' => 1,
                'color' => "#8A2BE2",
            ],
            [

                'value' => "Business Development",
                'icon' => "calendar2-event-fill",
                'category_id' => 1,
                'color' => "#307D7E",
            ],
            [

                'value' => "Accounting",
                'icon' => "suit-club-fill",
                'category_id' => 1,
                'color' => "#001414",
            ],

            [

                'value' => "Networking",
                'icon' => "signpost2",
                'category_id' => 2,
                'color' => "#7E354D",
            ],
            [

                'value' => "Upskilling",
                'icon' => "slack",
                'category_id' => 2,
                'color' => "#C32148",
            ],
            [

                'value' => "Negotiation",
                'icon' => "archive",
                'category_id' => 2,
                'color' => "#483D8B",
            ],
            [

                'value' => "Leadership",
                'icon' => "stop-btn-fill",
                'category_id' => 2,
                'color' => "#504A4B",
            ],

            [

                'value' => "Start Up",
                'icon' => "server",
                'category_id' => 3,
                'color' => "#0020C2",
            ],
            [

                'value' => "Programming",
                'icon' => "cpu",
                'category_id' => 3,
                'color' => "#347C2C",
            ],
            [

                'value' => "Design",
                'icon' => "collection-fill",
                'category_id' => 3,
                'color' => "#191970",
            ],
            [

                'value' => "Blockchain",
                'icon' => "globe",
                'category_id' => 3,
                'color' => "#728C00",
            ],
            [

                'value' => "Space",
                'icon' => "card-heading",
                'category_id' => 3,
                'color' => "#3C565B",
            ],
            [

                'value' => "Money",
                'icon' => "tags",
                'category_id' => 4,
                'color' => "#A91FFF",
            ],

            [

                'value' => "Stock",
                'icon' => "diagram2-fill",
                'category_id' => 4,
                'color' => "#3C565B",
            ],
            [

                'value' => "Real Estate",
                'icon' => "diagram2-fill",
                'category_id' => 4,
                'color' => "#3C565B",
            ],
            [

                'value' => "Crypto",
                'icon' => "diagram2-fill",
                'category_id' => 4,
                'color' => "#3C565B",
            ],
            [

                'value' => "Investment",
                'icon' => "diagram2-fill",
                'category_id' => 4,
                'color' => "#3C565B",
            ],

            [

                'value' => "Physical Fitness",
                'icon' => "diagram2-fill",
                'category_id' => 5,
                'color' => "#3C565B",
            ],
            [

                'value' => "Mental Health",
                'icon' => "diagram2-fill",
                'category_id' => 5,
                'color' => "#3C565B",
            ],
            [

                'value' => "Nutrition",
                'icon' => "diagram2-fill",
                'category_id' => 5,
                'color' => "#3C565B",
            ],
            [

                'value' => "Lifestyle",
                'icon' => "diagram2-fill",
                'category_id' => 5,
                'color' => "#3C565B",
            ],

            [

                'value' => "Betting",
                'icon' => "joystick",
                'category_id' => 6,
                'color' => "#78866B",
            ],
            [

                'value' => "Sports",
                'icon' => "globe",
                'category_id' => 6,
                'color' => "#728C00",
            ],
            [

                'value' => "Gaming",
                'icon' => "controller",
                'category_id' => 6,
                'color' => "#667C26",
            ],
            [

                'value' => "Pop Culture",
                'icon' => "speaker-fill",
                'category_id' => 6,
                'color' => "#7ecc00",
            ],
            [

                'value' => "Music",
                'icon' => "rss",
                'category_id' => 6,
                'color' => "#66AF97",
            ],

            [

                'value' => "Gender",
                'icon' => "people-fill",
                'category_id' => 7,
                'color' => "#4863A0",
            ],
            [

                'value' => "Religion",
                'icon' => "shield-plus",
                'category_id' => 7,
                'color' => "#2B3856",
            ],
            [

                'value' => "Politics",
                'icon' => "stop-btn-fill",
                'category_id' => 7,
                'color' => "#504A4B",
            ],
            [

                'value' => "Environment",
                'icon' => "life-preserver",
                'category_id' => 7,
                'color' => "#6D7B8D",
            ],
        ];

        Interest::insert($interests);
        return Interest::all();
    }
    public function getinterests($id)
    {
        $category = Category::find($id);
        return $category->interests()->get();
    }
    public function getinterest($id)
    {
        return Interest::find($id);

    }
}
