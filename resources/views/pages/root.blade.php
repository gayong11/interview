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
                <textarea
                        id="markdown-editor"
                        name="content"
                        placeholder="分享你的见解~"
                        rows="3"></textarea>
            </div>

            <div class="box preview markdown-reply fluidbox-content" id="preview-box" style="display: none;border: dashed 1px #ccc;background: #ffffff;border-radius: 6px;box-shadow:none;margin-top: 20px;margin-bottom: 6px;padding: 20px;"></div>
        @endif
        <div class="row" style="margin-top: 20px; margin-bottom: 20px">
            <div class="col-xs-2 col-md-2 col-sm-2">
                <button type="button" class="btn btn-primary active look">查看答案</button>
            </div>

            <div class="col-xs-2 col-md-2 col-sm-2 col-md-offset-8">
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
    <script src="/ext/Simplemde_Markdown/HtmlToMarkdown.js"></script>


    <script>
        $(function () {
            class Markdown{
                constructor() {
                    this.setting = {
                        'textarea': {
                            'id': 'markdown-editor',
                        },
                        'interval': true,
                        'markdown': {
                            element: document.getElementById('markdown-editor'),
                            autoDownloadFontAwesome: false,
                            spellChecker: false,
                            forceSync: true,
                            tabSize: 4,
                            toolbar: [
                                "bold", "italic", "heading", "|", "quote", "code", "table",
                                "horizontal-rule", "unordered-list", "ordered-list", "|",
                                "link", "image", "|", "side-by-side", 'fullscreen', "|",
                                {
                                    // 自定义[ 发布话题 ]
                                    name: "publish",
                                    action: function customFunction(editor) {
                                        $(editor.element).closest('form').eq(0).submit();
                                    },
                                    className: "fa fa-paper-plane",
                                    title: "{{ isset($_markdown['publish_title']) ? $_markdown['publish_title'] : '发布文章' }}",
                                },
                                {
                                    // 自定义[ 清楚缓存 ]
                                    name: "publish",
                                    action: function customFunction(editor) {
                                        localStorage.clear();
                                    },
                                    className: "fa fa-trash",
                                    title: "清除本地缓存",
                                },
                                {
                                    // 自定义标签
                                    name: "guide",
                                    action: function customFunction(editor) {
                                        var win = window.open('https://github.com/riku/Markdown-Syntax-CN/blob/master/syntax.md', '_blank');
                                        if (win) {
                                            //Browser has allowed it to be opened
                                            win.focus();
                                        } else {
                                            //Browser has blocked it
                                            alert('Please allow popups for this website');
                                        }
                                    },
                                    className: "fa fa-info-circle f_r",
                                    title: "Markdown 语法！",
                                }
                            ],
                        },
                        'uploadFieldName': 'image',
                        'jsonFieldName': 'path',
                        'events': {
                            change: function () {}
                        }
                    }
                }
                init(opt){
                    this.create($.extend(true, this.setting, opt));
                }
                create(setting){
                    var self = this;
                    if (document.getElementById(setting.textarea.id)) {
                        $(document).ready(function () {
                            self.initSimpleMDE(setting);
                        });
                    } else {
                        console.error('必须先创建好 textarea DOM节点后才可以调用 `init` 方法')
                    }
                }

                initSimpleMDE(setting){
                    var self = this;
                    var turndownService = new TurndownService();
                    setting.markdown.element = document.getElementById(setting.textarea.id);
                    var simplemde = window['markdown_' + setting.textarea.id] = new SimpleMDE(setting.markdown);

                    if(setting.interval){
                        var interval = setInterval(function () {
                            if (simplemde.isFullscreenActive()) {
                                $('.duke-pulse.editor-fullscreen').hide();
                                $(window).trigger('resize');
                                clearInterval(interval);
                            }
                        }, 1000);
                    }

                    simplemde.codemirror.on("refresh", function () {
                        $(window).trigger('resize');
                    });
                    simplemde.codemirror.on("paste", function () {
                        $(window).trigger('resize');
                    });
                    // 此处转多次是为了防止用户恶意输入
                    simplemde.codemirror.on("change", function(){
                        // markdown to html
                        var html = simplemde.markdown(simplemde.value());
                        // html to markdown
                        var markdown = turndownService.turndown(html);
                        // markdown to html
                        html = simplemde.markdown(markdown);
                        setting.events.change(html);
                    });
                }
            }



            {{-- 单选 --}}
            $('.option').click(function () {
                var value = $(this).data('value');
                $(this).addClass('selected').siblings().removeClass('selected');
            });

            {{-- 查看答案 --}}
            $('.look').click(function () {
                $('.answer').removeClass('hidden')
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


            var markdown = new Markdown();
            markdown.init({
                'textarea': {
                    'id': 'markdown-editor'
                },
                'interval': false,
                'markdown': {
                    status: false,
                    toolbar: false,
                },
                'events': {
                    change: function (html) {
                        if ($.trim(html) !== '') {
                            $("#preview-box").html(html).fadeIn();
                        } else {
                            $("#preview-box").fadeOut();
                        }
                    }
                }
            });
        });
    </script>
@endsection
