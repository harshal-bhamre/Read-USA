

$(function () {
    'use strict';

    var dtObserverTable = $('.Teacher'),
        assetPath = '../../../app-assets/',
        invoicePreview = 'app-invoice-preview.html',
        invoiceAdd = 'app-invoice-add.html',
        invoiceEdit = 'app-invoice-edit.html';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path');
        invoicePreview = assetPath + 'app/invoice/preview';
        invoiceAdd = assetPath + 'app/invoice/add';
        invoiceEdit = assetPath + 'app/invoice/edit';
    }

    // datatable
    if (dtObserverTable.length) {
        // console.log(dtObserverTable.length);
        var dtObserver = dtObserverTable.dataTable({

            ajax: location.href, // JSON file to add data
            autoWidth: false,
            columns: [
                // columns according to JSON

                    {
                       data: 'DT RowIndex',
                       name: "DT RowIndex"
                      },
                       
                    {
                         data: 'university name',
                         name: "university name" 
                     },  
                {
                    data: 'School Name',
                    name: "School Name" 
               },
               {
                data: 'User Name',
                name: "User Name" 
           },
                     {
                        data:'Tutor Registration Link',
                        name:'Tutor Registration Link'
                     },     

                    {
                        data: 'email',
                        name: "email" 
                   },
                    {
                    data: 'Phonenumber',
                    name: "phonenumber"
                },
                {
                    data: 'Enrollment',
                    name: "Enrollment"
                },
                {
                    data: 'created at',
                    name: "created at"
                },
                {
                    data: 'Action',
                    name: "Action"
                },
                   {  
                    data: 'View Tutors', 
                    name: "View Tutors" },
            
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: 'control',
                    responsivePriority: 2,
                    targets: 0
                },

                
            ],
            order: [[1, 'desc']],
            dom:
                '<"row d-flex justify-content-between align-items-center m-1"' +
                '<"col-lg-6 d-flex align-items-center"l<"dt-action-buttons text-xl-right text-lg-left text-lg-right text-left "B>>' +
                '<"col-lg-6 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap pr-lg-1 p-0"f<"invoice_status ml-sm-2">>' +
                '>t' +
                '<"d-flex justify-content-between mx-2 row"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',
            buttons: [
                {
                   
                }
            ],
            drawCallback: function () {
                $(document).find('[data-toggle="tooltip"]').tooltip();
            }
        });
    }
});
