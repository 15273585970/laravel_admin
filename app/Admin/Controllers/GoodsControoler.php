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
        $form->text('name','goods Name');
        $form->number('price','line Price');
        $form->text('category.name','category Name');
        $form->text('state','state');

        $form->switch('recommend','status');
        $form->display('created_at','create Time');
        $form->display('updated_at','update Time');
        $form->tools(function(Form\Tools $tools){
            //去掉列表按钮
            $tools->disableList();
            //去掉删除按钮
            $tools->disableDelete();
            //去掉查看按钮
            $tools->disableView();
            $tools->add("<a class=\"btn btn-sm btn-danger\"><i class=\"fa fa-trash\"></i>&nbsp;&nbsp;delete</a>");
        });

        $form->footer(function($tooter){
           //去掉重置按钮
            $tooter->disableReset();
            //去掉提交按钮
          //  $tooter->disableSubmit();
            //去掉 继续编辑 checkbox
            $tooter->disableEditingCheck();
            // 去掉 继续创建 checkboox
            $tooter->disableCreatingCheck();
        });


//        //忽略不需要保存的字段
//        $form->ignore(['name']);

        return $form;
    }
}
