{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}/</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('depootcom.blog.index') }}</loc>
        <lastmod>{{ $posts->first() ? $posts->first()->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach($posts as $post)
    <url>
        <loc>{{ route('depootcom.blog.show', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
