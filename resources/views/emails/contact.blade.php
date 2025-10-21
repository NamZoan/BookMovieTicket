<div style="font-family: Inter, Arial, sans-serif; color: #111;">
    <h3>Liên hệ mới từ trang MyShowz</h3>
    <p><strong>Tên:</strong> {{ $data['name'] ?? '-' }}</p>
    <p><strong>Email:</strong> {{ $data['email'] ?? '-' }}</p>
    <p><strong>Số điện thoại:</strong> {{ $data['phone'] ?? '-' }}</p>
    <p><strong>Chủ đề:</strong> {{ $data['subject'] ?? '-' }}</p>
    <p><strong>Nội dung:</strong></p>
    <div style="border-left: 4px solid #ddd; padding-left: 12px;">{!! nl2br(e($data['message'] ?? '')) !!}</div>
</div>
