$(document).ready(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });

    function getDataTable($el) {
        var $table = $el.closest('table');
        if ($table.length && $.fn.DataTable.isDataTable($table)) {
            return $table.DataTable();
        }
        return null;
    }

    // ========== STATUS TOGGLE ==========
    $(document).on('click', '.btn-status-toggle', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var url = $btn.data('url');

        $.ajax({
            url: url,
            type: 'PATCH',
            dataType: 'json',
            beforeSend: function () {
                $btn.prop('disabled', true);
            },
            success: function (res) {
                var dt = getDataTable($btn);
                if (dt) {
                    dt.draw(false);
                }
                Toast.fire({ icon: 'success', title: res.message || 'Status updated!' });
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                Toast.fire({ icon: 'error', title: msg });
            }
        });
    });

    // ========== SET ACTIVE (Financial Year) ==========
    $(document).on('click', '.btn-set-active', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var url = $btn.data('url');

        $.ajax({
            url: url,
            type: 'PATCH',
            dataType: 'json',
            beforeSend: function () {
                $btn.prop('disabled', true);
            },
            success: function (res) {
                var dt = getDataTable($btn);
                if (dt) {
                    dt.draw(false);
                }
                Toast.fire({ icon: 'success', title: res.message || 'Updated!' });
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                Toast.fire({ icon: 'error', title: msg });
            }
        });
    });

    // ========== DELETE ==========
    $(document).on('click', '.btn-delete-item', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var url = $btn.data('url');
        var name = $btn.data('name') || 'this item';

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete " + name + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    beforeSend: function () {
                        $btn.prop('disabled', true);
                    },
                    success: function (res) {
                        var dt = getDataTable($btn);
                        if (dt) {
                            dt.ajax.reload(null, false);
                        }
                        Toast.fire({ icon: 'success', title: res.message || 'Deleted!' });
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false);
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                        Toast.fire({ icon: 'error', title: msg });
                    }
                });
            }
        });
    });
});
