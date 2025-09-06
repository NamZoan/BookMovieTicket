document.addEventListener('DOMContentLoaded', function () {
    let currentSeatType = 'normal';
    const colors = {
        normal: 'bg-gray-200',
        vip: 'bg-yellow-500',
        disabled: 'bg-red-500'
    };

    // Chọn loại ghế
    document.querySelectorAll('.seat-type-option').forEach(option => {
        option.addEventListener('click', function () {
            currentSeatType = this.getAttribute('data-type');
            document.querySelectorAll('.seat-type-option').forEach(el => el.classList.remove('border-blue-500'));
            this.classList.add('border-blue-500');
        });
    });

    // Thêm hàng ghế
    document.getElementById('add-row').addEventListener('click', function () {
        const seatCount = parseInt(document.getElementById('row-seats').value);
        const rows = document.querySelectorAll('#cinema-room .row');
        const lastRow = rows.length > 0 ? rows[rows.length - 1].getAttribute('data-row') : '@';
        const newRow = String.fromCharCode(lastRow.charCodeAt(0) + 1);

        const rowDiv = document.createElement('div');
        rowDiv.className = 'row mb-3 flex justify-center';
        rowDiv.setAttribute('data-row', newRow);

        for (let i = 1; i <= seatCount; i++) {
            const seat = document.createElement('div');
            seat.className = `seat ${colors[currentSeatType]} w-8 h-8 rounded-sm flex items-center justify-center mx-1`;
            seat.setAttribute('data-seat', `${newRow}${i}`);
            seat.innerHTML = document.getElementById('toggle-names').checked ? `${newRow}${i}` : '';
            if (currentSeatType === 'vip') seat.setAttribute('data-vip', 'true');
            if (currentSeatType === 'disabled') seat.setAttribute('data-disabled', 'true');
            rowDiv.appendChild(seat);
        }

        document.getElementById('cinema-room').appendChild(rowDiv);
        makeSeatsDraggable();
        updateSeatCount();
    });

    // Toggle hiển thị tên ghế
    document.getElementById('toggle-names').addEventListener('change', function () {
        const seats = document.querySelectorAll('.seat');
        seats.forEach(seat => {
            if (this.checked) {
                seat.innerHTML = seat.getAttribute('data-seat');
            } else {
                seat.innerHTML = seat.classList.contains('vip') ? 'VIP' : '';
            }
        });
    });

    // Làm ghế có thể kéo thả
    function makeSeatsDraggable() {
        interact('.seat').draggable({
            inertia: false,
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: '#cinema-room',
                    endOnly: true
                })
            ],
            autoScroll: false,
            listeners: {
                start(event) {
                    event.target.classList.add('selected');
                },
                move(event) {
                    const target = event.target;
                    const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);
                },
                end(event) {
                    event.target.classList.remove('selected');
                }
            }
        });
    }

    // Khởi tạo các ghế ban đầu có thể kéo thả
    makeSeatsDraggable();

    // Cập nhật số lượng ghế
    function updateSeatCount() {
        const seats = document.querySelectorAll('.seat');
        const normal = document.querySelectorAll('.seat:not(.vip):not(.disabled)').length;
        const vip = document.querySelectorAll('.seat.vip').length;
        const disabled = document.querySelectorAll('.seat.disabled').length;

        document.getElementById('total-seats').textContent = seats.length;
        document.getElementById('normal-seats').textContent = normal;
        document.getElementById('vip-seats').textContent = vip;
        document.getElementById('disabled-seats').textContent = disabled;
    }

    // Lưu bố trí ghế
    document.getElementById('save-btn').addEventListener('click', function () {
        const seats = [];
        document.querySelectorAll('.seat').forEach(seat => {
            seats.push({
                id: seat.getAttribute('data-seat'),
                type: seat.classList.contains('vip') ? 'vip' :
                    seat.classList.contains('disabled') ? 'disabled' : 'normal',
                x: parseFloat(seat.getAttribute('data-x')) || 0,
                y: parseFloat(seat.getAttribute('data-y')) || 0
            });
        });

        // Gửi dữ liệu lên server
        alert(`Đã lưu bố trí ${seats.length} ghế\nDữ liệu có thể gửi lên server ở đây`);
        console.log('Dữ liệu ghế:', seats);
    });

    // Cập nhật đếm ghế ban đầu
    updateSeatCount();
});
