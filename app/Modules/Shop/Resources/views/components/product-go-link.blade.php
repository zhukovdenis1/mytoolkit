@php
    /*if ($product->not_found_at) {
        if ($product->search_ae) {
            $href = route('go', ['search' => $product->search_ae]);
        }
        else {
            $searchQuery = $product->title ?: $product->title_ae;
            $href = route('go', ['title' => $searchQuery]);
        }
    }
    else {
        $href = route('go', ['aid' => $product->id_ae]);
    }*/
    $href = route('go', ['aid' => $product->id_ae]);
@endphp

<a
    href="{{ $href }}"
    rel="nofollow"
    target="_blank"
    {{ $attributes->except(['product']) }}
>
    {{ $slot }}
</a>

