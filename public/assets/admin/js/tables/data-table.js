+function ($, window) {

    var dataTables = {};

    dataTables.init = function () {
        if ($.fn.DataTable.isDataTable('#dt-opt')) {
            return;
        }
        $('#dt-opt').DataTable({
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ mục",
                info: "Hiển thị _START_ đến _END_ trong số _TOTAL_ mục",
                infoEmpty: "Hiển thị 0 đến 0 trong số 0 mục",
                infoFiltered: "(lọc từ _MAX_ mục)",
                zeroRecords: "Không tìm thấy kết quả phù hợp",
                emptyTable: "Không có dữ liệu trong bảng",
                paginate: {
                    first: "Đầu",
                    previous: "Trước",
                    next: "Tiếp",
                    last: "Cuối"
                }
            }
        });
    };
    window.dataTables = dataTables;

}(jQuery, window);

// initialize app
+function ($) {
    dataTables.init();
}(jQuery);

