@extends('user.layouts.default')

@section('title')
    {{ __('titles.course_list') }}
@endsection

@section('inline_styles')
    <style>
        /* Modern Clean Cart Page Styling */
        #cart .inner-box,
        #saved-for-later .inner-box,
        #wishlist .inner-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px 26px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            margin-bottom: 28px;
        }

        .title-2 {
            font-size: 19px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding-bottom: 14px !important;
            margin-bottom: 18px !important;
            text-transform: none !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .title-2 i {
            color: #2563eb;
            font-size: 18px;
        }

        .table-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-action .checkbox {
            margin: 0;
            padding: 0;
        }

        .table-action .table-search {
            margin: 0;
            padding: 0;
            float: none !important;
        }

        .table-search .form-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .table-search label {
            float: none !important;
            width: auto !important;
            padding: 0 !important;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }

        .table-search .searchpan {
            float: none !important;
            width: auto !important;
            padding: 0 !important;
        }

        .table-search .form-control {
            border-radius: 20px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
            padding: 6px 14px;
            height: 34px;
            width: 220px;
        }

        .add-manage-table {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .add-manage-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
        }

        .add-manage-table tbody td {
            vertical-align: middle !important;
            padding: 14px !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        .add-manage-table tbody tr:hover {
            background-color: #fbfcfe;
        }

        .ads-details-td h4 {
            font-size: 14.5px;
            font-weight: 700;
            line-height: 1.4;
            margin: 0 0 5px;
        }

        .ads-details-td h4 a {
            color: #0f172a !important;
            text-decoration: none !important;
            transition: color 0.18s ease;
        }

        .ads-details-td h4 a:hover {
            color: #2563eb !important;
        }

        .ads-details-td p {
            font-size: 12.5px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .ads-details-td p strong {
            color: #475569;
        }

        /* Action Buttons */
        .action-td p {
            margin-bottom: 5px;
        }

        .action-td .btn {
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }

        .action-td .btn-danger {
            background: #fff1f2;
            color: #e11d48 !important;
            border: 1px solid #fecdd3;
        }

        .action-td .btn-danger:hover {
            background: #e11d48;
            color: #ffffff !important;
            border-color: #e11d48;
        }

        .action-td .btn-info {
            background: #eff6ff;
            color: #2563eb !important;
            border: 1px solid #bfdbfe;
        }

        .action-td .btn-info:hover {
            background: #2563eb;
            color: #ffffff !important;
            border-color: #2563eb;
        }

        .action-td .move-to-wishlist {
            background: #fdf2f8 !important;
            color: #db2777 !important;
            border: 1px solid #fbcfe8 !important;
        }

        .action-td .move-to-wishlist:hover {
            background: #db2777 !important;
            color: #ffffff !important;
        }

        .action-td .move-to-cart {
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
        }

        .action-td .move-to-cart:hover {
            background: #2563eb !important;
            color: #ffffff !important;
        }

        /* Summary Box */
        .cart-summary-box {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 22px 26px !important;
        }

        #btn-cart-checkout {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
            transition: all 0.2s ease !important;
        }

        #btn-cart-checkout:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45) !important;
        }
    </style>
@endsection

@section('content')
    {{ Breadcrumbs::render('cart') }}<br>
    <div id="content">
        <div class="container">
            <div class="row">
                @include('user.partials.user_management_info')
                @include('user.cart_items.partials.list')
            </div>
        </div>
    </div>
    </div>
@endsection
@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#my-cart').addClass('active');

            // --- ĐỊNH DẠNG TIỀN TỆ ---
            function formatVND(amount) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' đ';
            }

            // --- TÍNH TOÁN LẠI TỔNG TIỀN GIỎ HÀNG THEO CÁC MỤC ĐƯỢC CHỌN ---
            function updateCartSummary() {
                var checkedBoxes = $('#in-cart-item-table tbody .cart-item-checkbox:checked');
                var totalOrigin = 0;
                var totalPromotion = 0;

                checkedBoxes.each(function () {
                    var origin = parseFloat($(this).data('origin-price')) || 0;
                    var promo = parseFloat($(this).data('promotion-price')) || origin;
                    totalOrigin += origin;
                    totalPromotion += promo;
                });

                if (checkedBoxes.length === 0) {
                    $('#cart-total-pay').text('0 đ').css('color', '#64748b');
                    $('#cart-total-origin').hide();
                    $('#cart-total-discount').hide();
                    $('#btn-cart-checkout').css({'opacity': '0.6', 'pointer-events': 'none'});
                } else {
                    $('#btn-cart-checkout').css({'opacity': '1', 'pointer-events': 'auto'});
                    if (totalPromotion < totalOrigin && totalOrigin > 0) {
                        $('#cart-total-pay').text(formatVND(totalPromotion)).css('color', '#dc2626');
                        $('#cart-total-origin').text(formatVND(totalOrigin)).show();
                        var percent = ((1 - totalPromotion / totalOrigin) * 100).toFixed(1);
                        $('#cart-total-discount').text('Tiết kiệm ' + percent + '%').show();
                    } else {
                        $('#cart-total-pay').text(formatVND(totalOrigin)).css('color', '#1e293b');
                        $('#cart-total-origin').hide();
                        $('#cart-total-discount').hide();
                    }
                }
            }

            // --- XỬ LÝ CHỌN TẤT CẢ & XÓA NHIỀU MỤC TRONG GIỎ HÀNG ---
            $('#checkAllInCart').on('change', function () {
                var isChecked = $(this).is(':checked');
                $('#in-cart-item-table tbody .cart-item-checkbox').prop('checked', isChecked);
                updateCartSummary();
            });
            $('#in-cart-item-table tbody').on('change', '.cart-item-checkbox', function () {
                var total = $('#in-cart-item-table tbody .cart-item-checkbox').length;
                var checked = $('#in-cart-item-table tbody .cart-item-checkbox:checked').length;
                $('#checkAllInCart').prop('checked', total > 0 && total === checked);
                updateCartSummary();
            });

            // Khởi chạy tính toán lúc load trang
            updateCartSummary();

            // --- XỬ LÝ NÚT THANH TOÁN (CHỈ THANH TOÁN CÁC KHÓA ĐƯỢC CHỌN) ---
            $('#btn-cart-checkout').on('click', function (e) {
                e.preventDefault();
                var selectedIds = [];
                $('#in-cart-item-table tbody .cart-item-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                if (selectedIds.length === 0) {
                    alert('Vui lòng tích chọn ít nhất một khóa học để tiến hành thanh toán.');
                    return;
                }
                var checkoutUrl = "{{ route('cart_items.checkout.get') }}" + '?items=' + selectedIds.join(',');
                window.location.href = checkoutUrl;
            });
            $('#btn-delete-selected-cart').on('click', function (e) {
                e.preventDefault();
                var selectedIds = [];
                $('#in-cart-item-table tbody .cart-item-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                if (selectedIds.length === 0) {
                    alert('Vui lòng tích chọn ít nhất một khóa học để xóa.');
                    return;
                }
                if (confirm('Bạn có chắc chắn muốn xóa ' + selectedIds.length + ' khóa học đã chọn khỏi giỏ hàng?')) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: 'cart_items/remove_multiple',
                        data: {
                            cartItemIds: selectedIds,
                        },
                        success: function () {
                            location.reload();
                        }
                    }, 'json');
                }
            });

            // --- XỬ LÝ CHO MỤC LƯU ĐỂ HỌC SAU ---
            $('#checkAllInLater').on('change', function () {
                var isChecked = $(this).is(':checked');
                $('#save-for-later-item-table tbody .later-item-checkbox').prop('checked', isChecked);
            });
            $('#save-for-later-item-table tbody').on('change', '.later-item-checkbox', function () {
                var total = $('#save-for-later-item-table tbody .later-item-checkbox').length;
                var checked = $('#save-for-later-item-table tbody .later-item-checkbox:checked').length;
                $('#checkAllInLater').prop('checked', total > 0 && total === checked);
            });
            $('#btn-delete-selected-later').on('click', function (e) {
                e.preventDefault();
                var selectedIds = [];
                $('#save-for-later-item-table tbody .later-item-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                if (selectedIds.length === 0) {
                    alert('Vui lòng tích chọn ít nhất một khóa học để xóa.');
                    return;
                }
                if (confirm('Bạn có chắc chắn muốn xóa ' + selectedIds.length + ' khóa học đã chọn?')) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: 'cart_items/remove_multiple',
                        data: {
                            cartItemIds: selectedIds,
                        },
                        success: function () {
                            location.reload();
                        }
                    }, 'json');
                }
            });

            // --- XỬ LÝ CHO DANH SÁCH YÊU THÍCH ---
            $('#checkAllInWishlist').on('change', function () {
                var isChecked = $(this).is(':checked');
                $('#move-to-wishlist-item-table tbody .wishlist-item-checkbox').prop('checked', isChecked);
            });
            $('#move-to-wishlist-item-table tbody').on('change', '.wishlist-item-checkbox', function () {
                var total = $('#move-to-wishlist-item-table tbody .wishlist-item-checkbox').length;
                var checked = $('#move-to-wishlist-item-table tbody .wishlist-item-checkbox:checked').length;
                $('#checkAllInWishlist').prop('checked', total > 0 && total === checked);
            });
            $('#btn-delete-selected-wishlist').on('click', function (e) {
                e.preventDefault();
                var selectedIds = [];
                $('#move-to-wishlist-item-table tbody .wishlist-item-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                if (selectedIds.length === 0) {
                    alert('Vui lòng tích chọn ít nhất một khóa học để xóa.');
                    return;
                }
                if (confirm('Bạn có chắc chắn muốn xóa ' + selectedIds.length + ' khóa học đã chọn?')) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: 'cart_items/remove_multiple',
                        data: {
                            cartItemIds: selectedIds,
                        },
                        success: function () {
                            location.reload();
                        }
                    }, 'json');
                }
            });

            $('.remove').on('click', function () {
                var cartItemId = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'cart_items/remove',
                    data: {
                        cartItemId: cartItemId,
                    },
                    success: function () {
                        // $('#cart-item-' + cartItemId).remove();
                        location.reload();
                    }
                }, "json")
            });

            $('.save-for-later').on('click', function () {
                var cartItemId = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'cart_items/save_for_later',
                    data: {
                        cartItemId: cartItemId,
                    },
                    success: function (data) {
                        location.reload();
                    }
                }, "json")
            });

            $('.move-to-wishlist').on('click', function () {
                var cartItemId = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'cart_items/move_to_wishlist',
                    data: {
                        cartItemId: cartItemId,
                    },
                    success: function (data) {
                        location.reload();
                        // var dataParsed = jQuery.parseJSON(data);
                        // var cartItem = dataParsed.cartItem;
                        // console.log(cartItem);
                        // var row = $('#cart-item-' + cartItem.id).closest('tr').html();
                        // $('#cart-item-' + cartItem.id).parent().remove();
                        // $('#move-to-wishlist-item-table tbody').append('<tr>'+row+'</tr>');
                        // $('#checkbox-' + cartItem.id).parent().attr('id', 'cart-item-' + cartItem.id);
                        // $('#save-for-later-' + cartItem.id).remove();
                        // $('#move-to-wishlist-' + cartItem.id).attr('id', 'move-to-cart-' + cartItem.id);
                        // $('#move-to-cart-' + cartItem.id).html('<i class="fa fa-chevron-circle-down"></i> Move to cart');
                    }
                }, "json")
            });

            $('.move-to-cart').on('click', function () {
                var cartItemId = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'cart_items/move_to_cart',
                    data: {
                        cartItemId: cartItemId,
                    },
                    success: function (data) {
                        location.reload();
                        // var dataParsed = jQuery.parseJSON(data);
                        // var cartItem = dataParsed.cartItem;
                        // var row = $('#cart-item-' + cartItem.id).closest('tr').html();
                        // $('#cart-item-' + cartItem.id).parent().remove();
                        // $('#in-cart-item-table tbody').append('<tr>'+row+'</tr>');
                        // $('#checkbox-' + cartItem.id).parent().attr('id', 'cart-item-' + cartItem.id);
                        // $('#move-to-cart-' + cartItem.id).attr('id', 'move-to-wishlist-' + cartItem.id);
                        // $('#move-to-wishlist-' + cartItem.id).html('<i class="fa fa-chevron-circle-down"></i> Move to wishlist');
                        // $('<p><a class="btn btn-info btn-xs save-for-later" data-id="' + cartItem.id + '" id="save-for-later-' + cartItem.id + '"> <i class="fa fa-share-square-o"></i> Save for later </a></p>').insertBefore('#move-to-wishlist-' + cartItem.id);
                    }
                }, "json");
            });

            // Lọc tìm kiếm thời gian thực cho các bảng trong giỏ hàng
            $('.table-search input').on('keyup', function () {
                var val = $(this).val().toLowerCase().trim();
                var table = $(this).closest('.table-responsive').find('table');
                table.find('tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
                });
            });
        });
    </script>
@endsection
