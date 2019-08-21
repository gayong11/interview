<div class="box box-info">
    <div class="box-header with-border">
        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a href="{{ route('admin.topics.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <blockquote>
            <p>{{ $topic->title }}</p>

            @if($topic->options->isNotEmpty())
                @foreach($topic->options as $option)
                    <div class="radio">
                        <label>
                            {{ $option->option }}：
                            {{ $option->content }}
                        </label>
                    </div>
                @endforeach
            @endif
            <p class="text-success">答案：{!! $topic->answer !!}</p>
        </blockquote>
    </div>
</div>
