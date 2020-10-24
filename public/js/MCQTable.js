function startAjaxLoader() {
    $(".ajaxloader").removeClass('d-none');
}

function stopAjaxLoader() {
    $(".ajaxloader").addClass('d-none');
}

var orderFalseIndex = [];
var columnDefs = [];

function getSearchAction() {
    var action = [];
    var empty = true;
    $("#filterForm button[type!=button], #filterForm select, #filterForm input").each(function () {
        if ($(this).val() == null || $(this).val() == "") {
        } else {
            empty = false;
            action.push([$(this).attr('id'), $(this).val()]);
        }
    });
    return action;
}

function getSearchData(action = []) {
    var data = "";
    if (action.length > 0) {
        action.forEach(function (item, index) {
            if (index > 0) {
                data += "&";
            }
            data += item[0] + "=" + item[1];
        });
    }
    return data;
}

function drawTable(action = [], from = '') {
    $("#filterForm select, #filterForm input, #filterForm button[type!=button]").each(function () {
        $.cookie("search_" + $(this).attr("id"), $(this).val());
    });
    var defaultSorting = [[0, "asc"]];
    var columnDefs = [];
    var action = getSearchAction();
    var data = getSearchData(action);
    var pageLength = $("#dataTable_length").children('select').val();
    if (from == "print") {
        pageLength = 500;
        var printHides = [];
        $("thead tr th").each(function (index) {
            if ($(this).data('printhide') == true) {
                printHides.push(index);
            }
        });
        columnDefs.push({
            "targets": printHides,
            "visible": false
        });
    }
    $("#dataTable.ajax").DataTable().destroy();
    if ($("table").data('checkbox') == true) {
        orderFalseIndex.push(0);
        columnDefs.push({
            "width": '1px',
            "targets": 0
        });
    }
    $("thead tr th").each(function (index) {
        if ($(this).data('order') == false) {
            orderFalseIndex.push(index);
        }
    });
    for (let i = 0; i < 10; i++) {
        if (!orderFalseIndex.includes(i)) {
            defaultSorting = [[i, "asc"]];
            break;
        }
    }
    columnDefs.push({
        "orderable": false,
        "targets": orderFalseIndex
    });
    var table = $('#dataTable.ajax').DataTable({
        "order": defaultSorting,
        "dom": 't<"table-bottom"ilp>r<"clear">',
        "columnDefs": columnDefs,
        "pageLength": pageLength,
        "processing": true,
        "serverSide": true,
        "searching": false,
        "createdRow": function (row, data, index) {
            $("thead tr th").each(function (i) {
                if ($(this).hasClass('text-center')) {
                    $(row).children(":nth-child(" + (i + 1) + ")").addClass('text-center');
                }
            });
        },
        "fnDrawCallback": function () {
            stopAjaxLoader();
            $("#dataTable_previous").html('<i class="ace-icon fa fa-angle-double-left"></i>');
            $("#dataTable_next").html('<i class="ace-icon fa fa-angle-double-right"></i>');
            // if ($("tbody").text() != "No data available in table") {
            //     var html = '';
            //     if ($(".table-tools").html() == undefined) {
            //         html += '<div class="table-tools">';
            //     }
            //     tabletools.forEach(element => {
            //         if (element == 'print') {
            //             var printHtml = '';
            //             printHtml = '<button type="button" class="btn btn-white print-btn"><i class="fas fa-print"></i></button>';
            //             html += printHtml;
            //         }
            //         if (element == 'export') {
            //             var exportHtml = '';
            //             exportHtml = '<button type="button" data-export=' + exportRoute + ' class="btn btn-white btn-success export-btn"><i class="fa fa-file-excel"></i></button>';
            //             html += exportHtml;
            //         }
            //     });
            //     if ($(".table-tools").html() == undefined) {
            //         html += '</div>';
            //         $(".table-responsive").before(html);
            //     } else {
            //         $(".table-tools").html(html);
            //     }
            // } else {
            //     $(".table-tools").remove();
            // }
        },
        "stateSave": false,
        "ajax": {
            "url": $('table').data('load'),
            "type": "POST",
            "beforeSend": function () {
                startAjaxLoader();
            },
            "data": {
                _token: $("#csrf").val(),
                data: data,
            }
        }
    });
    $(".dataTables_processing").empty();
    $(".dataTables_processing").append('<div class="table_processing">Processing</div>');
}

setTimeout(function () {
    $(".alert.alert-dismissible").children('button').click();
}, 4000);
var index = 0;

function successMessage(message) {
    index = index + 1;
    $("body").append("<div class='alert alert-success alert-dismissible' id='" + (index) + "'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Success!</strong> " + message + "</div>");
    setTimeout(() => {
        $(".alert#" + index).children('button').click();
    }, 4000);
}

function failMessage(message) {
    index = index + 1;
    $("body").append("<div class='alert alert-danger alert-dismissible' id='" + (index) + "'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Error!</strong> " + message + "</div>");
    setTimeout(() => {
        $(".alert#" + index).children('button').click();
    }, 6000);
}

$(document).ready(function () {
    $("button").click(function () {
        $(this).css("outline", 'none !important');
        $(this).css("decoration", 'none !important');
    });
    var count = 0;
    $(".dataTables_processing").empty();
    $(".dataTables_processing").append('<div class="table_processing">Processing</div>');
    $("input.hide, input[type=hidden]").each(function () {
        $(this).addClass('ignore');
    });
    $("#filterForm select, #filterForm input[type!=button]").each(function () {
        var tagName = $(this).prop("tagName").toLowerCase();
        switch (tagName) {
            case "input":
                if ($.cookie("search_" + $(this).attr("id")) != "") {
                    $(this).val($.cookie("search_" + $(this).attr("id")));
                    count++;
                }
                break;
            case "select":
                if ($.cookie("search_" + $(this).attr("id")) != "null") {
                    $(this).val($.cookie("search_" + $(this).attr("id")));
                    count++;
                }
                break;
        }
    });

    if ($('table.ajax.table').length > 0) {
        drawTable(getSearchAction());
    }
    $("#filterForm").submit(function (e) {
        e.preventDefault();
        $("#filterForm button[type=button]#search").click();
    });
    $("#filterForm button#search").click(function () {
        drawTable(getSearchAction());
    });
    $("#filterForm button[type=button]#reset").click(function () {
        $("#filterForm button[type!=button], #filterForm select, #filterForm input").val("");
        $("#filterForm button[type=button]#search").click();
    });
    $("input.only-number").keydown(function (e) {
        var key = e.charCode || e.keyCode || 0;
        return (
            key == 8 ||
            key == 9 ||
            key == 13 ||
            key == 46 ||
            key == 110 ||
            key == 190 ||
            (key >= 35 && key <= 40) ||
            (key >= 48 && key <= 57) ||
            (key >= 96 && key <= 105));
    });
    $(document).delegate('button.change_status.ajax', 'click', function () {
        var url = $(this).data('url');
        var id = $(this).data('id');
        var status = $(this).data('status');
        $.ajax({
            url: url,
            type: 'POST',
            data: {id: id, status: status, _token: $("#csrf").val()},
            beforeSend: function () {
                $(".alert.alert-dismissible").remove();
                startAjaxLoader();
            },
            success: function (response) {
                if (response == 'success') {
                    successMessage("Status is changed successfully.");
                } else {
                    failMessage("Error while changing status.");
                }
                stopAjaxLoader();
            },
            complete: function () {
                stopAjaxLoader();
                drawTable(getSearchAction());
            }
        });
    });

    $(document).delegate('a.ajax.delete', "click", function (e) {
        e.preventDefault();
        var atag = $(this);
        Swal.fire({
            title: 'Are you sure to delete record?',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: `Delete`,
        }).then((result) => {
            if (result.value) {
                var url = $(atag).attr('href');
                $.ajax({
                    url: url,
                    type: "GET",
                    beforeSend: function () {
                        startAjaxLoader();
                    },
                    success: function (response) {
                        if (response == 'success') {
                            Swal.fire('Record Deleted successfully!', '', 'success')
                        } else {
                            failMessage(response);
                        }
                        stopAjaxLoader();
                    },
                    complete: function () {
                        stopAjaxLoader();
                        drawTable(getSearchAction());
                    }
                });
            }
        });
    });
    $("thead tr th > #table_select_all").change(function () {
        $("tbody tr td > input[class*=table_checkbox]").prop('checked', $(this).prop('checked'));
    });
});
