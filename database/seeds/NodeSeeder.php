<?php

use Illuminate\Database\Seeder;

class NodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        Schema::dropIfExists('nodes');
        Schema::create('nodes', function(\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('node_id')->nullable();
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('banner')->nullable();
            $table->string('description')->nullable();
            $table->json('settings')->nullable(); // title_color/description_color
            $table->json('cache')->nullable(); // threads_count/views_count/followers_count
            $table->timestamps();
            $table->softDeletes();
        });


        $maps = [
            [
                'id'       => 1,
                'title'    => '社区',
                'cache'       => [
                    'threads_count'     => 0,
                    'subscribers_count' => 0,
                ],
                'children' => [
                    [
                        'id'      => 2,
                        'title'   => '公告',
                        'node_id' => 1,
                        'cache'   => [
                            'threads_count'     => 0,
                            'subscribers_count' => 0,
                        ]
                    ],
                    [
                        'id'      => 3,
                        'title'   => '规范',
                        'node_id' => 1,
                        'cache'   => [
                            'threads_count'     => 0,
                            'subscribers_count' => 0,
                        ]
                    ],
                    [
                        'id'      => 4,
                        'title'   => '招聘',
                        'node_id' => 1,
                        'cache'   => [
                            'threads_count'     => 0,
                            'subscribers_count' => 0,
                        ]
                    ],
                    [
                        'id'      => 5,
                        'title'   => '求职',
                        'node_id' => 1,
                        'cache'   => [
                            'threads_count'     => 0,
                            'subscribers_count' => 0,
                        ]
                    ]
                ]
            ],
            [
                'id'       => 6,
                'title'    => '分类',
                'cache'       => [
                    'threads_count'     => 0,
                    'subscribers_count' => 0,
                ],
                'children' => [
                    [
                        'id'          => 7,
                        'title'       => '教程',
                        'description' => '开发技巧、推荐扩展包等',
                        'node_id'     => 6,
                        'cache'       => [
                            'threads_count'     => 0,
                            'subscribers_count' => 0,
                        ]
                    ],
                    [
                        'id'          => 8,
                        'title'       => '分享',
                        'description' => '分享创造，分享发现',
                        'node_id'     => 6,
                        'cache'       => [
                            'threads_count'     => 0,
                            'subscribers_count' => 0,
                        ]
                    ],
                ]
            ]
        ];

        $content = \Illuminate\Support\Collection::make($maps);
        $node = app(\App\Node::class);
        foreach($content as $key => $item) {
            if(array_key_exists('children', $item)) {

                foreach($item['children'] as $key1 => $item1) {
                    $node = app(\App\Node::class);
                    if($node->where('title', $item1['title'])->count()) {
                        continue;
                    }
                    unset($item1['children']);
                    $node->create($item1);
                }
            }
            unset($item['children']);
            $node->create($item);
        }
    }
}
