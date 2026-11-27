<script src="{{ asset('assets/admin/js/vendor.js') }}"></script>
<script src="{{ asset('assets/admin/js/app.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-notify.min.js') }}"></script>
<script>
    // Tự ẩn thông báo thành công/thông tin sau 4 giây (lỗi vẫn giữ lại để người dùng đọc)
    $(function () {
        $('.alert-success, .alert-info').not('.alert-important').each(function () {
            var $alert = $(this);
            setTimeout(function () {
                $alert.fadeOut(400, function () { $alert.remove(); });
            }, 4000);
        });
    });
</script>
