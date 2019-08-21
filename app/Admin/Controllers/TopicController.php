<?php

namespace App\Admin\Controllers;

use App\Models\Topic;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Validation\Rule;

class TopicController extends Controller
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
            ->header('题目列表')
            ->body($this->grid());
    }

    /**
     * 题目详情
     * @param Topic $topic
     *
     * @return Content
     */
    public function show(Topic $topic)
    {
        $topic->load('options');

        return Admin::content(function (Content $content) use ($topic) {
            $content->header('题目详情');
            $content->body(view('admin.topics.show', ['topic' => $topic]));
        });
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
            ->header('编辑题目')
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
            ->header('添加题目')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Topic);

        $grid->id('ID')->sortable();
        $grid->title('题目标题');
        $grid->type('类型')->display(function ($value) {
            return Topic::$typeMap[$value];
        });
        // 禁用导入数据
        $grid->disableExport();

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Topic);

        $form->text('title', '题目')->rules(function ($form) {
            if ($id = $form->model()->id) {
                return ['required', 'unique:topics,title,' . $id . ',id'];
            } else {
                return ['required', 'unique:topics,title'];
            }
        });
        $form->radio('type', '题目类型')
            ->options(Topic::$typeMap)
            ->default('text')
            ->rules(['required', Rule::in(array_keys(Topic::$typeMap))]);
        $form->editor('answer', '答案')->rules('required');
        $form->hasMany('options', '答案选项', function (Form\NestedForm $form) {
            $form->text('option', '选项值')->rules(['required_unless:type,text']);
            $form->text('content', '选项内容')->rules(['required_unless:type,text']);
        });

        return $form;
    }
}
