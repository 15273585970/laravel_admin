<?php

namespace App\Admin\Controllers;

use App\Enums\GoodsRecommend;
use App\Enums\GoodsState;
use App\Models\GoodsCategory;
use App\Models\Goods;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class GoodsControoler extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Goods';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Goods());

        $grid->column('id',"ID")->sortable();
        $grid->column('name')->editable();

        $grid->column('line_price');
//        $grid->column('price');

        $grid->column('category.name');
        $grid->column('state')->display(function($state){
            return GoodsState::getKey(intval($state));
        });


        $states = [
            'on'  => ['value' => 1001, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 1002, 'text' => '关闭', 'color' => 'default'],
        ];

        $grid->column('recommend')->display(function($state){
            if ( !$state ) return GoodsRecommend::getKey(intval($state));
            else return '该商品暂无设置';
        })->switch($states);



//        $grid->column('created_at');
        $grid->column('updated_at');
        //设置每页显示条数
        $grid->paginate(15);


        //价格范围查询
        $grid->column('price','price')->filter('range');
//        $grid->column('price')->editable('textarea');
        $grid->column('birth')->editable('date');
        //时间范围查询
        $grid->column('created_at','created_at')->filter('range','datetime');
//        $grid->column('name')->editable('select', [1 => 'option1', 2 => 'option2', 3 => 'option3']);
        //设置简单搜索框
        $grid->filter(function( $filter ){

             //去掉默认的id过滤器
             $filter->disableIdFilter();
             $filter->like('name','Goods Name');
             //分类查询
             $filter->like('category.name','Category Name');
             $filter->between('created_at','Create Time')->datetime();

//             $filter->where(function($query){
//                 $query->where('name','like',"%{$this->input}%")
//                 ->orWhere('price','like',"%{$this->input}%");
//             },'Text');



        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Goods::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Goods());

        $form->column('status')->switch();
        $states = [
            'on'  => ['value' => 1001, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 1002, 'text' => '关闭', 'color' => 'default'],
        ];
        $form->column('recommend')->switch($states);
        return $form;
    }
}
