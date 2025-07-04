@php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($articles as $a)
        <url>
            <loc>{{ route('article.detail', ['article' => $a, 'articleHru' => $a->uri]) }}</loc>
            <lastmod>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $a->created_at)->format('Y-m-d') }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>1</priority>
        </url>
    @endforeach
    @foreach ($products as $p)
    <url>
        <loc>{{ route('detail', ['product' => $p, 'productHru' => $p->hru]) }}</loc>
        <lastmod>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $p->created_at)->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>1</priority>
    </url>
    @endforeach
</urlset>
