<ol class="dd-list">
    @foreach ($items as $item)
        <li class="dd-item" data-id="{{ $item->id }}">
            <div class="pull-right item_actions" style="margin-top: 12px;margin-right: 5px">
                @if(Auth::user()->can('auto_reply.delete'))
                    <form style="display: inline-block" method="post" class="pull-right" action="{{url('auto-reply/'.$item->id)}}">
                        {{csrf_field()}}
                        {{method_field('DELETE')}}
                        <div class="btn-sm btn-danger confirm-form" style="cursor: pointer" data-id="{{ $item->id }}">
                            <i class="entypo-trash"></i>
                        </div>
                    </form>
                @endif
                @if(Auth::user()->can('auto_reply.update'))
                    <div class="btn-sm btn-info pull-right item-edit" style="margin-right: 5px;cursor: pointer" data-id="{{ $item->id }}">
                        <i class="entypo-pencil"></i>
                    </div>
                @endif
            </div>
            <div class="dd-handle">
                @if($item->keyword == "")
                    <i class="entypo-rss"></i>
                @else
                    <span>{{ $item->keyword }}</span>
                @endif
                |
                <small>{{ $item->source }}</small>
                <i class="entypo-forward"></i>
                <small>{{ $item->did->did }}</small>
                |
                <small class="hidden-sm">{{ str_limit($item->text, 30) }}</small>
            </div>
            @if($item->children->isNotEmpty())
                @include('partials.auto_reply', ['items' => $item->children])
            @endif
        </li>
    @endforeach
</ol>