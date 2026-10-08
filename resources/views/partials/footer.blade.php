<style>
/* Modern Luxurious Footer */
.modern-footer {
    background: linear-gradient(to bottom, #0f172a, #0b1120);
    color: #e2e8f0;
    padding: 5rem 2.5rem 2rem;
    margin-top: 4rem;
    font-family: 'Inter', sans-serif;
    position: relative;
    overflow: hidden;
}
.modern-footer::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(79,70,229,0.5), transparent);
}
.mf-inner {
    max-width: 1280px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr;
    gap: 3rem;
}
.mf-brand {
    font-size: 1.8rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.mf-brand i { color: #4f46e5; }
.mf-desc {
    color: #94a3b8;
    line-height: 1.7;
    font-size: 0.95rem;
    margin-bottom: 2rem;
    max-width: 350px;
}
.mf-socials {
    display: flex;
    gap: 1rem;
}
.mf-socials a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px; height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    color: #fff;
    transition: all 0.3s ease;
    text-decoration: none;
}
.mf-socials a:hover {
    background: #4f46e5;
    transform: translateY(-3px);
    box-shadow: 0 10px 15px -3px rgba(79,70,229,0.3);
}
.mf-title {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.5rem;
}
.mf-title::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 30px; height: 2px;
    background: #4f46e5;
    border-radius: 2px;
}
.mf-links {
    list-style: none;
    padding: 0; margin: 0;
}
.mf-links li { margin-bottom: 1rem; }
.mf-links a {
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: inline-block;
}
.mf-links a:hover {
    color: #4f46e5;
    transform: translateX(5px);
}
.mf-contact li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    color: #94a3b8;
    font-size: 0.95rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}
.mf-contact i {
    color: #4f46e5;
    margin-top: 4px;
}
.mf-newsletter {
    background: rgba(255,255,255,0.03);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.05);
}
.mf-newsletter p {
    color: #94a3b8;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    margin-top: 0;
}
.mf-form { display: flex; gap: 0; }
.mf-form input {
    flex: 1;
    padding: 0.8rem 1rem;
    border: none;
    background: rgba(255,255,255,0.05);
    color: #fff;
    border-radius: 6px 0 0 6px;
    outline: none;
    font-family: inherit;
}
.mf-form input::placeholder { color: #64748b; }
.mf-form button {
    padding: 0.8rem 1.2rem;
    border: none;
    background: #4f46e5;
    color: #fff;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s;
}
.mf-form button:hover { background: #4338ca; }
.mf-bottom {
    max-width: 1280px;
    margin: 4rem auto 0;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #64748b;
    font-size: 0.85rem;
}
.mf-payments {
    display: flex;
    gap: 10px;
    font-size: 1.5rem;
    color: #94a3b8;
}
@media (max-width: 1024px) {
    .mf-inner { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
    .mf-inner { grid-template-columns: 1fr; }
    .mf-bottom { flex-direction: column; gap: 1rem; text-align: center; }
}
</style>

<footer class="modern-footer">
    <div class="mf-inner">
        <!-- Col 1: Brand -->
        <div>
            <div class="mf-brand">
                <i class="fa-solid fa-camera-retro"></i> LensStore
            </div>
            <p class="mf-desc">
                Nhà phân phối ủy quyền ống kính máy ảnh hàng đầu Việt Nam. Chúng tôi mang đến giải pháp nhiếp ảnh chuyên nghiệp, biến mọi khoảnh khắc thành kiệt tác.
            </p>
            <div class="mf-socials">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>

        <!-- Col 2: Links -->
        <div>
            <h4 class="mf-title">Sản Phẩm</h4>
            <ul class="mf-links">
                @php $cats = \App\Models\Category::take(5)->get(); @endphp
                @foreach($cats as $cat)
                    <li><a href="/?category={{ $cat->id }}">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Col 3: Services -->
        <div>
            <h4 class="mf-title">Dịch Vụ</h4>
            <ul class="mf-links">
                <li><a href="#">Cho thuê ống kính</a></li>
                <li><a href="#">Bảo dưỡng & Vệ sinh</a></li>
                <li><a href="#">Thu cũ đổi mới</a></li>
                <li><a href="#">Trang tin tức</a></li>
                <li><a href="#">Chính sách bảo hành</a></li>
            </ul>
        </div>

        <!-- Col 4: Newsletter -->
        <div>
            <div class="mf-newsletter">
                <h4 class="mf-title" style="margin-bottom: 0.5rem; border: none; padding: 0;">Đăng Ký Nhận Tin</h4>
                <p>Nhận ưu đãi độc quyền và cập nhật mới nhất về các dòng lens cao cấp.</p>
                <form class="mf-form" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký!');">
                    <input type="email" placeholder="Email của bạn..." required>
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>
            
            <ul class="mf-contact" style="list-style: none; padding: 0; margin-top: 1.5rem;">
                <li><i class="fa-solid fa-location-dot"></i> 123 Nguyễn Huệ, Quận 1, TP.HCM</li>
                <li><i class="fa-solid fa-phone"></i> 1800 1234 (Miễn phí)</li>
            </ul>
        </div>
    </div>
    
    <div class="mf-bottom">
        <div>&copy; {{ date('Y') }} LensStore. Bảo lưu mọi quyền. Thiết kế độc quyền.</div>
        <div class="mf-payments">
            <i class="fa-brands fa-cc-visa"></i>
            <i class="fa-brands fa-cc-mastercard"></i>
            <i class="fa-brands fa-cc-paypal"></i>
            <i class="fa-brands fa-cc-apple-pay"></i>
        </div>
    </div>
</footer>
