<li>
    <a href="{{route('note.detail', ['noteId' => $item['id']])}}">{{ $item['name'] }}</a>
    @if (!empty($item['children']))
        <ul>
            @foreach ($item['children'] as $child)
                @include('partials.note-tree-item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>
