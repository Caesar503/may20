<?php

namespace App\Admin\Controllers;

use App\Model\Ee;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ee);

        $grid->id('Id');
        $grid->username('公司名称');
        $grid->user('法尔奈');
        $grid->code('工商号');
        $grid->zhizhao('执照')->image();
        $grid->card_code('卡号');
        $grid->a_status('审核状态')->using(['1' => '审核中', '2' => '审核通过','3'=>'审核未通过']);

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
        $show = new Show(Ee::findOrFail($id));

        $show->id('Id');
        $show->username('Username');
        $show->user('User');
        $show->code('Code');
        $show->zhizhao('Zhizhao');
        $show->card_code('Card code');
        $show->a_status('A status');;

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Ee);

        $form->text('username', 'Username');
        $form->text('user', 'User');
        $form->text('code', 'Code');
        $form->image('zhizhao', 'Zhizhao');
        $form->text('card_code', 'Card code');
        $form->number('a_status', 'A status')->default(1);

        return $form;
    }
}
