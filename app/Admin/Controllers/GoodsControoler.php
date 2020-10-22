<?php

namespace App\Admin\Controllers;

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
        $grid->column('price');

        $grid->column('category.name');
        $grid->column('state')->display(function($state){
            return GoodsState::getKey(intval($state));
        });

        $grid->column('created_at');
        $grid->column('updated_at');
        //设置每页显示条数
        $grid->paginate(15);
        //设置简单搜索框
        $grid->filter(function( $filter ){
             $filter->between('create_at','Create Time')->datetime();
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



        return $form;
    }
}
