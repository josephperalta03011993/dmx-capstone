$(document).ready(function() {
    // Use pageTitle from the page; default to empty string if undefined
    let isManagePayments = typeof pageTitle !== 'undefined' && pageTitle === "Manage Payments";
    
    $('#myTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':not(:last-child)', // Exclude the last column
                    format: isManagePayments ? {
                        body: function(data, row, column, node) {
                            // Use data-value for Manage Payments to export raw data
                            var value = $(node).attr('data-value');
                            if (value === undefined || value === 'N/A' || value === '') {
                                return 'N/A'; // Fallback for NULL/empty
                            }
                            return value;
                        }
                    } : undefined // Default for other pages
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)', // Exclude the last column
                    format: isManagePayments ? {
                        body: function(data, row, column, node) {
                            // Use data-value for Manage Payments to export raw data
                            var value = $(node).attr('data-value');
                            if (value === undefined || value === 'N/A' || value === '') {
                                return 'N/A'; // Fallback for NULL/empty
                            }
                            return value;
                        }
                    } : undefined // Default for other pages
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':not(:last-child)', // Exclude the last column
                    format: isManagePayments ? {
                        body: function(data, row, column, node) {
                            // Use data-value for Manage Payments to export raw data
                            var value = $(node).attr('data-value');
                            if (value === undefined || value === 'N/A' || value === '') {
                                return 'N/A'; // Fallback for NULL/empty
                            }
                            return value;
                        }
                    } : undefined // Default for other pages
                },
                customize: function(doc) {
                    if (isManagePayments) {
                        // Custom settings for Manage Payments
                        doc.pageSize = 'LEGAL';
                        doc.orientation = 'landscape';
                        doc.content[1].table.widths = ['15%', '8%', '8%', '8%', '10%', '10%', '15%', '16%']; // 9 columns (excl. Actions)
                        doc.styles.tableHeader.fontSize = 8;
                        doc.defaultStyle.fontSize = 6;
                        doc.pageMargins = [10, 10, 10, 10];
                        doc.content[1].layout = 'lightHorizontalLines';
                    } else {
                        // Default settings for other pages
                        doc.content[1].table.widths = 
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        doc.styles.tableBodyEven.alignment = 'center';
                        doc.styles.tableBodyOdd.alignment = 'center';
                        doc.styles.tableHeader.alignment = 'center';
                        doc.pageMargins = [20, 20, 20, 20];
                    }
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':not(:last-child)', // Exclude the last column
                    format: isManagePayments ? {
                        body: function(data, row, column, node) {
                            // Use data-value for Manage Payments to export raw data
                            var value = $(node).attr('data-value');
                            if (value === undefined || value === 'N/A' || value === '') {
                                return 'N/A'; // Fallback for NULL/empty
                            }
                            return value;
                        }
                    } : undefined // Default for other pages
                }
            }
        ]
    });
});