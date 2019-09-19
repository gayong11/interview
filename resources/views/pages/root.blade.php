@extends('layouts.app')

@section('title', '首页')

@section('content')
    <div class="row">
        <div class="col-xs-9">
            <div class="progress">
                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width: {{ floor($countTopic / $count * 100) }}%;"></div>
            </div>
        </div>
        <div class="col-xs-2 col-md-offset-1">
            <div>
                {{ $countTopic }} / {{ $count }}
            </div>
        </div>
    </div>

    @if(!empty($topic))
        <h2>{{ $topic->title }}</h2>

        @if($topic->options->isNotEmpty())
            @foreach($topic->options as $option)
                <div class="option" data-value="C">
                    {{ $option->option }}：{{ $option->content }}
                </div>
            @endforeach
        @else
            <div class="markdown-base">
                <!--输入部分-->
                <textarea class="form-control" id="markdown-area" rows="5" placeholder="分享你的见解~"></textarea>
            </div>
            <!--预览部分-->
            <div class="content-preview-area"></div>
        @endif
        <div class="row" style="margin-top: 20px; margin-bottom: 20px">
            <div class="col-xs-2 col-md-2 col-sm-2">
                <button type="button" class="btn btn-primary active look">
                    查看答案
                    <span class="glyphicon glyphicon-eye-open"></span>
                </button>
            </div>

            <div class="col-xs-2 col-md-2 col-sm-2 col-md-offset-8 col-xs-offset-6 col-sm-offset-8">
                <button type="button" class="btn btn-success active next">下一题</button>
            </div>
        </div>
        <div class="answer hidden">
            <p class="text-success">答案：{!! $topic->answer !!}</p>
        </div>
    @else
        <h1>恭喜答完全部题目了！！</h1>
        <button type="button" class="btn btn-primary active reset">重新开始</button>
    @endif
@stop

@section('scriptJs')
    <link rel="stylesheet" href="/ext/Simplemde_Markdown/dist/simplemde.min.css">
    <script src="/ext/Simplemde_Markdown/dist/simplemde.min.js"></script>
    <script src="/ext/InlineAttachment/dist/codemirror-4.inline-attachment.min.js"></script>
    <script src="/ext/markdown.min.js"></script>

    <script>
        $(function () {
            $('#markdown-area').bind('input propertychange','textarea',function(){
                var md = window.markdownit(), result = md.render($(this).val());
                if ($(this).val().length === 0) {
                    $('.content-preview-area').html(result).hide();
                } else {
                    $('.content-preview-area').html(result).show();
                }
            });

            {{-- 单选 --}}
            $('.option').click(function () {
                var value = $(this).data('value');
                $(this).addClass('selected').siblings().removeClass('selected');
            });

            var status = false;
            {{-- 查看答案 --}}
            $('.look').click(function () {
                if (status) {
                    status = false;
                    $('.look').html('查看答案 <span class="glyphicon glyphicon-eye-open"></span>');
                    $('.answer').addClass('hidden')
                } else {
                    status = true;
                    $('.look').html('隐藏答案 <span class="glyphicon glyphicon-eye-close"></span>');
                    $('.answer').removeClass('hidden')
                }
            });

            {{-- 重置 --}}
            $('.reset').click(function () {
                axios.post('{{ route('topic.reset') }}', '')
                    .then(function () {
                        location.reload();
                    });
            });

            {{-- 下一题 --}}
            $('.next').click(function () {
                axios.get('{{ route('topic.next') }}')
                    .then(function () {
                        location.reload();
                    })
            });
        });
    </script>
@endsection
