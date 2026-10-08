<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} – LensStore</title>
    <meta name="description" content="{{ Str::limit($article->description ?? strip_tags($article->content), 150) }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#4f46e5;--primary-hover:#4338ca;--accent:#f59e0b;--dark:#0f172a;--surface:#fff;--surface2:#f8fafc;--text-main:#1e293b;--text-muted:#64748b;--border:#e2e8f0;--radius:16px;--shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -2px rgba(0,0,0,.1);}
        *{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{font-family:'Inter',sans-serif;background:var(--surface2);color:var(--text-main);line-height:1.6;-webkit-font-smoothing:antialiased;}
        a{text-decoration:none;color:inherit;}

        .main-layout { max-width: 1280px; margin: 3rem auto; padding: 0 2.5rem; display: grid; grid-template-columns: 1fr 320px; gap: 3rem; }
        @media(max-width:900px){.main-layout{grid-template-columns:1fr; gap: 2rem; padding: 0 1.5rem; margin: 2rem auto;}}

        .article-container { background: white; border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; }
        .article-header { padding: 3rem 3rem 0; }
        .article-tag { display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; background: #ede9fe; color: #6d28d9; margin-bottom: 1.5rem; }
        .article-title { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.5px; line-height: 1.3; margin-bottom: 1.5rem; color: var(--dark); }
        .article-meta { display: flex; align-items: center; gap: 1.5rem; font-size: 0.9rem; color: var(--text-muted); padding-bottom: 1.5rem; border-bottom: 1px solid var(--border); }
        .article-meta span { display: flex; align-items: center; gap: 6px; }
        
        .article-hero-img { width: 100%; height: auto; max-height: 500px; object-fit: cover; margin-top: 2rem; }
        .article-content { padding: 3rem; font-size: 1.05rem; line-height: 1.8; color: #334155; }
        .article-content p { margin-bottom: 1.5rem; }
        .article-content h2, .article-content h3 { color: var(--dark); margin: 2rem 0 1rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.4; }
        .article-content h2 { font-size: 1.7rem; }
        .article-content h3 { font-size: 1.4rem; }
        .article-content img { max-width: 100%; border-radius: 12px; margin: 1.5rem 0; }
        .article-content ul, .article-content ol { margin: 0 0 1.5rem 2rem; }
        .article-content li { margin-bottom: 0.5rem; }
        .article-content blockquote { border-left: 4px solid var(--primary); padding-left: 1.5rem; margin: 2rem 0; font-style: italic; color: var(--text-muted); font-size: 1.1rem; }
        
        .article-actions { padding: 2rem 3rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
        .back-link { font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 8px; transition: gap 0.2s; }
        .back-link:hover { gap: 12px; }
        .share-buttons { display: flex; gap: 10px; }
        .share-btn { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; transition: transform 0.2s; }
        .share-btn:hover { transform: translateY(-2px); }
        .share-fb { background: #1877f2; }
        .share-tw { background: #1da1f2; }
        .share-li { background: #0a66c2; }

        /* SIDEBAR WIDGET */
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
        
        @media(max-width:600px) {
            .article-header { padding: 2rem 1.5rem 0; }
            .article-title { font-size: 1.8rem; }
            .article-meta { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
            .article-content { padding: 2rem 1.5rem; font-size: 1rem; }
            .article-actions { padding: 1.5rem; flex-direction: column; gap: 1.5rem; }
        }
    </style>
</head>
<body>

@include('partials.storefront-navbar')

<div class="main-layout">
    <!-- LEFT: MAIN ARTICLE -->
    <div>
        <div class="article-container">
            <div class="article-header">
                <span class="article-tag"><i class="fa-solid fa-tag"></i> {{ $article->tag_text ?? 'Tin tức' }}</span>
                <h1 class="article-title">{{ $article->title }}</h1>
                <div class="article-meta">
                    <span><i class="fa-solid fa-user"></i> {{ $article->author }}</span>
                    <span><i class="fa-solid fa-calendar"></i> {{ $article->created_at->format('d M Y') }}</span>
                    <span><i class="fa-solid fa-clock"></i> {{ $article->read_time }} phút đọc</span>
                </div>
            </div>
            
            @if($article->image_url)
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="article-hero-img">
            @endif

            <div class="article-content">
                @if($article->description)
                <p style="font-weight: 600; font-size: 1.1rem; margin-bottom: 2rem;">{{ $article->description }}</p>
                @endif
                
                {!! $article->content !!}
            </div>
            
            <div class="article-actions">
                <a href="{{ route('storefront.news') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại Tin tức</a>
                <div class="share-buttons">
                    <a href="#" class="share-btn share-fb" title="Chia sẻ Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="share-btn share-tw" title="Chia sẻ Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="share-btn share-li" title="Chia sẻ LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: SIDEBAR -->
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
                <a href="{{ route('storefront.news', ['tag' => $tag]) }}" style="padding:5px 12px;border:1px solid var(--border);border-radius:6px;font-size:0.82rem;color:var(--text-muted);background:var(--surface2);transition:all 0.2s;" onmouseover="this.style.background='var(--primary)';this.style.color='white';this.style.borderColor='var(--primary)';" onmouseout="this.style.background='var(--surface2)';this.style.color='var(--text-muted)';this.style.borderColor='var(--border)';">{{ $tag }}</a>
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

<!-- FOOTER -->
@include('partials.footer')

@include('partials.customer-chat')
</body>
</html>
