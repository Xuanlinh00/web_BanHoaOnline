<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Câu hỏi thường gặp';
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="mb-4 fw-bold" style="color: var(--primary-color);">❓ Câu hỏi thường gặp</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="accordion" id="faqAccordion">
                <!-- Question 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Làm thế nào để đặt hàng?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Để đặt hàng, bạn cần:</p>
                            <ol>
                                <li>Đăng nhập hoặc tạo tài khoản mới</li>
                                <li>Chọn sản phẩm bạn muốn mua</li>
                                <li>Thêm vào giỏ hàng</li>
                                <li>Tiến hành thanh toán</li>
                                <li>Chọn địa chỉ giao hàng và khung giờ giao hàng</li>
                                <li>Xác nhận đơn hàng</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Thời gian giao hàng là bao lâu?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Chúng tôi cung cấp dịch vụ giao hàng trong ngày hoặc ngày hôm sau tùy theo thời gian đặt hàng:</p>
                            <ul>
                                <li>Đặt hàng trước 10:00 sáng: Giao hàng cùng ngày</li>
                                <li>Đặt hàng từ 10:00 - 18:00: Giao hàng ngày hôm sau</li>
                                <li>Đặt hàng sau 18:00: Giao hàng ngày hôm sau</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Phí giao hàng là bao nhiêu?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Phí giao hàng cố định là <strong>30.000đ</strong> cho mỗi đơn hàng.</p>
                            <p><strong>Miễn phí giao hàng</strong> cho đơn hàng từ <strong>500.000đ</strong> trở lên.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            Tôi có thể thanh toán như thế nào?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Chúng tôi hỗ trợ các phương thức thanh toán sau:</p>
                            <ul>
                                <li><strong>Thanh toán khi nhận hàng (COD):</strong> Thanh toán trực tiếp cho shipper</li>
                                <li><strong>Chuyển khoản ngân hàng:</strong> Chuyển tiền trước khi giao hàng</li>
                                <li><strong>Ví điện tử:</strong> Hỗ trợ các ví điện tử phổ biến</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            Tôi có thể hoàn trả sản phẩm không?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Có, bạn có thể hoàn trả sản phẩm trong vòng <strong>24 giờ</strong> kể từ khi nhận hàng nếu:</p>
                            <ul>
                                <li>Sản phẩm không đúng với mô tả</li>
                                <li>Sản phẩm bị hư hỏng hoặc lỗi</li>
                            </ul>
                            <p>Vui lòng liên hệ với chúng tôi qua điện thoại hoặc email để yêu cầu hoàn trả.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                            Làm thế nào để theo dõi đơn hàng của tôi?
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Bạn có thể theo dõi đơn hàng của mình bằng cách:</p>
                            <ol>
                                <li>Đăng nhập vào tài khoản của bạn</li>
                                <li>Vào mục "Đơn hàng của tôi"</li>
                                <li>Chọn đơn hàng bạn muốn xem</li>
                                <li>Xem trạng thái và thông tin giao hàng</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                            Tôi có thể gửi hoa ẩn danh không?
                        </button>
                    </h2>
                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Có, bạn có thể gửi hoa ẩn danh. Khi thanh toán, hãy chọn tùy chọn "Gửi ẩn danh" và tên người gửi sẽ không được hiển thị.</p>
                            <p>Bạn vẫn có thể thêm thiệp chúc mừng nếu muốn.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                            Tôi quên mật khẩu, làm thế nào?
                        </button>
                    </h2>
                    <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Nếu bạn quên mật khẩu, bạn có thể:</p>
                            <ol>
                                <li>Vào trang đăng nhập</li>
                                <li>Nhấp vào "Quên mật khẩu?"</li>
                                <li>Nhập email của bạn</li>
                                <li>Kiểm tra email để nhận liên kết đặt lại mật khẩu</li>
                                <li>Nhấp vào liên kết và tạo mật khẩu mới</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Question 9 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                            Hoa có tươi bao lâu?
                        </button>
                    </h2>
                    <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Thời gian tươi của hoa tùy thuộc vào loại hoa:</p>
                            <ul>
                                <li><strong>Hoa hồng:</strong> 7-10 ngày</li>
                                <li><strong>Hoa hướng dương:</strong> 10-14 ngày</li>
                                <li><strong>Hoa tulip:</strong> 7-10 ngày</li>
                                <li><strong>Hoa cúc:</strong> 10-14 ngày</li>
                            </ul>
                            <p>Để hoa tươi lâu hơn, hãy thay nước hàng ngày và cắt thân hoa mỗi 2-3 ngày.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 10 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                            Tôi có thể hủy đơn hàng không?
                        </button>
                    </h2>
                    <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Bạn có thể hủy đơn hàng nếu nó vẫn ở trạng thái "Chờ xác nhận". Sau khi đơn hàng được xác nhận, bạn không thể hủy.</p>
                            <p>Để hủy đơn hàng, vui lòng liên hệ với chúng tôi qua điện thoại hoặc email.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">Không tìm thấy câu trả lời?</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Nếu bạn không tìm thấy câu trả lời cho câu hỏi của mình, vui lòng liên hệ với chúng tôi.</p>
                    <a href="/web_banhoa/views/contact.php" class="btn btn-primary w-100">
                        <i class="fas fa-envelope"></i> Liên hệ ngay
                    </a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">📞 Hỗ trợ trực tiếp</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gọi cho chúng tôi để được hỗ trợ ngay lập tức</p>
                    <p class="mb-0"><strong>0123 456 789</strong></p>
                    <p class="text-muted small">Thứ 2 - Chủ nhật: 8:00 - 20:00</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
