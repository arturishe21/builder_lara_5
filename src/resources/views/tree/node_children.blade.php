<ul>
    @foreach($item->children as $child)
        @if ($child->children)
            <li data-id="{{$child->id}}" data-parent-id="{{$child->parent_id}}" @if(in_array($child->id, $parentIDs))  class="jstree-open" @endif>
                {{$child->t('title')}}
                @include('admin::tree.node_children', array('item' => $child))
            </li>
        @else
            <li data-id="{{$child->id}}" data-parent-id="{{$child->parent_id}}" @if(in_array($child->id, $parentIDs)) class="jstree-open" @endif>
                @include('admin::tree.node', array('item' => $child))
            </li>
        @endif
    @endforeach
</ul>


