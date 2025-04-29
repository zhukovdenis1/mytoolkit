@php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($products as $p)
    <url>
        <loc>{{ route('detail', ['productId' => $p->id, 'productHru' => $p->hru]) }}</loc>
        <lastmod>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $p->created_at)->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>1</priority>d
    </url>
    @endforeach
</urlset>
