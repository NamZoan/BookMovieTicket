{{-- Form để người dùng gửi đánh giá --}}
@auth
    @if ($canReview && !$userHasReviewed)
        <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Viết đánh giá của bạn</h3>
            <form id="review-form" action="{{ route('movies.reviews.store', $movie) }}" method="POST">
                @csrf

                {{-- Rating Stars --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Bạn đánh giá phim này thế nào?</label>
                    <div class="flex items-center space-x-1 rating-stars">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="hidden" required/>
                            <label for="star{{ $i }}" class="cursor-pointer text-gray-300 text-3xl transition-colors hover:text-yellow-400">★</label>
                        @endfor
                    </div>
                </div>

                {{-- Comment Box --}}
                <div class="mb-4">
                    <label for="comment" class="block text-gray-700 font-medium mb-2">Bình luận của bạn</label>
                    <textarea name="comment" id="comment" rows="4"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        placeholder="Chia sẻ cảm nhận của bạn về bộ phim..."></textarea>
                </div>

                <button type="submit"
                    class="bg-red-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-red-700 transition-colors disabled:bg-gray-400">
                    Gửi đánh giá
                </button>
            </form>
        </div>
    @elseif($userHasReviewed)
        <div class="mb-8 p-4 text-center bg-green-50 text-green-800 rounded-lg border border-green-200">
            Cảm ơn bạn đã đánh giá phim này!
        </div>
    @else
        <div class="mb-8 p-4 text-center bg-blue-50 text-blue-800 rounded-lg border border-blue-200">
            Bạn cần đặt vé xem phim này để có thể để lại đánh giá.
        </div>
    @endif
@else
    <div class="mb-8 p-4 text-center bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200">
        <a href="{{ route('auth.login') }}?redirect={{ url()->current() }}" class="font-semibold underline">Đăng nhập</a> để đánh giá phim này.
    </div>
@endauth

{{-- Thống kê đánh giá --}}
<div class="mb-8">
    <h3 class="text-xl font-bold mb-4">Tổng quan đánh giá</h3>
    <div class="flex items-center gap-8">
        <div class="text-center">
            <div class="text-5xl font-bold text-red-600">{{ number_format($averageRating, 1) }}</div>
            <div class="text-gray-500">trên 5 sao</div>
            <div class="text-sm text-gray-400 mt-1">({{ $totalReviews }} đánh giá)</div>
        </div>
        <div class="flex-grow">
            <div class="space-y-2">
                @for ($i = 5; $i >= 1; $i--)
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">{{ $i }} sao</span>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ $ratingDistribution[$i]['percentage'] }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-600 w-12 text-right">{{ $ratingDistribution[$i]['count'] }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

{{-- Danh sách các bài đánh giá --}}
<div>
    <h3 class="text-xl font-bold mb-6 border-b pb-3">Tất cả đánh giá ({{ $reviews->total() }})</h3>
    @if($reviews->isEmpty())
        <div class="text-center py-12 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 5.523-4.477 10-10 10S1 17.523 1 12 5.477 2 11 2s10 4.477 10 10z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đánh giá nào</h3>
            <p class="mt-1 text-sm text-gray-500">Hãy là người đầu tiên chia sẻ cảm nhận của bạn!</p>
        </div>
    @else
        <div class="space-y-8">
            @foreach($reviews as $review)
                <div class="flex space-x-4">
                    <div class="flex-shrink-0">
                        <img class="h-12 w-12 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($review->user->full_name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $review->user->full_name }}">
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h4 class="text-sm font-bold text-gray-900">{{ $review->user->full_name }}</h4>
                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center mt-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        @if($review->comment)
                            <p class="mt-3 text-gray-700 leading-relaxed">
                                {{ $review->comment }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($reviews->hasPages())
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>
        @endif
    @endif
</div>

<style>
.rating-stars {
    direction: rtl;
}
.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #f59e0b; /* text-yellow-400 */
}
</style>

<script>
    // Handle review form submission via AJAX
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Đang gửi...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload reviews to show the new one
                    loadReviews();
                } else {
                    alert(data.message || 'Đã có lỗi xảy ra.');
                    button.disabled = false;
                    button.textContent = 'Gửi đánh giá';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra, vui lòng thử lại.');
                button.disabled = false;
                button.textContent = 'Gửi đánh giá';
            });
        });
    }
</script>
