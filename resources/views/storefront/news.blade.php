<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức – LensStore | Kiến thức nhiếp ảnh & ống kính</title>
    <meta name="description" content="Cập nhật tin tức, kiến thức nhiếp ảnh, review ống kính mới nhất tại LensStore.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#4f46e5;--primary-hover:#4338ca;--accent:#f59e0b;--dark:#0f172a;--surface:#fff;--surface2:#f8fafc;--text-main:#1e293b;--text-muted:#64748b;--border:#e2e8f0;--radius:16px;--shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -2px rgba(0,0,0,.1);}
        *{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{font-family:'Inter',sans-serif;background:var(--surface2);color:var(--text-main);line-height:1.6;-webkit-font-smoothing:antialiased;}
        a{text-decoration:none;color:inherit;}

        /* PAGE HERO */
        .page-hero{background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#312e81 100%);color:white;padding:5rem 2.5rem;text-align:center;position:relative;overflow:hidden;}
        .page-hero::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1452780212461-0b3ee3e2cc2d?w=1600&auto=format&fit=crop&q=80') center/cover;opacity:0.1;}
        .page-hero-inner{max-width:720px;margin:0 auto;position:relative;z-index:1;}
        .page-hero-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);padding:6px 14px;border-radius:50px;font-size:0.82rem;font-weight:500;margin-bottom:1.5rem;}
        .page-hero h1{font-size:3rem;font-weight:900;letter-spacing:-1.5px;margin-bottom:1rem;}
        .page-hero h1 span{color:var(--accent);}
        .page-hero p{color:rgba(255,255,255,.75);font-size:1.05rem;line-height:1.8;}

        /* FEATURED ARTICLE */
        .featured-wrap{max-width:1280px;margin:3rem auto 0;padding:0 2.5rem;}
        .featured-article{display:grid;grid-template-columns:1.2fr 1fr;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);background:white;border:1px solid var(--border);}
        .featured-img{height:420px;object-fit:cover;width:100%;}
        .featured-content{padding:3rem;display:flex;flex-direction:column;justify-content:center;}
        .tag{display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:700;padding:4px 12px;border-radius:6px;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:1rem;}
        .tag-review{background:#ede9fe;color:#6d28d9;}
        .tag-news{background:#fef3c7;color:#92400e;}
        .tag-tips{background:#d1fae5;color:#065f46;}
        .tag-compare{background:#dbeafe;color:#1d4ed8;}
        .featured-content h2{font-size:1.8rem;font-weight:800;letter-spacing:-0.5px;line-height:1.3;margin-bottom:1rem;}
        .featured-content p{color:var(--text-muted);line-height:1.8;margin-bottom:1.5rem;}
        .article-meta{display:flex;align-items:center;gap:1.25rem;font-size:0.82rem;color:var(--text-muted);margin-bottom:1.5rem;}
        .article-meta span{display:flex;align-items:center;gap:5px;}
        .btn-read{display:inline-flex;align-items:center;gap:8px;background:var(--primary);color:white;padding:0.75rem 1.5rem;border-radius:10px;font-weight:700;font-size:0.9rem;transition:all 0.3s;}
        .btn-read:hover{background:var(--primary-hover);transform:translateY(-2px);}

        /* TABS */
        .tabs{display:flex;gap:0.5rem;overflow-x:auto;padding-bottom:4px;}
        .tab{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:50px;font-size:0.88rem;font-weight:600;border:1px solid var(--border);background:white;color:var(--text-muted);cursor:pointer;white-space:nowrap;transition:all 0.2s;font-family:inherit;}
        .tab.active,.tab:hover{background:var(--primary);color:white;border-color:var(--primary);}

        /* ARTICLES GRID */
        .articles-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;transition:opacity 0.3s;}
        .article-card{background:white;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;transition:all 0.3s;}
        .article-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(79,70,229,.1);border-color:var(--primary);}
        .article-img{width:100%;height:200px;object-fit:cover;transition:transform 0.4s;}
        .article-card:hover .article-img{transform:scale(1.03);}
        .article-img-wrap{overflow:hidden;position:relative;}
        .article-tag-overlay{position:absolute;top:12px;left:12px;}
        .article-body{padding:1.25rem;}
        .article-body h3{font-size:1.05rem;font-weight:700;line-height:1.4;margin-bottom:0.75rem;color:var(--text-main);transition:color 0.2s;}
        .article-card:hover .article-body h3{color:var(--primary);}
        .article-body p{color:var(--text-muted);font-size:0.88rem;line-height:1.7;margin-bottom:1rem;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
        .article-footer{display:flex;justify-content:space-between;align-items:center;font-size:0.8rem;color:var(--text-muted);}
        .article-author{display:flex;align-items:center;gap:8px;}
        .author-avatar{width:28px;height:28px;border-radius:50%;background:var(--primary);color:white;font-weight:700;font-size:0.7rem;display:flex;align-items:center;justify-content:center;}
        .read-more-link{color:var(--primary);font-weight:600;font-size:0.82rem;display:flex;align-items:center;gap:4px;}

        /* NEWSLETTER */
        .newsletter{background:linear-gradient(135deg,var(--primary),#6366f1);color:white;padding:4rem 2.5rem;text-align:center;}
        .newsletter-inner{max-width:560px;margin:0 auto;}
        .newsletter h2{font-size:2rem;font-weight:800;margin-bottom:0.75rem;letter-spacing:-0.5px;}
        .newsletter p{color:rgba(255,255,255,.8);margin-bottom:2rem;line-height:1.8;}
        .newsletter-form{display:flex;gap:0.75rem;max-width:440px;margin:0 auto;}
        .newsletter-input{flex:1;padding:0.9rem 1.2rem;border-radius:10px;border:none;font-size:0.95rem;outline:none;}
        .newsletter-btn{background:var(--accent);color:var(--dark);padding:0.9rem 1.5rem;border-radius:10px;border:none;font-weight:700;cursor:pointer;font-size:0.9rem;white-space:nowrap;transition:all 0.2s;}
        .newsletter-btn:hover{background:#e8ab00;}

        /* SIDEBAR WIDGET */
        .main-sidebar-layout{max-width:1280px;margin:2rem auto;padding:0 2.5rem;display:grid;grid-template-columns:1fr 320px;gap:2rem;}
        .sidebar-widget{background:white;border-radius:var(--radius);border:1px solid var(--border);padding:1.5rem;margin-bottom:1.25rem;}
        .widget-title{font-size:1rem;font-weight:700;border-bottom:2px solid var(--primary);padding-bottom:0.75rem;margin-bottom:1rem;color:var(--text-main);}
        .trending-item{display:flex;gap:12px;align-items:flex-start;padding:10px 0;border-bottom:1px solid var(--surface2);}
        .trending-num{font-size:1.4rem;font-weight:900;color:#e2e8f0;width:30px;flex-shrink:0;line-height:1.2;}
        .trending-item:last-child{border-bottom:none;}
        .trending-title{font-size:0.87rem;font-weight:600;line-height:1.4;color:var(--text-main);}
        .trending-title:hover{color:var(--primary);}
        .trending-meta{font-size:0.75rem;color:var(--text-muted);margin-top:3px;}

        /* FOOTER */
        .footer{background:var(--dark);color:rgba(255,255,255,0.7);padding:3rem 2.5rem 1.5rem;margin-top:4rem;}
        .footer-inner{max-width:1280px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;}
        .footer-logo{font-size:1.3rem;font-weight:800;color:white;}
        .footer-links{display:flex;gap:1.5rem;flex-wrap:wrap;}
        .footer-links a{color:rgba(255,255,255,0.6);font-size:0.88rem;transition:color 0.2s;}
        .footer-links a:hover{color:white;}
        .footer-copy{max-width:1280px;margin:1.5rem auto 0;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.08);text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.4);}
        @media(max-width:900px){.featured-article{grid-template-columns:1fr;}.main-sidebar-layout{grid-template-columns:1fr;}}
    </style>
</head>
<body>

@include('partials.storefront-navbar')

<!-- PAGE HERO -->
<div class="page-hero">
    <div class="page-hero-inner">
        <div class="page-hero-tag"><i class="fa-solid fa-newspaper"></i> Blog & Tin tức</div>
        <h1>Kiến thức <span>Nhiếp ảnh</span><br>Chuyên sâu</h1>
        <p>Cập nhật review ống kính mới nhất, mẹo chụp ảnh, so sánh thiết bị từ đội ngũ chuyên gia nhiếp ảnh của LensStore.</p>
    </div>
</div>

<!-- FEATURED ARTICLE -->
@if($featuredArticle)
<div class="featured-wrap">
    <div class="featured-article">
        <img src="{{ $featuredArticle->image_url ?? 'https://images.unsplash.com/photo-1510127034890-ba27508e9f1c?w=800&auto=format&fit=crop&q=80' }}" alt="{{ $featuredArticle->title }}" class="featured-img">
        <div class="featured-content">
            <span class="tag tag-{{ $featuredArticle->tag ?? 'review' }}"><i class="fa-solid fa-star"></i> {{ $featuredArticle->tag_text ?? 'Review chi tiết' }}</span>
            <h2>{{ $featuredArticle->title }}</h2>
            <div class="article-meta">
                <span><i class="fa-solid fa-user"></i> {{ $featuredArticle->author }}</span>
                <span><i class="fa-solid fa-calendar"></i> {{ $featuredArticle->created_at->format('d M Y') }}</span>
                <span><i class="fa-solid fa-clock"></i> {{ $featuredArticle->read_time }} phút đọc</span>
            </div>
            <p>{{ $featuredArticle->description ?? Str::limit(strip_tags($featuredArticle->content), 150) }}</p>
            <a href="{{ route('storefront.news.show', $featuredArticle->id) }}" class="btn-read"><i class="fa-solid fa-arrow-right"></i> Đọc toàn bộ bài viết</a>
        </div>
    </div>
</div>
@endif

<!-- MAIN + SIDEBAR -->
<div class="main-sidebar-layout">
    <!-- LEFT: ARTICLES -->
    <div>
        <!-- FILTER TABS -->
        <div style="overflow-x:auto;margin-bottom:1.5rem;">
            <div class="tabs" id="news-tabs">
                <button class="tab active" data-tag="" onclick="filterNews(this,'')">
                    <i class="fa-solid fa-border-all"></i> Tất cả
                </button>
                @foreach($tags as $t)
                <button class="tab" data-tag="{{ $t }}" onclick="filterNews(this,'{{ $t }}')">{{ $t }}</button>
                @endforeach
            </div>
        </div>

        <div id="articles-grid-wrap">
            <div class="articles-grid" id="articles-grid">
                @forelse($articles as $art)
                <div class="article-card">
                    <div class="article-img-wrap">
                        <img src="{{ $art->image_url ?? 'https://images.unsplash.com/photo-1617886759944-8e09b0f95f3c?w=600&auto=format&fit=crop&q=80' }}" alt="{{ $art->title }}" class="article-img">
                        <div class="article-tag-overlay">
                            <span class="tag tag-{{ $art->tag ?? 'news' }}" style="margin:0;">{{ $art->tag_text ?? 'Tin tức' }}</span>
                        </div>
                    </div>
                    <div class="article-body">
                        <h3><a href="{{ route('storefront.news.show', $art->id) }}" style="color:inherit;">{{ $art->title }}</a></h3>
                        <p>{{ $art->description ?? Str::limit(strip_tags($art->content), 100) }}</p>
                        <div class="article-footer">
                            <div class="article-author">
                                <div class="author-avatar">{{ mb_substr($art->author, 0, 1) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:0.8rem;">{{ $art->author }}</div>
                                    <div>{{ $art->created_at->format('d M Y') }} · {{ $art->read_time }} phút</div>
                                </div>
                            </div>
                            <a href="{{ route('storefront.news.show', $art->id) }}" class="read-more-link">Đọc thêm <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                @empty
                <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">
                    Chưa có bài viết nào được đăng.
                </div>
                @endforelse
            </div>
            <div id="pagination-wrap">
                {{ $articles->links('vendor.pagination.storefront') }}
            </div>
        </div>
    </div>

    <!-- SIDEBAR -->
    <div>
        <!-- TRENDING -->
        <div class="sidebar-widget">
            <div class="widget-title"><i class="fa-solid fa-fire" style="color:#ef4444;"></i> Bài viết nổi bật</div>
            @foreach($trending as $i => $t)
            <div class="trending-item">
                <div class="trending-num">0{{ $i+1 }}</div>
                <div>
                    <div class="trending-title"><a href="{{ route('storefront.news.show', $t->id) }}">{{ $t->title }}</a></div>
                    <div class="trending-meta"><i class="fa-solid fa-clock"></i> {{ $t->read_time }} phút đọc · {{ $t->created_at->format('d M Y') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- TAGS -->
        <div class="sidebar-widget">
            <div class="widget-title"><i class="fa-solid fa-tag" style="color:var(--primary);"></i> Chủ đề</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($tags as $tag)
                <button onclick="filterNewsFromSidebar('{{ $tag }}')" style="padding:5px 12px;border:1px solid var(--border);border-radius:6px;font-size:0.82rem;color:var(--text-muted);background:var(--surface2);transition:all 0.2s;cursor:pointer;font-family:inherit;" onmouseover="this.style.background='var(--primary)';this.style.color='white';this.style.borderColor='var(--primary)';" onmouseout="this.style.background='var(--surface2)';this.style.color='var(--text-muted)';this.style.borderColor='var(--border)';">{{ $tag }}</button>
                @endforeach
            </div>
        </div>

        <!-- PROMO -->
        <div style="background:linear-gradient(135deg,var(--primary),#6366f1);border-radius:var(--radius);padding:1.75rem;color:white;text-align:center;">
            <i class="fa-solid fa-tag" style="font-size:2rem;margin-bottom:1rem;color:var(--accent);display:block;"></i>
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:0.5rem;">Ưu đãi hôm nay</h3>
            <p style="color:rgba(255,255,255,.8);font-size:0.85rem;margin-bottom:1.25rem;">Giảm đến 15% cho tất cả ống kính Sony G Master trong tuần này!</p>
            <a href="{{ route('storefront.products') }}" style="display:block;background:var(--accent);color:var(--dark);padding:0.7rem;border-radius:8px;font-weight:700;font-size:0.88rem;">Mua ngay →</a>
        </div>
    </div>
</div>

<!-- NEWSLETTER -->
<div class="newsletter">
    <div class="newsletter-inner">
        <h2>Đăng ký nhận tin tức</h2>
        <p>Nhận bài viết mới, review ống kính và ưu đãi đặc biệt trực tiếp vào hộp thư của bạn.</p>
        <div class="newsletter-form">
            <input type="email" class="newsletter-input" placeholder="Nhập địa chỉ email của bạn...">
            <button class="newsletter-btn"><i class="fa-solid fa-paper-plane"></i> Đăng ký</button>
        </div>
    </div>
</div>

<!-- FOOTER -->
@include('partials.footer')

@include('partials.customer-chat')

<script>
const NEWS_AJAX_URL = "{{ route('storefront.news.ajax') }}";
let currentTag = '';

function filterNews(btn, tag) {
    document.querySelectorAll('#news-tabs .tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    currentTag = tag;
    fetchArticles(tag, 1);
}

function filterNewsFromSidebar(tag) {
    const btn = [...document.querySelectorAll('#news-tabs .tab')].find(b => b.dataset.tag === tag);
    if (btn) {
        filterNews(btn, tag);
    } else {
        currentTag = tag;
        fetchArticles(tag, 1);
    }
    document.getElementById('articles-grid-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function fetchArticles(tag, page) {
    const grid = document.getElementById('articles-grid');
    const paginationWrap = document.getElementById('pagination-wrap');

    grid.style.opacity = '0.35';
    grid.style.pointerEvents = 'none';

    const params = new URLSearchParams({ page });
    if (tag) params.set('tag', tag);

    fetch(NEWS_AJAX_URL + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.articles || data.articles.length === 0) {
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:#64748b;"><i class="fa-solid fa-folder-open" style="font-size:2.5rem;opacity:0.3;display:block;margin-bottom:1rem;"></i>Chưa có bài viết nào trong mục này.</div>';
        } else {
            grid.innerHTML = data.articles.map(art => `
            <div class="article-card">
                <div class="article-img-wrap">
                    <img src="${art.image_url || 'https://images.unsplash.com/photo-1617886759944-8e09b0f95f3c?w=600&auto=format&fit=crop&q=80'}" alt="${escHtml(art.title)}" class="article-img" loading="lazy">
                    <div class="article-tag-overlay">
                        <span class="tag tag-${art.tag || 'news'}" style="margin:0;">${escHtml(art.tag_text || 'Tin tức')}</span>
                    </div>
                </div>
                <div class="article-body">
                    <h3><a href="/tin-tuc/${art.id}" style="color:inherit;">${escHtml(art.title)}</a></h3>
                    <p>${escHtml(art.description || art.content_preview)}</p>
                    <div class="article-footer">
                        <div class="article-author">
                            <div class="author-avatar">${escHtml(art.author.charAt(0))}</div>
                            <div>
                                <div style="font-weight:600;font-size:0.8rem;">${escHtml(art.author)}</div>
                                <div>${escHtml(art.date)} · ${art.read_time} phút</div>
                            </div>
                        </div>
                        <a href="/tin-tuc/${art.id}" class="read-more-link">Đọc thêm <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>`).join('');
        }

        paginationWrap.innerHTML = data.pagination_html || '';
        grid.style.opacity = '1';
        grid.style.pointerEvents = 'auto';
    })
    .catch(() => {
        grid.style.opacity = '1';
        grid.style.pointerEvents = 'auto';
    });
}

function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Hijack pagination link clicks to use AJAX
document.getElementById('pagination-wrap').addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');
    if (!link) return;
    e.preventDefault();
    try {
        const url = new URL(link.href);
        const page = url.searchParams.get('page') || 1;
        fetchArticles(currentTag, page);
    } catch(err) {}
});
</script>
</body>
</html>
